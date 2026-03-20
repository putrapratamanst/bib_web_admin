<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $contract = Contract::with(['endorsements.contractReference.contact', 'billingAddress'])->findOrFail($id);
        return view('transaction.contract.show', [
            'contract' => $contract,
        ]);
    }

    public function edit($id)
    {
        $contract = Contract::with(['details', 'endorsements.contractReference.contact', 'contractType', 'contact'])->findOrFail($id);
        
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only admins can edit contracts.');
        }

        // Lock form if approved or has issued debit notes — only policy_number can be edited
        $isFormLocked = $contract->approval_status === 'approved' || $contract->debitNotes()->exists();

        $contractTypes = \App\Models\ContractType::all();
        $currencies = \App\Models\Currency::all();

        return view('transaction.contract.edit', [
            'contract' => $contract,
            'contractTypes' => $contractTypes,
            'currencies' => $currencies,
            'isFormLocked' => $isFormLocked,
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
