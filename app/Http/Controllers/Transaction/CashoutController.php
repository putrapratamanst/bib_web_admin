<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Cashout;
use Illuminate\Http\Request;

class CashoutController extends Controller
{
    public function index()
    {
        return view('transaction.cashouts.index');
    }

    public function show($id)
    {
        $cashout = Cashout::with([
            'debitNote.contract.contact',
            'debitNote.contract.details.insurance',
            'insurance', 
            'createdBy', 
            'updatedBy'
        ])->findOrFail($id);
        
        return view('transaction.cashouts.show', compact('cashout'));
    }

    public function markAsPaid($id)
    {
        $cashout = Cashout::findOrFail($id);
        
        $cashout->update([
            'status' => 'paid',
            'updated_by' => 1 // Todo: Replace with auth()->user()->id
        ]);

        return redirect()->back()->with('success', 'Cashout marked as paid successfully');
    }

    public function markAsCancelled($id)
    {
        $cashout = Cashout::findOrFail($id);
        
        $cashout->update([
            'status' => 'cancelled',
            'updated_by' => 1 // Todo: Replace with auth()->user()->id
        ]);

        return redirect()->back()->with('success', 'Cashout cancelled successfully');
    }
}
