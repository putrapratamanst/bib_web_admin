<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractType;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $contractTypes = ContractType::orderBy('name')->get();
        $contracts = Contract::with(['contractType', 'contact', 'debitNotes'])
            ->where('approval_status', 'pending')
            ->when($request->filled('contract_type_id'), function ($query) use ($request) {
                $query->where('contract_type_id', $request->input('contract_type_id'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('approval.index', compact('contracts', 'contractTypes'));
    }

    public function show(Contract $contract)
    {
        abort_unless($contract->approval_status === 'pending', 404);

        $contract->load([
            'contractType',
            'contact',
            'billingAddress',
            'details.insurance',
            'endorsements.contractReference.contact',
            'debitNotes.debitNoteBillings',
        ]);

        return view('approval.show', compact('contract'));
    }
}
