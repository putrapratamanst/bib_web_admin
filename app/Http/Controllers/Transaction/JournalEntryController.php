<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
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
}
