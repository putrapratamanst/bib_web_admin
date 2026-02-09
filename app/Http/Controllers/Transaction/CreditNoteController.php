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

    public function print($id)
    {
        $creditNote = \App\Models\CreditNote::with(['contract', 'contract.contact', 'currency'])->findOrFail($id);

        return view('transaction.creditnote.print', compact('creditNote'));
    }
}
