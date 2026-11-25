<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Currency;
use App\Models\DebitNoteBilling;
use App\Models\LuminousInvoice;
use App\Models\LuminousPayment;
use App\Services\LuminousReceivableService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LuminousController extends Controller
{
    public function index()
    {
        return view('luminous.index', [
            'invoices' => LuminousInvoice::latest('date')->paginate(20),
        ]);
    }

    public function receivables()
    {
        $billings = DebitNoteBilling::with(['debitNote.contact', 'debitNote.contract'])
            ->latest('date')
            ->paginate(25);

        return view('luminous.receivables.index', compact('billings'));
    }

    public function createReceivable()
    {
        $contacts = Contact::whereHas('contactTypes', fn ($query) => $query->where('type', 'client'))
            ->orderBy('display_name')
            ->get();
        $currencies = Currency::orderBy('code')->get();

        return view('luminous.receivables.create', compact('contacts', 'currencies'));
    }

    public function storeReceivable(Request $request, LuminousReceivableService $service)
    {
        $data = $request->validate([
            'contact_id' => ['required', 'uuid', Rule::exists('contacts', 'id')],
            'currency_code' => ['required', 'string', 'size:3', Rule::exists('currencies', 'code')],
            'exchange_rate' => ['required', 'numeric', 'gt:0'],
            'date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['required', 'string', 'max:500'],
        ]);

        $isClient = Contact::whereKey($data['contact_id'])
            ->whereHas('contactTypes', fn ($query) => $query->where('type', 'client'))
            ->exists();

        if (! $isClient) {
            throw ValidationException::withMessages([
                'contact_id' => 'Pilih kontak dengan tipe client.',
            ]);
        }

        $service->createManual($data, (int) auth()->id());

        return redirect()->route('luminous.receivables.index')
            ->with('success', 'Debit Note dan Billing Luminous berhasil dibuat.');
    }

    public function print(LuminousInvoice $invoice)
    {
        $invoice->load('items');

        return view('luminous.print', compact('invoice'));
    }

    public function pay(Request $request, LuminousInvoice $invoice)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($invoice, $data) {
            $locked = LuminousInvoice::lockForUpdate()->findOrFail($invoice->id);

            if (round((float) $data['amount'], 2) > round($locked->outstanding_amount, 2)) {
                throw ValidationException::withMessages(['amount' => 'Pembayaran melebihi outstanding.']);
            }

            $locked->payments()->create($data + ['created_by' => auth()->id()]);
            $locked->update(['paid_amount' => round((float) $locked->paid_amount + (float) $data['amount'], 2)]);
            $locked->refreshStatus();
        });

        return back()->with('success', 'Pembayaran Luminous berhasil dicatat.');
    }

    public function aging(Request $request)
    {
        $request->validate([
            'as_of_date' => ['nullable', 'date'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'insurance_id' => ['nullable', Rule::exists('luminous_invoices', 'insurance_id')->where('company_id', auth()->user()->company_id)],
        ]);
        $asOf = Carbon::parse($request->input('as_of_date') ?: now())->startOfDay();
        $from = $request->filled('from_date') ? Carbon::parse($request->input('from_date'))->startOfDay() : null;
        $to = $request->filled('to_date') ? Carbon::parse($request->input('to_date'))->startOfDay() : null;
        $insuranceId = $request->input('insurance_id');

        $insurances = LuminousInvoice::selectRaw('insurance_id as id, MAX(recipient) as display_name')
            ->whereNotNull('insurance_id')
            ->groupBy('insurance_id')
            ->orderBy('display_name')
            ->get();

        // Sama dengan A/R Aging existing: From & To terisi = filter rentang tanggal tagihan, selain itu s/d As Of Date.
        $rows = LuminousInvoice::with('payments')
            ->when($from && $to,
                fn ($q) => $q->whereBetween('date', [$from->toDateString(), $to->toDateString()]),
                fn ($q) => $q->whereDate('date', '<=', $asOf))
            ->when($insuranceId, fn ($q, $id) => $q->where('insurance_id', $id))
            ->orderBy('recipient')->orderBy('due_date')->get()
            ->map(function ($invoice) use ($asOf) {
                $outstanding = round((float) $invoice->amount - $this->paidAsOf($invoice, $asOf), 2);
                $days = max(0, (int) round(($asOf->timestamp - $invoice->due_date->timestamp) / 86400));
                $bucket = match (true) {
                    $days === 0 => 'current',
                    $days <= 30 => 'd30',
                    $days <= 60 => 'd60',
                    $days <= 90 => 'd90',
                    default => 'd90plus',
                };

                return compact('invoice', 'outstanding', 'days', 'bucket');
            })
            ->filter(fn ($row) => $row['outstanding'] > 0)->values();

        $totals = collect(['current', 'd30', 'd60', 'd90', 'd90plus'])
            ->mapWithKeys(fn ($b) => [$b => $rows->where('bucket', $b)->sum('outstanding')]);

        return view('luminous.aging', compact('rows', 'totals', 'asOf', 'insurances', 'insuranceId'));
    }

    // ponytail: ringkasan penagihan/penerimaan/piutang. Neraca & Laba Rugi penuh butuh COA + jurnal khusus Luminous.
    public function report(Request $request)
    {
        $request->validate(['from_date' => ['nullable', 'date'], 'to_date' => ['nullable', 'date']]);
        $to = Carbon::parse($request->query('to_date', now()))->startOfDay();
        $from = Carbon::parse($request->query('from_date', $to->copy()->startOfMonth()))->startOfDay();
        $range = [$from->toDateString(), $to->toDateString()];

        $invoices = LuminousInvoice::whereBetween('date', $range)->orderBy('date')->get();
        $payments = LuminousPayment::with('invoice')->whereBetween('date', $range)->orderBy('date')->get();
        $receivable = LuminousInvoice::with('payments')->whereDate('date', '<=', $to)->get()
            ->sum(fn ($invoice) => max(0, (float) $invoice->amount - $this->paidAsOf($invoice, $to)));

        return view('luminous.report', compact('invoices', 'payments', 'receivable', 'from', 'to'));
    }

    private function paidAsOf(LuminousInvoice $invoice, Carbon $date): float
    {
        // Tagihan lama tanpa riwayat pembayaran memakai paid_amount apa adanya.
        if ($invoice->payments->isEmpty()) {
            return (float) $invoice->paid_amount;
        }

        return (float) $invoice->payments->filter(fn ($p) => $p->date->lte($date))->sum('amount');
    }
}
