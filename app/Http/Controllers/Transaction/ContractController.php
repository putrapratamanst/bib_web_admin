<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contractTypes = \App\Models\ContractType::all();

        return view('transaction.contract.index', [
            'contractTypes' => $contractTypes,
        ]);
    }

    public function create()
    {
        $contractTypes = \App\Models\ContractType::all();
        $currencies = \App\Models\Currency::all();

        return view('transaction.contract.create', [
            'contractTypes' => $contractTypes,
            'currencies' => $currencies,
        ]);
    }

    public function show($id)
    {
        $contract = Contract::find($id);
        return view('transaction.contract.show', [
            'contract' => $contract,
        ]);
    }
    public function showAddUnit($id)
    {
        $contract = Contract::with('autoMobileUnits')->findOrFail($id);
        return view('transaction.contract.show-add-unit', [
            'contract' => $contract,
            'units' => $contract->autoMobileUnits,
        ]);
    }

    public function showAddProperty($id)
    {
        $contract = Contract::with('propertyUnits')->findOrFail($id);
        return view('transaction.contract.show-add-property', [
            'contract' => $contract,
            'units' => $contract->propertyUnits,
        ]);
    }
}
