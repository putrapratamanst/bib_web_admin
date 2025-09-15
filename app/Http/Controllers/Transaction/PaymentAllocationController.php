<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CashBank;
use App\Models\CashBankDetail;
use App\Models\PaymentAllocation;
use Illuminate\Http\Request;

class PaymentAllocationController extends Controller
{
    public function index()
    {
        return view('transaction.paymentallocation.index');
    }

    public function create($cashBankId = null)
    {
        $currentDate = date('d-m-Y');
        $cashBank = CashBankDetail::with('cashBank','debitNote')
        ->where('cash_bank_id', $cashBankId)->get();
        $dataCashBank = CashBank::find($cashBankId);
        return view('transaction.paymentallocation.create', [
            'currentDate' => $currentDate,
            'cashBank' => $cashBank,
            'dataCashBank' => $dataCashBank
        ]);
    }

    public function show($id)
    {
        $cashBank = CashBankDetail::with('cashBank')->where('cash_bank_id', $id)->first();
        $paymentAllocation = PaymentAllocation::with('debitNote')->where('cash_bank_id', $id)->get();
        return view('transaction.paymentallocation.show', [
            'cashBank' => $cashBank->cashBank,
            'paymentAllocations' => $paymentAllocation
        ]);
    }
}
