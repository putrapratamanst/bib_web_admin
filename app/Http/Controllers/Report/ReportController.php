<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\ContractType;

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

    public function renewalNotice()
    {
        $contractTypes = ContractType::orderBy('name')->get();

        return view('report.renewal-notice.index', compact('contractTypes'));
    }
}