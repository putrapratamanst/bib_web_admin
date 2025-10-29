<?php

namespace App\Http\Controllers\Api\Report;

use App\Exports\CashoutReconciliationExport;
use App\Exports\CashoutReportExport;
use App\Http\Controllers\Controller;
use App\Models\Cashout;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CashoutReportController extends Controller
{
    public function index(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $insurance_id = $request->input('insurance_id');
        $status = $request->input('status');

        $query = Cashout::with(['debitNote.contract.contact', 'insurance'])
            // Filter by date range
            ->when($from_date && $to_date, function ($q) use ($from_date, $to_date) {
                $q->whereBetween('date', [
                    Carbon::parse($from_date)->startOfDay(),
                    Carbon::parse($to_date)->endOfDay()
                ]);
            })
            ->when($from_date && !$to_date, function ($q) use ($from_date) {
                $q->where('due_date', '>=', Carbon::parse($from_date)->startOfDay());
            })
            ->when(!$from_date && $to_date, function ($q) use ($to_date) {
                $q->where('date', '<=', Carbon::parse($to_date)->endOfDay());
            })
            // Filter by insurance company
            ->when($insurance_id, function ($q) use ($insurance_id) {
                $q->where('insurance_id', $insurance_id);
            })
            // Filter by status
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            });

            $cashouts = $query->get()->map(function ($cashout) {
            return [
                'cashout_number' => $cashout->number,
                'cashout_date' => $cashout->date_formatted,
                'due_date' => $cashout->due_date_formatted,
                'debit_note_number' => $cashout->debitNote->number,
                'contract_number' => $cashout->debitNote->contract->number,
                'client_name' => $cashout->debitNote->contract->contact->display_name,
                'insurance_name' => $cashout->insurance->display_name,
                'currency_code' => $cashout->currency_code,
                'amount' => $cashout->amount,
                'amount_formatted' => $cashout->amount_formatted,
                'status' => ucfirst($cashout->status),
                'description' => $cashout->description,
                'created_at' => $cashout->created_at->format('d M Y H:i'),
                'updated_at' => $cashout->updated_at->format('d M Y H:i'),
            ];
        });

        // Summary by status
        $summary = [
            'total_records' => $cashouts->count(),
            'total_amount' => $cashouts->sum('amount'),
            'pending_count' => $cashouts->where('status', 'Pending')->count(),
            'pending_amount' => $cashouts->where('status', 'Pending')->sum('amount'),
            'paid_count' => $cashouts->where('status', 'Paid')->count(),
            'paid_amount' => $cashouts->where('status', 'Paid')->sum('amount'),
            'cancelled_count' => $cashouts->where('status', 'Cancelled')->count(),
            'cancelled_amount' => $cashouts->where('status', 'Cancelled')->sum('amount'),
        ];

        if ($request->input('format') == "json") {
            return response()->json([
                'cashouts' => $cashouts,
                'summary' => $summary
            ]);
        } elseif ($request->input('format') == "excel") {
            return Excel::download(new CashoutReportExport($cashouts, $summary), 'Cashout_Report.xlsx');
        } else {
            return view('api.report.cashout', [
                'cashouts' => $cashouts,
                'summary' => $summary
            ]);
        }
    }

    public function reconciliation(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date') ?? now()->toDateString();
        $insurance_id = $request->input('insurance_id');
        $status = $request->input('status');

        $toDate = Carbon::parse($to_date)->endOfDay();

        $query = Cashout::with(['debitNote.contract.contact', 'insurance', 'journalEntry'])
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
            ->when($insurance_id, function ($q) use ($insurance_id) {
                $q->where('insurance_id', $insurance_id);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            });

        $cashouts = $query->get();

        // Process reconciliation data
        $reconciliation = $cashouts->map(function ($cashout) {
            $journalAmount = $cashout->journalEntry ? $cashout->journalEntry->amount : 0;
            $variance = $cashout->amount - $journalAmount;
            
            // Determine reconciliation status
            $reconciliationStatus = 'No Journal';
            if ($cashout->journalEntry) {
                if ($variance == 0) {
                    $reconciliationStatus = 'Matched';
                } elseif (abs($variance) < 0.01) { // Consider floating point precision
                    $reconciliationStatus = 'Matched';
                } else {
                    $reconciliationStatus = 'Variance';
                }
            }

            return [
                'id' => $cashout->id,
                'cashout_number' => $cashout->number,
                'cashout_date' => $cashout->date_formatted,
                'insurance_name' => $cashout->insurance->display_name,
                'debit_note_number' => $cashout->debitNote->number,
                'contract_number' => $cashout->debitNote->contract->number,
                'amount' => $cashout->amount,
                'amount_formatted' => $cashout->amount_formatted,
                'journal_entry_number' => $cashout->journalEntry->number ?? null,
                'journal_amount' => $journalAmount,
                'journal_amount_formatted' => number_format($journalAmount, 2, ",", "."),
                'variance' => $variance,
                'variance_formatted' => number_format($variance, 2, ",", "."),
                'status' => ucfirst($cashout->status),
                'reconciliation_status' => $reconciliationStatus,
            ];
        });

        // Calculate summary
        $summary = [
            'total_cashouts' => $reconciliation->count(),
            'total_cashouts_amount' => $reconciliation->sum('amount'),
            'matched_count' => $reconciliation->where('reconciliation_status', 'Matched')->count(),
            'matched_amount' => $reconciliation->where('reconciliation_status', 'Matched')->sum('amount'),
            'unmatched_count' => $reconciliation->whereIn('reconciliation_status', ['Variance', 'No Journal'])->count(),
            'unmatched_amount' => $reconciliation->whereIn('reconciliation_status', ['Variance', 'No Journal'])->sum('amount'),
            'variance_amount' => $reconciliation->sum('variance'),
            'variance_percentage' => $reconciliation->count() > 0 ? round(($reconciliation->whereIn('reconciliation_status', ['Variance', 'No Journal'])->count() / $reconciliation->count()) * 100, 2) : 0,
        ];

        if ($request->input('format') == "json") {
            return response()->json([
                'reconciliation' => $reconciliation,
                'summary' => $summary
            ]);
        } elseif ($request->input('format') == "excel") {
            return Excel::download(new CashoutReconciliationExport($reconciliation, $to_date), 'Cashout_Reconciliation.xlsx');
        } else {
            return view('api.report.cashout-reconciliation', [
                'reconciliation' => $reconciliation,
                'summary' => $summary
            ]);
        }
    }
}
