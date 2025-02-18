<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        return view('transaction.billing.index');
    }

    public function create($contractId)
    {
        $contract = Contract::find($contractId);

        if (!$contract) {
            flash('Transaction Contract not found.', 'danger');
            return redirect()->route('transaction.contract.index');
        }

        $urlBack = route('transaction.contract.show', $contractId);
        $textBack = 'Back to Contract Detail';

        return view('transaction.billing.create', compact('contract', 'urlBack', 'textBack'));
    }
}
