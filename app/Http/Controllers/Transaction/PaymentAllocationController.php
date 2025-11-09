<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CashBank;
use App\Models\CashBankDetail;
use App\Models\Cashout;
use App\Models\PaymentAllocation;
use Illuminate\Http\Request;

class PaymentAllocationController extends Controller
{
    public function index()
    {
        return view('transaction.paymentallocation.index');
    }

    public function create($cashBankId = null)
    {
        $currentDate = date('d-m-Y');
        $cashBank = CashBankDetail::with(['cashBank', 'debitNote' => function($query) {
            $query->with('creditNotes'); // Load credit notes related to debit note
        }])->where('cash_bank_id', $cashBankId)->get();
        $dataCashBank = CashBank::find($cashBankId);
        return view('transaction.paymentallocation.create', [
            'currentDate' => $currentDate,
            'cashBank' => $cashBank,
            'dataCashBank' => $dataCashBank
        ]);
    }

    public function show($id)
    {
        $cashBank = CashBankDetail::with('cashBank')->where('cash_bank_id', $id)->first();
        $paymentAllocation = PaymentAllocation::with('debitNote')->where('cash_bank_id', $id)->get();
        return view('transaction.paymentallocation.show', [
            'cashBank' => $cashBank->cashBank,
            'paymentAllocations' => $paymentAllocation
        ]);
    }

    public function post(Request $request, $id)
    {
        $paymentAllocation = PaymentAllocation::find($id);
        if (!$paymentAllocation) {
            return redirect()->back()->with('error', 'Payment Allocation not found.');
        }

        if ($paymentAllocation->status === 'posted') {
            return redirect()->back()->with('error', 'Payment Allocation is already posted.');
        }
        $contracts = $paymentAllocation->debitNote->contract->details;
        foreach ($contracts as $contract) {
            Cashout::create([
                'debit_note_id' => $paymentAllocation->debit_note_id,
                'insurance_id' => $contract->insurance_id,
                'number' => $this->generateCashoutNumber(),
                'date' => $paymentAllocation->debitNote->date,
                'due_date' => $paymentAllocation->debitNote->due_date,
                'currency_code' => $paymentAllocation->debitNote->currency_code,
                'exchange_rate' => $paymentAllocation->debitNote->exchange_rate,
                'amount' => $paymentAllocation->allocation * $contract->percentage / 100,
                'description' => 'Cashout for Payment Allocation ID: ' . $paymentAllocation->id,
                'status' => 'paid',
                'created_by' => auth()->id() ?? 1,

            ]);
        }
        // Update the status to 'posted'
        $paymentAllocation->status = 'posted';
        $paymentAllocation->save();

        return redirect()->back()->with('success', 'Payment Allocation has been posted successfully.');
    }
    private function generateCashoutNumber(): string
    {
        $prefix = 'CSH';
        $date = now()->format('Ym');
        $sequence = Cashout::whereRaw('DATE_FORMAT(created_at, "%Y%m") = ?', [$date])->count() + 1;

        return "{$prefix}-{$date}-" . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
