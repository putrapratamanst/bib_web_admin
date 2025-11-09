<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CashBankDetail;
use App\Models\DebitNoteBilling;
use App\Models\DebitNoteDetail;
use Illuminate\Http\Request;

class CashBankController extends Controller
{
    public function index()
    {
        return view('transaction.cashbank.index');
    }

    public function create()
    {
        $currentDate = date('d-m-Y');

        return view('transaction.cashbank.create', [
            'currentDate' => $currentDate
        ]);
    }

    public function show($id)
    {
        $cashBank = CashBankDetail::with(['cashBank', 'debitNote'])->where('cash_bank_id', $id)->first();
        $debitNoteBillings = [];
        if ($cashBank && $cashBank->debitNote) {
            $debitNoteBillings = DebitNoteBilling::with([
                'debitNote',
                'debitNote.contract',
                'paymentAllocation' => function($query) use ($cashBank) {
                    $query->where('cash_bank_id', $cashBank->cash_bank_id);
                }
            ])
                ->where('debit_note_id', $cashBank->debitNote->id)
                ->get()
                ->map(function ($billing) {
                    // Hitung total yang sudah dialokasikan untuk billing ini
                    $allocatedAmount = $billing->paymentAllocation ? $billing->paymentAllocation->sum('allocation') : 0;
                    $billing->allocated_amount = $allocatedAmount;
                    $billing->remaining_amount = $billing->amount - $allocatedAmount;
                    return $billing;
                });
        }
        return view('transaction.cashbank.show', [
            'cashBank' => $cashBank->cashBank ?? null,
            'debitNote' => $cashBank->debitNote ?? null,
            'debitNoteBillings' => $debitNoteBillings
        ]);
    }
}
