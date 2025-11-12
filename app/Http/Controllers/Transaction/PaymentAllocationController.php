<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CashBank;
use App\Models\CashBankDetail;
use App\Models\Cashout;
use App\Models\DebitNoteBilling;
use App\Models\PaymentAllocation;
use App\Models\CreditNote;
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
        $cashBank = CashBank::with('contact')->where('id', $id)->first();
        // Get all debit note billings for the contact
        $debitNoteBillings = DebitNoteBilling::whereHas('debitNote', function($query) use ($cashBank) {
            $query->where('contact_id', $cashBank->contact_id);
        })
        ->with(['debitNote.contract', 'debitNote' => function($query) {
            $query->select('id', 'number', 'currency_code', 'contact_id', 'contract_id');
        }])
        ->select([
            'id',
            'debit_note_id',
            'billing_number',
            'date',
            'due_date',
            'amount',
            'status'
        ])
        ->get()
        ->map(function($billing) use ($cashBank){
            // Calculate allocated amount for this billing across ALL cash banks
            $total_allocated = PaymentAllocation::where('debit_note_billing_id', $billing->id)
                ->sum('allocation');
            // Calculate allocated amount from this cash bank specifically
            $allocated_on_this_cashbank = PaymentAllocation::where('debit_note_billing_id', $billing->id)
                ->where('cash_bank_id', $cashBank->id)
                ->sum('allocation');

            // Calculate total credit note amount applied to this billing (reduce outstanding)
            $credit_note = CreditNote::where('billing_id', $billing->id);
            $credit_note_amount = $credit_note->sum('amount');

            // Net amount after credit notes
            $net_amount = floatval($billing->amount) - floatval($credit_note_amount);

            // Expose both amounts to the view: total allocated and allocated for this cash bank
            $billing->total_allocated = $total_allocated;
            $billing->allocated_amount = $allocated_on_this_cashbank;

            // Expose credit note and net amount to the view for clarity
            $billing->credit_note_amount = $credit_note_amount;
            $billing->amount = $net_amount;

            // Remaining amount is net amount minus total allocations (across all cash banks)
            // Clamp to 0 to avoid negative values from rounding or over-allocation
            $billing->remaining_amount = max(0, round($net_amount - $total_allocated, 2));
            return $billing;
        })
        // Only include billings that still have remaining amount (i.e., not fully allocated)
        ->filter(function($billing) {
            return ($billing->remaining_amount ?? 0) > 0;
        })
        ->values();

        return view('transaction.paymentallocation.show', [
            'cashBank' => $cashBank,
            'debitNoteBillings' => $debitNoteBillings
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
                'created_by' => 1, // TODO: Replace with proper auth user ID when authentication is implemented

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
