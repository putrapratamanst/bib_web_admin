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

    public function edit($id)
    {
        $contract = Contract::with(['details', 'contractType', 'contact'])->findOrFail($id);
        
        // Check if user is admin and contract is pending or rejected
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only admins can edit contracts.');
        }

        if (!in_array($contract->approval_status, ['pending', 'rejected'])) {
            abort(403, 'Cannot edit approved contracts.');
        }

        $contractTypes = \App\Models\ContractType::all();
        $currencies = \App\Models\Currency::all();

        return view('transaction.contract.edit', [
            'contract' => $contract,
            'contractTypes' => $contractTypes,
            'currencies' => $currencies,
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
