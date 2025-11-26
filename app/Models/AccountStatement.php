<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * This is not a database-backed model, but a utility class for building account statement reports.
 * It collects transactions from JournalEntryDetail, CashBank, and calculates running balances.
 */
class AccountStatement extends Model
{
    /**
     * Get account statement transactions for a specific chart of account
     * 
     * @param string $chartOfAccountId
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return \Illuminate\Support\Collection
     */
    public static function getTransactions($chartOfAccountId, $dateFrom = null, $dateTo = null)
    {
        $transactions = collect();

        // Get Journal Entry Details
        $journalEntries = JournalEntryDetail::with(['journalEntry', 'chartOfAccount'])
            ->where('chart_of_account_id', $chartOfAccountId)
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereHas('journalEntry', function ($q) use ($dateFrom) {
                    $q->whereDate('entry_date', '>=', $dateFrom);
                });
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereHas('journalEntry', function ($q) use ($dateTo) {
                    $q->whereDate('entry_date', '<=', $dateTo);
                });
            })
            ->get();

        foreach ($journalEntries as $detail) {
            $transactions->push((object)[
                'date' => $detail->journalEntry->entry_date,
                'transaction_type' => 'Journal Entry',
                'reference' => $detail->journalEntry->number,
                'description' => $detail->journalEntry->description . ($detail->description ? ' - ' . $detail->description : ''),
                'debit' => $detail->debit,
                'credit' => $detail->credit,
                'source_model' => 'JournalEntryDetail',
                'source_id' => $detail->id,
            ]);
        }

        // Get Cash Bank transactions (where this account is the cash/bank account)
        $cashBanks = CashBank::with(['contact', 'chartOfAccount'])
            ->where('chart_of_account_id', $chartOfAccountId)
            ->when($dateFrom, function ($q) use ($dateFrom) {
                $q->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                $q->whereDate('date', '<=', $dateTo);
            })
            ->get();

        foreach ($cashBanks as $cashBank) {
            $debit = 0;
            $credit = 0;

            // Determine if this is debit or credit based on type
            if ($cashBank->type === 'receive') {
                $debit = $cashBank->amount;
            } else {
                $credit = $cashBank->amount;
            }

            $transactions->push((object)[
                'date' => $cashBank->date,
                'transaction_type' => $cashBank->type === 'receive' ? 'Cash Receipt' : 'Cash Payment',
                'reference' => $cashBank->number,
                'description' => $cashBank->description . ($cashBank->contact ? ' - ' . $cashBank->contact->display_name : ''),
                'debit' => $debit,
                'credit' => $credit,
                'source_model' => 'CashBank',
                'source_id' => $cashBank->id,
            ]);
        }

        // Sort by date ascending
        return $transactions->sortBy('date')->values();
    }

    /**
     * Calculate opening balance for an account as of a specific date
     * 
     * @param string $chartOfAccountId
     * @param string|null $dateFrom
     * @return float
     */
    public static function getOpeningBalance($chartOfAccountId, $dateFrom = null)
    {
        if (!$dateFrom) {
            return 0;
        }

        $balance = 0;

        // Sum journal entry details before dateFrom
        $journalBalance = JournalEntryDetail::where('chart_of_account_id', $chartOfAccountId)
            ->whereHas('journalEntry', function ($q) use ($dateFrom) {
                $q->whereDate('entry_date', '<', $dateFrom);
            })
            ->selectRaw('SUM(debit) - SUM(credit) as balance')
            ->value('balance') ?? 0;

        $balance += $journalBalance;

        // Sum cash bank transactions before dateFrom
        $cashBankBalance = CashBank::where('chart_of_account_id', $chartOfAccountId)
            ->whereDate('date', '<', $dateFrom)
            ->get()
            ->sum(function ($cb) {
                return $cb->type === 'receive' ? $cb->amount : -$cb->amount;
            });

        $balance += $cashBankBalance;

        return $balance;
    }

    /**
     * Build account statement with running balance
     * 
     * @param string $chartOfAccountId
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    public static function buildStatement($chartOfAccountId, $dateFrom = null, $dateTo = null)
    {
        $openingBalance = self::getOpeningBalance($chartOfAccountId, $dateFrom);
        $transactions = self::getTransactions($chartOfAccountId, $dateFrom, $dateTo);

        $runningBalance = $openingBalance;
        $statementLines = collect();

        foreach ($transactions as $transaction) {
            $runningBalance += ($transaction->debit - $transaction->credit);

            $statementLines->push((object)[
                'date' => $transaction->date,
                'transaction_type' => $transaction->transaction_type,
                'reference' => $transaction->reference,
                'description' => $transaction->description,
                'debit' => $transaction->debit,
                'credit' => $transaction->credit,
                'balance' => $runningBalance,
                'source_model' => $transaction->source_model,
                'source_id' => $transaction->source_id,
            ]);
        }

        return [
            'opening_balance' => $openingBalance,
            'closing_balance' => $runningBalance,
            'total_debit' => $transactions->sum('debit'),
            'total_credit' => $transactions->sum('credit'),
            'transactions' => $statementLines,
        ];
    }
}
