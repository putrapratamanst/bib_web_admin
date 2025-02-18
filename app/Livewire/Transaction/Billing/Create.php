<?php

namespace App\Livewire\Transaction\Billing;

use App\Models\Billing;
use App\Models\Contract;
use Livewire\Component;

class Create extends Component
{
    public $contract_number;
    public $date;
    public $due_date;
    public $amount;
    public $description;

    public function render()
    {
        $contracts = Contract::where('status', 'active')->get();

        return view('livewire.transaction.billing.create', [
            'contracts' => $contracts
        ]);
    }

    public function closeModal()
    {
        $this->reset();
        $this->dispatch('billing.created');
    }

    public function saveData()
    {
        $this->validate([
            'contract_number' => 'required',
            'date' => 'required',
            'due_date' => 'required',
            'amount' => 'required',
            'description' => 'max:250',
        ], [
            'contract_number.required' => 'Contract number field is required.',
            'date.required' => 'Date field is required.',
            'due_date.required' => 'Due date field is required.',
            'amount.required' => 'Amount field is required.',
            'description.max' => 'Description field must be less than 250 characters.',
        ]);

        $x = 0;
        // for 1 to 10000000
        for($i=0;$i<100000000;$i++) {
            $x += $i;
        }

        $customDate = date('Ymd', strtotime($this->date));
        $this->date = date('Y-m-d', strtotime($this->date));
        $this->due_date = date('Y-m-d', strtotime($this->due_date));
        $this->amount = str_replace('.', '', $this->amount);
        $this->amount = str_replace(',', '.', $this->amount);

        $lastBilling = Billing::whereDate('date', $this->date)->orderBy('id', 'desc')->first();
        $sequenceNumber = $lastBilling == null ? 1 : (int) substr($lastBilling->number, -4) + 1;
        $sequenceNumber = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        $number = 'B-' . $customDate . '-' . $sequenceNumber;

        $createBilling = Billing::create([
            'contract_id' => $this->contract_number,
            'number' => $number,
            'date' => $this->date,
            'due_date' => $this->due_date,
            'amount' => $this->amount,
            'description' => $this->description,
            'status' => 'unpaid',
        ]);

        if ($createBilling) {
            $this->closeModal();
            $this->dispatch('swal:toast', [
                'icon' => 'success',
                'title' => 'Billing created successfully',
            ]);
        } else {
            $this->dispatch('swal:toast', [
                'icon' => 'error',
                'title' => 'Failed to create billing',
            ]);
        }
    }
}
