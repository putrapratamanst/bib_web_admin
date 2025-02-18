<?php

namespace App\Livewire\Transaction\PaymentAllocation;

use App\Models\Billing;
use App\Models\CashTransaction;
use App\Models\PaymentAllocation;
use App\Models\PaymentAllocationDetail;
use Livewire\Component;

class Create extends Component
{
    public $cash_transaction_id;
    public $number;
    public $date;
    public $amount;
    public $description;

    public $cashTransactions = [];

    public $rowDetails = [];
    public $billings = [];

    protected $rules = [
        'cash_transaction_id' => 'required',
        'number' => 'required',
        'date' => 'required',
        'amount' => 'required',
        'rowDetails.*.billing_id' => 'required',
        // amount cannot 0
        'rowDetails.*.amount' => 'required|numeric|min:1',
    ];

    // messages
    protected $messages = [
        'rowDetails.*.billing_id.required' => 'The billing field is required.',
        'rowDetails.*.amount.required' => 'The amount field is required.',
        'rowDetails.*.amount.numeric' => 'The amount must be a number.',
        'rowDetails.*.amount.min' => 'The amount must be at least 1.',
    ];

    public function mount()
    {
        $this->cashTransactions = CashTransaction::where('status', 'active')->get();
    }

    public function addRow()
    {
        $this->rowDetails[] = [
            'billing_id' => '', 
            'amount' => 0
        ];

        $this->dispatch('addRow'); 
    }

    public function removeRow($index)
    {
        // Remove a row by index
        unset($this->rowDetails[$index]);
        $this->rowDetails = array_values($this->rowDetails); // Reindex the array
    }

    public function removeAllRows()
    {
        $this->rowDetails = [];
    }

    public function updatedCashTransactionId()
    {
        $cashTransaction = CashTransaction::find($this->cash_transaction_id);
        $clientId = $cashTransaction->client_id;

        $this->billings = Billing::whereHas('contract', function($query) use ($clientId) {
            $query->where('client_id', $clientId);
        })->get();

        $this->removeAllRows();
    }

    public function render()
    {
        return view('livewire.transaction.payment-allocation.create');
    }

    public function store()
    {
        $this->validate();

        $this->date = date('Y-m-d', strtotime($this->date));

        $this->amount = str_replace('.', '', $this->amount);
        $this->amount = str_replace(',', '.', $this->amount);

        $save = PaymentAllocation::create([
            'cash_transaction_id' => $this->cash_transaction_id,
            'number' => $this->number,
            'date' => $this->date,
            'amount' => $this->amount,
            'description' => $this->description == null ? null : $this->description,
            'status' => 'confirmed',
        ]);

        if ($save) {
            $payment_allocation_id = $save->id;

            foreach ($this->rowDetails as $row) {
                $billingId = $row['billing_id'];
                $amount = $row['amount'];

                $saveDetail = PaymentAllocationDetail::create([
                    'payment_allocation_id' => $payment_allocation_id,
                    'billing_id' => $billingId,
                    'amount' => $amount,
                    'description' => null,
                ]);

                if (!$saveDetail) {
                    flash('Payment Allocation Detail failed to create.', 'danger');
                    return;
                }
            }

            flash('Payment Allocation created successfully.', 'success');
            return redirect()->route('transaction.payment-allocation.index');
        }
        else {
            flash('Payment Allocation failed to create.', 'danger');
        }
    }
}
