<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\DebitNoteReportExport;
use App\Exports\AccountStatementExport;
use App\Models\DebitNote;
use App\Models\AccountStatement;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function debitNotes(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $contactId = $request->get('contact_id');
        $status = $request->get('status');
        $currencyCode = $request->get('currency_code');
        $isPosted = $request->get('is_posted');
        $format = $request->get('format', 'json');

        // Build query
        $query = DebitNote::with(['contact', 'contract', 'creditNotes', 'paymentAllocations'])
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('date', '<=', $dateTo);
            })
            ->when($contactId, function ($q) use ($contactId) {
                $q->where('contact_id', $contactId);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($currencyCode, function ($q) use ($currencyCode) {
                $q->where('currency_code', $currencyCode);
            })
            ->when($isPosted !== null && $isPosted !== '', function ($q) use ($isPosted) {
                $q->where('is_posted', $isPosted);
            })
            ->orderBy('date', 'desc')
            ->orderBy('number', 'desc');

        if ($format === 'excel') {
            // Generate filename with timestamp
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $filename = "debit_note_report_{$timestamp}.xlsx";

            // Export to Excel
            return Excel::download(
                new DebitNoteReportExport($dateFrom, $dateTo, $contactId, $status, $currencyCode, $isPosted),
                $filename
            );
        }

        // Return JSON data
        $debitNotes = $query->paginate($request->get('per_page', 50));

        // Calculate totals
        $totalsQuery = DebitNote::query()
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('date', '<=', $dateTo);
            })
            ->when($contactId, function ($q) use ($contactId) {
                $q->where('contact_id', $contactId);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($currencyCode, function ($q) use ($currencyCode) {
                $q->where('currency_code', $currencyCode);
            })
            ->when($isPosted !== null && $isPosted !== '', function ($q) use ($isPosted) {
                $q->where('is_posted', $isPosted);
            });

        $totals = [
            'total_records' => $totalsQuery->count(),
            'total_amount_idr' => $totalsQuery->where('currency_code', 'IDR')->sum('amount'),
            'total_amount_usd' => $totalsQuery->where('currency_code', 'USD')->sum('amount'),
            'total_posted' => $totalsQuery->where('is_posted', true)->count(),
            'total_unposted' => $totalsQuery->where('is_posted', false)->count(),
        ];

        // Transform data for JSON response
        $transformedData = $debitNotes->map(function ($debitNote) {
            // Calculate outstanding amount
            $creditNotesAmount = $debitNote->creditNotes->sum('amount');
            $paymentAllocationsAmount = $debitNote->paymentAllocations->sum('amount');
            $outstandingAmount = $debitNote->amount - $creditNotesAmount - $paymentAllocationsAmount;

            // Convert to IDR if currency is not IDR
            $amountInIdr = $debitNote->currency_code === 'IDR'
                ? $debitNote->amount
                : $debitNote->amount * $debitNote->exchange_rate;

            return [
                'id' => $debitNote->id,
                'number' => $debitNote->number,
                'contract_number' => $debitNote->contract ? $debitNote->contract->number : null,
                'contact_name' => $debitNote->contact ? $debitNote->contact->display_name : null,
                'date' => $debitNote->date,
                'date_formatted' => $debitNote->date ? Carbon::parse($debitNote->date)->format('d/m/Y') : null,
                'due_date' => $debitNote->due_date,
                'due_date_formatted' => $debitNote->due_date ? Carbon::parse($debitNote->due_date)->format('d/m/Y') : null,
                'currency_code' => $debitNote->currency_code,
                'exchange_rate' => $debitNote->exchange_rate,
                'exchange_rate_formatted' => number_format($debitNote->exchange_rate, 2, ',', '.'),
                'amount' => $debitNote->amount,
                'amount_formatted' => number_format($debitNote->amount, 2, ',', '.'),
                'amount_idr' => $amountInIdr,
                'amount_idr_formatted' => number_format($amountInIdr, 2, ',', '.'),
                'installment' => $debitNote->installment,
                'status' => $debitNote->status,
                'is_posted' => $debitNote->is_posted,
                'outstanding_amount' => $outstandingAmount,
                'outstanding_amount_formatted' => number_format($outstandingAmount, 2, ',', '.'),
                'credit_notes_amount' => $creditNotesAmount,
                'credit_notes_amount_formatted' => number_format($creditNotesAmount, 2, ',', '.'),
                'payment_allocations_amount' => $paymentAllocationsAmount,
                'payment_allocations_amount_formatted' => number_format($paymentAllocationsAmount, 2, ',', '.'),
                'created_at' => $debitNote->created_at,
                'created_at_formatted' => $debitNote->created_at ? $debitNote->created_at->format('d/m/Y H:i') : null,
            ];
        });

        return response()->json([
            'data' => $transformedData,
            'meta' => [
                'current_page' => $debitNotes->currentPage(),
                'from' => $debitNotes->firstItem(),
                'last_page' => $debitNotes->lastPage(),
                'per_page' => $debitNotes->perPage(),
                'to' => $debitNotes->lastItem(),
                'total' => $debitNotes->total(),
            ],
            'totals' => $totals,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'contact_id' => $contactId,
                'status' => $status,
                'currency_code' => $currencyCode,
                'is_posted' => $isPosted,
            ]
        ]);
    }

    public function accountStatement(Request $request)
    {
        $chartOfAccountId = $request->get('chart_of_account_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $format = $request->get('format', 'json');

        if (!$chartOfAccountId) {
            return response()->json([
                'error' => 'chart_of_account_id is required'
            ], 400);
        }

        if ($format === 'excel') {
            // Generate filename with timestamp
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $filename = "account_statement_{$timestamp}.xlsx";

            // Export to Excel
            return Excel::download(
                new AccountStatementExport($chartOfAccountId, $dateFrom, $dateTo),
                $filename
            );
        }

        // Return JSON data
        $statement = AccountStatement::buildStatement($chartOfAccountId, $dateFrom, $dateTo);

        // Transform transactions for JSON response
        $transformedTransactions = $statement['transactions']->map(function ($transaction) {
            return [
                'date' => $transaction->date,
                'date_formatted' => Carbon::parse($transaction->date)->format('d/m/Y'),
                'transaction_type' => $transaction->transaction_type,
                'reference' => $transaction->reference,
                'description' => $transaction->description,
                'debit' => $transaction->debit,
                'debit_formatted' => number_format($transaction->debit, 2, ',', '.'),
                'credit' => $transaction->credit,
                'credit_formatted' => number_format($transaction->credit, 2, ',', '.'),
                'balance' => $transaction->balance,
                'balance_formatted' => number_format($transaction->balance, 2, ',', '.'),
            ];
        });

        return response()->json([
            'data' => $transformedTransactions,
            'summary' => [
                'opening_balance' => $statement['opening_balance'],
                'opening_balance_formatted' => number_format($statement['opening_balance'], 2, ',', '.'),
                'closing_balance' => $statement['closing_balance'],
                'closing_balance_formatted' => number_format($statement['closing_balance'], 2, ',', '.'),
                'total_debit' => $statement['total_debit'],
                'total_debit_formatted' => number_format($statement['total_debit'], 2, ',', '.'),
                'total_credit' => $statement['total_credit'],
                'total_credit_formatted' => number_format($statement['total_credit'], 2, ',', '.'),
            ],
            'filters' => [
                'chart_of_account_id' => $chartOfAccountId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ]
        ]);
    }
}
