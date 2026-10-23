<?php

namespace App\Http\Controllers;

use App\Models\Cashout;
use App\Models\Contact;
use App\Models\LuminousInvoice;
use App\Models\LuminousInvoiceItem;
use App\Models\LuminousPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LuminousController extends Controller
{
    public function index()
    {
        return view('luminous.index', [
            'invoices' => LuminousInvoice::latest('date')->paginate(20),
        ]);
    }

    public function create()
    {
        $cashouts = Cashout::where('status', '!=', 'cancelled')
            ->whereNotIn('id', LuminousInvoiceItem::select('cashout_id'))
            ->with(['insurance', 'debitNote.contract.details', 'debitNote.contract.contact', 'debitNote.billingAddress'])
            ->orderBy('date')->get()
            ->each(fn ($c) => $c->setAttribute('basos', $this->basos($c)))
            ->filter(fn ($c) => $c->basos > 0)
            ->sortBy(fn ($c) => strtolower($c->insurance?->display_name ?? ''))->values();

        return view('luminous.create', compact('cashouts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cashout_ids' => ['required', 'array', 'min:1'],
            'cashout_ids.*' => ['required', 'distinct', 'exists:cashouts,id'],
            'number' => ['required', 'string', 'max:100', 'unique:luminous_invoices,number'],
            'description' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'city' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'due_date' => ['required', 'date', 'after_or_equal:date'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:255'],
            'signatory_name' => ['required', 'string', 'max:255'],
            'signatory_title' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data) {
            $cashouts = Cashout::with('debitNote.contract.details')
                ->whereIn('id', $data['cashout_ids'])
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()->get();

            if ($cashouts->count() !== count($data['cashout_ids'])
                || LuminousInvoiceItem::whereIn('cashout_id', $data['cashout_ids'])->exists()) {
                throw ValidationException::withMessages([
                    'cashout_ids' => 'Hutang Asuransi tidak valid atau sudah pernah ditagihkan.',
                ]);
            }

            // Satu surat hanya untuk satu asuransi.
            $insuranceIds = $cashouts->pluck('insurance_id')->unique();
            if ($insuranceIds->count() !== 1) {
                throw ValidationException::withMessages([
                    'cashout_ids' => 'Pilih Hutang Asuransi dari satu asuransi saja per tagihan.',
                ]);
            }
            $insuranceId = $insuranceIds->first();

            $items = $cashouts->map(fn ($c) => ['cashout_id' => $c->id, 'basos_amount' => $this->basos($c)]);
            $total = round($items->sum('basos_amount'), 2);

            if ($total <= 0) {
                throw ValidationException::withMessages(['cashout_ids' => 'Total Basos harus lebih dari 0.']);
            }

            $invoice = LuminousInvoice::create(Arr::except($data, 'cashout_ids') + [
                'insurance_id' => $insuranceId,
                'recipient' => Contact::findOrFail($insuranceId)->display_name,
                'amount' => $total,
                'created_by' => auth()->id(),
            ]);
            $invoice->items()->createMany($items->all());
        });

        return redirect()->route('luminous.invoices.index')->with('success', 'Tagihan Luminous berhasil disimpan.');
    }

    public function print(LuminousInvoice $invoice)
    {
        $invoice->load('items.cashout.debitNote.contract.contact', 'items.cashout.debitNote.billingAddress');

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
            'insurance_id' => ['nullable', 'exists:contacts,id'],
        ]);
        $asOf = Carbon::parse($request->input('as_of_date') ?: now())->startOfDay();
        $from = $request->filled('from_date') ? Carbon::parse($request->input('from_date'))->startOfDay() : null;
        $to = $request->filled('to_date') ? Carbon::parse($request->input('to_date'))->startOfDay() : null;
        $insuranceId = $request->input('insurance_id');

        $insurances = Contact::whereIn('id', LuminousInvoice::select('insurance_id'))->orderBy('display_name')->get(['id', 'display_name']);

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

    // Basos = premi * share% * eng_fee% (angka yang tampil di detail Hutang Asuransi).
    // ponytail: dibagi rata per cicilan agar tidak tertagih berulang; sesuaikan bila billing cicilan tidak rata.
    private function basos(Cashout $cashout): float
    {
        $contract = $cashout->debitNote?->contract;
        $detail = $contract?->details->firstWhere('insurance_id', $cashout->insurance_id);

        if (! $detail) {
            return 0.0;
        }

        $installments = max(1, (int) $cashout->debitNote->installment);

        return round((float) $contract->gross_premium * $detail->percentage / 100 * $detail->eng_fee / 100 / $installments, 2);
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
