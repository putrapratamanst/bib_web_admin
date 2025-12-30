<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index()
    {
        return view('transaction.journalentry.index');
    }

    public function create()
    {
        return view('transaction.journalentry.create');
    }

    public function show($id)
    {
        $journalEntry = JournalEntry::with(['details.chartOfAccount'])->findOrFail($id);
        
        return view('transaction.journalentry.show', compact('journalEntry'));
    }

    public function print($id)
    {
        $journalEntry = JournalEntry::with(['details.chartOfAccount'])->findOrFail($id);
        
        return view('transaction.journalentry.print', compact('journalEntry'));
    }
}
