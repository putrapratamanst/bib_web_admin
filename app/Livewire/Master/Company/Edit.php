<?php

namespace App\Livewire\Master\Company;

use App\Models\Company;
use Livewire\Component;

class Edit extends Component
{
    public $id;
    public $code;
    public $name;
    public $email;
    public $description;

    public function rules()
    {
        return [
            'code' => 'required|max:20|unique:companies,code,' . $this->id,
            'name' => 'required|max:100',
            'email' => 'required|email|max:100|unique:companies,email,' . $this->id,
            'description' => 'max:250'
        ];
    }

    public function mount($id)
    {
        $company = Company::find($id);

        $this->id = $company->id;
        $this->code = $company->code;
        $this->name = $company->name;
        $this->email = $company->email;
        $this->description = $company->description;
    }

    public function render()
    {
        return view('livewire.master.company.edit');
    }

    public function store()
    {
        $this->validate();

        $save = Company::where('id', $this->id)->update([
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'description' => $this->description == "" ? null : $this->description,
            'status' => 'active'
        ]);

        if ($save) {
            flash('Company updated successfully.', 'success');
            return redirect()->route('company.index');
        }

        flash('Company failed to create.', 'error');
    }
}
