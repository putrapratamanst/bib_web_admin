<?php

namespace App\Livewire\Transaction\CreditNote;

use App\Models\Billing;
use App\Models\CreditNote;
use Livewire\Component;

class Create extends Component
{
    public $billing_number;
    public $date;
    public $amount;
    public $description;

    public function render()
    {
        $billings = Billing::where('status', 'unpaid')->get();

        return view('livewire.transaction.credit-note.create', [
            'billings' => $billings
        ]);
    }

    public function closeModal()
    {
        $this->reset();
        $this->dispatch('creditNote.created');
    }

    public function saveData()
    {
        $this->validate([
            'billing_number' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'description' => 'max:250',
        ], [
            'billing_number.required' => 'Billing number field is required.',
            'date.required' => 'Date field is required.',
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
        $this->amount = str_replace('.', '', $this->amount);
        $this->amount = str_replace(',', '.', $this->amount);

        $lastCreditNote = CreditNote::whereDate('date', $this->date)->orderBy('id', 'desc')->first();
        $sequenceNumber = $lastCreditNote == null ? 1 : (int) substr($lastCreditNote->number, -4) + 1;
        $sequenceNumber = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        $number = 'CN-' . $customDate . '-' . $sequenceNumber;

        $createCreditNote = CreditNote::create([
            'billing_id' => $this->billing_number,
            'number' => $number,
            'date' => $this->date,
            'amount' => $this->amount,
            'description' => $this->description,
            'status' => 'active',
        ]);

        if ($createCreditNote) {
            $this->closeModal();
            $this->dispatch('swal:toast', [
                'icon' => 'success',
                'title' => 'Credit Note created successfully',
            ]);
        } else {
            $this->dispatch('swal:toast', [
                'icon' => 'error',
                'title' => 'An error occurred. Please try again later.',
            ]);
        }
    }
}
