<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Currency;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        return view('transaction.contract.index');
    }

    public function show($id)
    {
        $contract = Contract::with(['client', 'contractType', 'currency'])->find($id);

        if (!$contract) {
            flash('Transaction Contract not found.', 'danger');
            return redirect()->route('transaction.contract.index');
        }

        return view('transaction.contract.show', compact('contract'));
    }

    public function create()
    {
        $clients = Contact::whereHas('type', function ($query) {
            $query->where('type', 'client');
        })->orderBy('name')->get();

        $insurances = Contact::whereHas('type', function ($query) {
            $query->where('type', 'insurance');
        })->orderBy('name')->get();
        
        $currencies = Currency::orderBy('name')->get();

        $contractTypies = ContractType::orderBy('name')->get();

        return view('transaction.contract.create', compact(
            'clients', 
            'insurances', 
            'currencies', 
            'contractTypies'
        ));
    }
}
