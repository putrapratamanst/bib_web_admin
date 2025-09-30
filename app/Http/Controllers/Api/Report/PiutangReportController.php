<?php

namespace App\Http\Controllers\Api\Report;

use App\Exports\PiutangReportExport;
use App\Http\Controllers\Controller;
use App\Models\DebitNote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PiutangReportController extends Controller
{
public function index(Request $request)
{
    $as_of_date = $request->input('as_of_date') ?? now()->toDateString();
    $from_date  = $request->input('from_date');
    $to_date    = $request->input('to_date');
    $client_id  = $request->input('client_id');

    // Normalisasi tanggal dengan Carbon
    $asOf = Carbon::parse($as_of_date)->endOfDay();
    $from = $from_date ? Carbon::parse($from_date)->startOfDay() : null;
    $to   = $to_date ? Carbon::parse($to_date)->endOfDay() : null;

    $query = DebitNote::with(['creditNotes', 'paymentAllocations', 'contract'])
        // gunakan kolom 'date' pada DebitNote
        ->when($from && $to, function ($q) use ($from, $to) {
            $q->whereBetween('date', [$from->toDateString(), $to->toDateString()]);
        })
        // filter client lewat relasi contract (coba contact_id atau client_id)
        ->when($client_id, function ($q) use ($client_id) {
            $q->whereHas('contract', function ($q2) use ($client_id) {
                $q2->where(function($q3) use ($client_id) {
                    $q3->where('contact_id', $client_id)
                       ->orWhere('client_id', $client_id);
                });
            });
        });

    $debitNotes = $query->get()->map(function ($dn) use ($asOf) {
        // jumlah CN (pakai kolom 'date' pada CreditNote)
        $credit_total = $dn->creditNotes->reduce(function ($carry, $cn) use ($asOf) {
            if (empty($cn->date)) return $carry;
            try {
                if (Carbon::parse($cn->date)->lte($asOf)) {
                    return $carry + (float) $cn->amount;
                }
            } catch (\Exception $e) {
                // jika parse gagal, skip
            }
            return $carry;
        }, 0);

        // jumlah alokasi (coba transfer_date, fallback ke created_at)
        $alloc_total = $dn->paymentAllocations->reduce(function ($carry, $pa) use ($asOf) {
            $date = $pa->transfer_date ?? $pa->date ?? $pa->created_at ?? null;
            if (empty($date)) return $carry;
            try {
                if (Carbon::parse($date)->lte($asOf)) {
                    return $carry + (float) $pa->allocation;
                }
            } catch (\Exception $e) {
            }
            return $carry;
        }, 0);

        $amount = (float) $dn->amount;

        return [
            'billing_no'   => $dn->number,
            'billing_date' => $dn->date ? Carbon::parse($dn->date)->toDateString() : null,
            'amount'       => $amount,
            'credit_note'  => $credit_total,
            'allocation'   => $alloc_total,
            'outstanding'  => $amount - $credit_total - $alloc_total,
        ];
    });

    if ($request->input('format') == "json") {
        return response()->json([
            'report' => $debitNotes
        ]);
    } elseif ($request->input('format') == "excel") {
        return Excel::download(new PiutangReportExport($debitNotes), 'Report_Piutang.xlsx');
    } else {
        return view('api.report.piutang', [
            'report' => $debitNotes
        ]);
    }
}
}
