<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\DebitNote;
use App\Models\DebitNoteBilling;
use Illuminate\Http\Request;

class DebitNoteBillingController extends Controller
{
    public function create($id)
    {
        $debitNote = DebitNote::findOrFail($id);
        return view('transaction.debitnotebilling.create', [
            'debitNote' => $debitNote,
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'debit_note_id' => 'required|exists:debit_notes,id',
            'billing_number'   => 'required|array',
            'billing_number.*' => 'required|string|max:50|distinct|unique:debit_note_billings,billing_number',
            'date'   => 'required|array',
            'date.*' => 'required|date',
            'due_date'   => 'required|array',
            'due_date.*' => 'required|date|after_or_equal:date.*',
            'amount'   => 'required|array',
            'amount.*' => 'required|numeric|min:0',
            // kalau status juga array
            // 'status'   => 'required|array',
            // 'status.*' => 'required|in:unpaid,paid,overdue',
        ]);

        foreach ($request->billing_number as $i => $billingNumber) {
            $debitNoteBilling = new DebitNoteBilling();
            $debitNoteBilling->debit_note_id = $request->debit_note_id;
            $debitNoteBilling->billing_number = $billingNumber;
            $debitNoteBilling->date = $request->date[$i];
            $debitNoteBilling->due_date = $request->due_date[$i];
            $debitNoteBilling->amount = $request->amount[$i];
            $debitNoteBilling->status = 'paid'; // default kalau ga ada
            $debitNoteBilling->save();
        }

        return redirect()
            ->route('transaction.debit-notes.show', ['id' => $request->debit_note_id])
            ->with('success', 'Debit Note Billings created successfully.');
    }
}
