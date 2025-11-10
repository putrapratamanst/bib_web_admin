<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CashBank;
use App\Models\CashBankDetail;
use App\Models\DebitNoteBilling;
use App\Models\DebitNoteDetail;
use Illuminate\Http\Request;

class CashBankController extends Controller
{
    public function index()
    {
        return view('transaction.cashbank.index');
    }

    public function create()
    {
        $currentDate = date('d-m-Y');

        return view('transaction.cashbank.create', [
            'currentDate' => $currentDate
        ]);
    }

    public function show($id)
    {
        // Get CashBank first
        $cashBank = CashBank::with(['contact'])->findOrFail($id);

        return view('transaction.cashbank.show', [
            'cashBank' => $cashBank,
        ]);
    }
}
