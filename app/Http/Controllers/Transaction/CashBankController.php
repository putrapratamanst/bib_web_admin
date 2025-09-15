<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CashBankDetail;
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
        $cashBank = CashBankDetail::with('cashBank','debitNote')->where('cash_bank_id', $id)->first();
        return view('transaction.cashbank.show', [
            'cashBank' => $cashBank->cashBank ?? null,
            'debitNote' => $cashBank->debitNote ?? null
        ]);
    }
}
