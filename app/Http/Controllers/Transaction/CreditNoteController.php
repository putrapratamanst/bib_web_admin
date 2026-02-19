<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    public function index()
    {
        return view('transaction.creditnote.index');
    }

    public function create()
    {
        $currentDate = date('d-m-Y');
        $currencies = \App\Models\Currency::all();

        return view('transaction.creditnote.create', [
            'currentDate' => $currentDate,
            'currencies' => $currencies
        ]);
    }

    public function show($id)
    {
        $creditNote = \App\Models\CreditNote::findOrFail($id);

        return view('transaction.creditnote.show', [
            'creditNote' => $creditNote
        ]);
    }

    public function edit($id)
    {
        $creditNote = \App\Models\CreditNote::with(['billing.debitNote.contract'])->findOrFail($id);

        if (!$creditNote->canBeEdited()) {
            return redirect()->route('transaction.credit-notes.show', $id)
                ->with('error', 'Credit Note cannot be edited because it has been approved or rejected.');
        }

        $currencies = \App\Models\Currency::all();

        return view('transaction.creditnote.edit', [
            'creditNote' => $creditNote,
            'currencies' => $currencies,
        ]);
    }

    public function print($id)
    {
        $creditNote = \App\Models\CreditNote::with(['contract', 'contract.contact', 'currency'])->findOrFail($id);

        return view('transaction.creditnote.print', compact('creditNote'));
    }

    public function printDirectory($id)
    {
        $creditNote = \App\Models\CreditNote::with([
            'contract', 
            'contract.contact', 
            'contract.billingAddress',
            'contract.contractType',
            'currency',
            'debitNote'
        ])->findOrFail($id);

        return view('transaction.creditnote.print-directory', compact('creditNote'));
    }
}
