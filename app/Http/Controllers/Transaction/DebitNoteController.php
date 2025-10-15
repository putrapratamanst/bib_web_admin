<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
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

    public function create()
    {
        return view('transaction.debitnote.create');
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
            // 'debit_note_number' => 'required|string|max:255|unique:debit_notes,debit_note_number',
            'contact_id' => 'required|exists:contacts,id',
            'contract_id' => 'required|exists:contracts,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'currency' => 'required|string|max:3',
            'exchange_rate' => 'required|numeric|min:0',
            'installment' => 'required|integer|max:12',
            'amount' => 'required|min:0',
            // 'details' => 'required|array|min:1',
            // 'details.*.item_description' => 'required|string|max:255',
            // 'details.*.amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            //generate debit note number dengan format BIB/D24/08-2384
            $newNumber = 'BIB/D' . date('y') . '/' . str_pad(DebitNote::count() + 1, 4, '0', STR_PAD_LEFT);
            $request->merge(['debit_note_number' => $newNumber]);
            // Create Debit Note
            $debitNote = DebitNote::create([
                'number' => $request->debit_note_number,
                'contact_id' => $request->contact_id,
                'contract_id' => $request->contract_id,
                'date' => $request->date,
                'due_date' => $request->due_date,
                'currency_code' => $request->currency,
                'exchange_rate' => $request->exchange_rate,
                'amount' => $request->amount,
                'description' => $request->description,
                'status' => 'active',
                'installment' => $request->installment,
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
}
