<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\PaymentAllocation;
use App\Models\CashBank;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function index()
    {
        return view('transaction.refund.index');
    }

    public function create()
    {
        $currentDate = date('d-m-Y');

        return view('transaction.refund.create', [
            'currentDate' => $currentDate
        ]);
    }

    public function show($id)
    {
        $refund = PaymentAllocation::with(['cashBank.contact', 'cashBank.chartOfAccount'])
            ->where('type', 'refund')
            ->findOrFail($id);

        return view('transaction.refund.show', [
            'refund' => $refund,
        ]);
    }
}
