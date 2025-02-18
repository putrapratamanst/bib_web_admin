<?php

namespace App\Livewire\Master\Bank;

use App\Models\Bank;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|max:20|unique:banks,code')] 
    public $code = '';
 
    #[Validate('required|max:100')] 
    public $name = '';

    public function render()
    {
        return view('livewire.master.bank.create');
    }

    public function store()
    {
        $this->validate();

        Bank::create([
            'code' => $this->code,
            'name' => $this->name,
            'status' => 'active'
        ]);

        flash('Bank created successfully.', 'success');

        $this->reset();
    }
}
