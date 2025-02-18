<?php

namespace App\Livewire\Master\Contact;

use App\Models\ChartOfAccount;
use App\Models\Contact;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|max:20|unique:contacts,code')] 
    public $code = '';

    // required and must in client, agent, insurance
    #[Validate('required|in:client,agent,insurance')]
    public $contact_type = '';
 
    #[Validate('required|max:100')] 
    public $name = '';

    #[Validate('required|max:100|email|unique:contacts,email')]
    public $email = '';

    #[Validate('required|max:20')]
    public $phone = '';

    #[Validate('required|max:250')]
    public $address = '';

    #[Validate('max:250')]
    public $description = '';

    #[Validate('required')]
    public $account_mapping_receivable = '';

    #[Validate('required')]
    public $account_mapping_payable = '';

    public $chartOfAccountsReceivable = [];

    public $chartOfAccountsPayable = [];

    public function mount()
    {
        $this->chartOfAccountsReceivable = ChartOfAccount::accountCategoryName('Akun Piutang')->get();

        $this->chartOfAccountsPayable = ChartOfAccount::accountCategoryName('Akun Hutang')->get();
    }

    public function render()
    {
        return view('livewire.master.contact.create');
    }

    public function store()
    {
        $this->validate();

        $contact = Contact::create([
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'account_mapping_receivable' => $this->account_mapping_receivable,
            'account_mapping_payable' => $this->account_mapping_payable,
            'address' => $this->address,
            'description' => $this->description == null ? '' : $this->description,
            'status' => 'active',
        ]);

        if ($contact) {
            $contact->type()->create([
                'type' => $this->contact_type,
            ]);
            flash('Contact created successfully.', 'success');
            
            return redirect()->route('contact.index');
        }
        else {
            flash('Failed to create contact.', 'error');
        }
    }
}
