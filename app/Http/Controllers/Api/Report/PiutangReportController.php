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

        $asOf = Carbon::parse($as_of_date)->endOfDay();

        $query = DebitNote::with(['creditNotes', 'paymentAllocations', 'contract'])
            // filter range billing date
            ->when($from_date && $to_date, function ($q) use ($from_date, $to_date) {
                $q->whereBetween('date', [
                    Carbon::parse($from_date)->startOfDay(),
                    Carbon::parse($to_date)->endOfDay()
                ]);
            })
            ->when($from_date && !$to_date, function ($q) use ($from_date) {
                $q->where('date', '>=', Carbon::parse($from_date)->startOfDay());
            })
            ->when(!$from_date && $to_date, function ($q) use ($to_date) {
                $q->where('date', '<=', Carbon::parse($to_date)->endOfDay());
            })
            // filter by client (via contract relasi)
            ->when($client_id, function ($q) use ($client_id) {
                $q->whereHas('contract', function ($q2) use ($client_id) {
                    $q2->where('client_id', $client_id)
                        ->orWhere('contact_id', $client_id);
                });
            });

        $debitNotes = $query->get()->map(function ($dn) use ($asOf) {
            // Total Credit Notes sampai as_of_date
            $credit_total = $dn->creditNotes->reduce(function ($carry, $cn) use ($asOf) {
                if ($cn->date && Carbon::parse($cn->date)->lte($asOf)) {
                    return $carry + (float) $cn->amount;
                }
                return $carry;
            }, 0);

            // Total Allocation sampai as_of_date
            $alloc_total = $dn->paymentAllocations->reduce(function ($carry, $pa) use ($asOf) {
                $date = $pa->transfer_date ?? $pa->date ?? $pa->created_at;
                if ($date && Carbon::parse($date)->lte($asOf)) {
                    return $carry + (float) $pa->allocation;
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
