<?php

namespace App\Livewire\Master\Contact;

use App\Models\ChartOfAccount;
use App\Models\Contact;
use Livewire\Component;

class Edit extends Component
{
    public $id;
    public $contact_type;
    public $code;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $description;
    public $account_mapping_receivable;
    public $account_mapping_payable;

    public $chartOfAccountsReceivable = [];

    public $chartOfAccountsPayable = [];

    public function rules()
    {
        return [
            'code' => 'required|unique:contacts,code,' . $this->id,
            'contact_type' => 'required|in:client,agent,insurance',
            'name' => 'required',
            'email' => 'required|email|unique:contacts,email,' . $this->id,
            'phone' => 'required',
            'address' => 'required',
            'description' => 'max:250',
            'account_mapping_receivable' => 'required',
            'account_mapping_payable' => 'required',
        ];
    }

    public function mount($id)
    {
        $this->chartOfAccountsReceivable = ChartOfAccount::accountCategoryName('Akun Piutang')->get();
        $this->chartOfAccountsPayable = ChartOfAccount::accountCategoryName('Akun Hutang')->get();
        $contact = Contact::find($id);
        if ($contact) {
            $this->id = $contact->id;
            $this->contact_type = $contact->type->first()->type;
            $this->code = $contact->code;
            $this->name = $contact->name;
            $this->email = $contact->email;
            $this->phone = $contact->phone;
            $this->address = $contact->address;
            $this->description = $contact->description;
            $this->account_mapping_receivable = $contact->account_mapping_receivable;
            $this->account_mapping_payable = $contact->account_mapping_payable;
        }
    }

    public function render()
    {
        return view('livewire.master.contact.edit');
    }

    public function store()
    {
        $this->validate();

        $contact = Contact::find($this->id);
        if ($contact) {
            $contact->update([
                'code' => $this->code,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'description' => $this->description,
                'account_mapping_receivable' => $this->account_mapping_receivable,
                'account_mapping_payable' => $this->account_mapping_payable,
            ]);

            $contact->type()->update([
                'type' => $this->contact_type,
            ]);

            flash('Contact updated successfully.', 'success');
            return redirect()->route('contact.index');
        }

        flash('Contact not found.', 'error');
    }
}
