<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\CashTransaction;
use App\Models\Client;
use App\Models\Currency;
use Illuminate\Http\Request;

class CashTransactionController extends Controller
{
    public function index()
    {
        return view('transaction.cash-transaction.index');
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $currencies = Currency::orderBy('name')->get();
        $banks = Bank::orderBy('name')->get();

        return view('transaction.cash-transaction.create', compact('clients', 'currencies', 'banks'));
    }

    public function show($id)
    {
        $ct = CashTransaction::with(['client', 'bank', 'currency'])->find($id);
        if (!$ct) {
            flash('Transaction not found.', 'danger');
            return redirect()->route('transaction.cash-transaction.index');
        }

        return view('transaction.cash-transaction.show', compact('ct'));
    }
}
