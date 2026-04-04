<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\DebitNote;
use App\Models\DebitNoteDetail;
use App\Models\Contact;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DebitNoteController extends Controller
{
    public function index()
    {
        return view('transaction.debitnote.index');
    }

    public function show($id)
    {
        $debitNote = DebitNote::with(['debitNoteDetails', 'debitNoteBillings'])
            ->findOrFail($id);
        
        return view('transaction.debitnote.show', [
            'debitNote' => $debitNote,
        ]);
    }

    public function printDirectory($id)
    {
        $debitNote = DebitNote::with([
            'contract', 
            'contract.contact', 
            'contract.billingAddress',
            'contract.contractType',
            'currency',
            'debitNoteBillings' => function ($query) {
                $query->orderBy('due_date')->orderBy('created_at');
            }
        ])->findOrFail($id);

        return view('transaction.debitnote.print-directory', compact('debitNote'));
    }

    public function create()
    {
        $currencies = Currency::orderBy('code')->get();

        return view('transaction.debitnote.create', [
            'currencies' => $currencies,
        ]);
    }

    public function store(Request $request)
    {
        // Clean up comma-separated numbers
        $cleanedData = $request->all();
        if (isset($cleanedData['exchange_rate'])) {
            $cleanedData['exchange_rate'] = str_replace(',', '', $cleanedData['exchange_rate']);
        }
        if (isset($cleanedData['amount'])) {
            $cleanedData['amount'] = str_replace(',', '', $cleanedData['amount']);
        }
        if (isset($cleanedData['gross_premium'])) {
            $cleanedData['gross_premium'] = str_replace(',', '', $cleanedData['gross_premium']);
        }
        if (isset($cleanedData['stamp_fee'])) {
            $cleanedData['stamp_fee'] = str_replace(',', '', $cleanedData['stamp_fee']);
        }

        // Replace request data with cleaned data
        $request->merge($cleanedData);
        // Validation


        $validator = Validator::make($request->all(), [
            'number' => 'required|string|max:255|unique:debit_notes,number',
            'contract_id' => 'required|exists:contracts,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            // 'details' => 'required|array|min:1',
            // 'details.*.item_description' => 'required|string|max:255',
            // 'details.*.amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contract = Contract::select('id', 'contact_id', 'currency_code', 'exchange_rate', 'amount', 'installment_count')->findOrFail($request->contract_id);

        if (blank($contract->currency_code)) {
            return redirect()->back()
                ->with('error', 'Selected contract does not have a currency.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create Debit Note
            $debitNote = DebitNote::create([
                'number' => $request->number,
                'contact_id' => $contract->contact_id,
                'contract_id' => $request->contract_id,
                'date' => $request->date,
                'due_date' => $request->due_date,
                'currency_code' => $contract->currency_code,
                'exchange_rate' => $contract->exchange_rate ?? 1,
                'amount' => $contract->amount ?? 0,
                'description' => $request->description,
                'status' => 'active',
                'approval_status' => 'pending', // Default pending status
                'installment' => $contract->installment_count ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Debit Note Details
            if (isset($request->details) && is_array($request->details)) {
                foreach ($request->details as $detail) {
                    DebitNoteDetail::create([
                        'debit_note_id' => $debitNote->id,
                        'item_description' => $detail['item_description'],
                        'amount' => $detail['amount'],
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('transaction.debit-notes.index')
                ->with('success', 'Debit Note created successfully.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Failed to create Debit Note: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $debitNote = DebitNote::with(['contract', 'billingAddress', 'debitNoteDetails'])->findOrFail($id);
        $currencies = Currency::orderBy('code')->get();

        // Check if the debit note can be edited
        if (!$debitNote->canBeEdited()) {
            return redirect()->route('transaction.debit-notes.show', $id)
                ->with('error', 'Debit Note cannot be edited because it has been submitted for approval or is already approved/rejected.');
        }

        return view('transaction.debitnote.edit', [
            'debitNote' => $debitNote,
            'currencies' => $currencies,
        ]);
    }

    public function update(Request $request, $id)
    {
        $debitNote = DebitNote::findOrFail($id);
        
        // Check if the debit note can be edited
        if (!$debitNote->canBeEdited()) {
            return redirect()->route('transaction.debit-notes.show', $id)
                ->with('error', 'Debit Note cannot be edited because it has been submitted for approval or is already approved/rejected.');
        }

        // Clean up comma-separated numbers
        $cleanedData = $request->all();
        if (isset($cleanedData['exchange_rate'])) {
            $cleanedData['exchange_rate'] = str_replace(',', '', $cleanedData['exchange_rate']);
        }
        if (isset($cleanedData['amount'])) {
            $cleanedData['amount'] = str_replace(',', '', $cleanedData['amount']);
        }

        // Replace request data with cleaned data
        $request->merge($cleanedData);

        // Validation
        $validator = Validator::make($request->all(), [
            'number' => 'required|string|max:255|unique:debit_notes,number,' . $id,
            'contract_id' => 'required|exists:contracts,id',
            'billing_address_id' => 'required|exists:billing_addresses,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contract = Contract::select('id', 'contact_id', 'currency_code', 'exchange_rate', 'amount', 'installment_count')->findOrFail($request->contract_id);

        if (blank($contract->currency_code)) {
            return redirect()->back()
                ->with('error', 'Selected contract does not have a currency.')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Prepare update data
            $updateData = [
                'number' => $request->number,
                'contact_id' => $contract->contact_id,
                'contract_id' => $request->contract_id,
                'billing_address_id' => $request->billing_address_id,
                'date' => $request->date,
                'due_date' => $request->due_date,
                'currency_code' => $contract->currency_code,
                'exchange_rate' => $contract->exchange_rate ?? 1,
                'amount' => $contract->amount ?? 0,
                'description' => $request->description,
                'installment' => $contract->installment_count ?? 0,
                'updated_by' => auth()->id(),
            ];

            // If debit note was rejected, reset approval status to pending
            if ($debitNote->approval_status === 'rejected') {
                $updateData['approval_status'] = 'pending';
                $updateData['approved_by'] = null;
                $updateData['approved_at'] = null;
                $updateData['approval_notes'] = null;
            }

            // Update Debit Note
            $debitNote->update($updateData);

            // Update Debit Note Details if provided
            if (isset($request->details) && is_array($request->details)) {
                // Delete existing details
                $debitNote->debitNoteDetails()->delete();
                
                // Create new details
                foreach ($request->details as $detail) {
                    DebitNoteDetail::create([
                        'debit_note_id' => $debitNote->id,
                        'item_description' => $detail['item_description'],
                        'amount' => $detail['amount'],
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('transaction.debit-notes.show', $debitNote->id)
                ->with('success', 'Debit Note updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Failed to update Debit Note: ' . $e->getMessage())
                ->withInput();
        }
    }
}
