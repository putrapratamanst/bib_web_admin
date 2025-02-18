<?php

namespace App\Livewire\Transaction\Contract;

use Livewire\Component;

class Billing extends Component
{
    public $id;

    public $count_of_installment;

    public function rules() {
        return [
            'count_of_installment' => 'required|numeric',
        ];
    }

    public function messages() {
        return [
            'count_of_installment.required' => 'The count of installment field is required.',
            'count_of_installment.numeric' => 'The count of installment field must be a number.',
        ];
    }

    public function render()
    {
        return view('livewire.transaction.contract.billing');
    }

    public function generate()
    {
        $this->validate();
    }
}
