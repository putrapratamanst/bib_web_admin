<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        return view('master.chart-of-account.index');
    }

    public function create()
    {
        return view('master.chart-of-account.create');
    }

    public function edit($id)
    {
        $chartOfAccount = ChartOfAccount::with('accountCategory')->findOrFail($id);
        return view('master.chart-of-account.edit', compact('chartOfAccount'));
    }
}
