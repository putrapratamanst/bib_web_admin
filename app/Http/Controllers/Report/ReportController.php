<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function debitNotes()
    {
        return view('report.debit-notes');
    }

    public function cashouts()
    {
        return view('report.cashouts');
    }
}