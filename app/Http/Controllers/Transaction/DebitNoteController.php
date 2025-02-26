<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\DebitNote;
use Illuminate\Http\Request;

class DebitNoteController extends Controller
{
    public function index()
    {
        return view('transaction.debitnote.index');
    }

    public function show($id)
    {
        $debitNote = DebitNote::find($id);

        return view('transaction.debitnote.show', [
            'debitNote' => $debitNote,
        ]);
    }
}
