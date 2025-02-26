<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
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
}
