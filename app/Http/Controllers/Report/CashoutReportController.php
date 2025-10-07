<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CashoutReportController extends Controller
{
    public function index()
    {
        return view('report.cashout.index');
    }

    public function reconciliation()
    {
        return view('report.cashout-reconciliation.index');
    }
}
