<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\PaymentAllocation;
use App\Models\CashBank;
use Illuminate\Http\Request;

class AdvanceController extends Controller
{
    public function index()
    {
        return view('transaction.advance.index');
    }

    public function create()
    {
        $currentDate = date('d-m-Y');

        return view('transaction.advance.create', [
            'currentDate' => $currentDate
        ]);
    }

    public function show($id)
    {
        $advance = PaymentAllocation::with(['cashBank.contact', 'cashBank.chartOfAccount'])
            ->where('type', 'advance')
            ->findOrFail($id);

        return view('transaction.advance.show', [
            'advance' => $advance,
        ]);
    }
}
