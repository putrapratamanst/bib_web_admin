<?php

namespace App\Livewire\Master\Company;

use App\Models\Company;
use Livewire\Attributes\Validate;
use LivewireUI\Modal\ModalComponent;

class Create extends ModalComponent
{
    #[Validate('required|max:20|unique:companies,code')] 
    public $code = '';

    #[Validate('required|max:100')] 
    public $name = '';

    #[Validate('required|email|max:100|unique:companies,email')]
    public $email = '';

    #[Validate('max:250')]
    public $description = '';

    public function render()
    {
        return view('livewire.master.company.create');
    }

    public function store()
    {
        $this->validate();

        Company::create([
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'description' => $this->description == "" ? null : $this->description,
            'status' => 'active'
        ]);
        
        $this->reset();
        $this->dispatch('company.created');
        $this->closeModal();
    }

    public static function modalMaxWidth(): string
    {
        return 'lg';
    }
}
