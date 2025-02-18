<?php

namespace App\Livewire\Master\Bank;

use App\Models\Bank;
use Livewire\Component;

class Edit extends Component
{
    public $id;
    public $code;
    public $name;

    public function rules()
    {
        return [
            'code' => 'required|max:20|unique:banks,code,' . $this->id,
            'name' => 'required|max:100'
        ];
    }

    public function mount($id)
    {
        $bank = Bank::find($id);

        $this->id = $bank->id;
        $this->code = $bank->code;
        $this->name = $bank->name;
    }

    public function render()
    {
        return view('livewire.master.bank.edit');
    }

    public function store()
    {
        $this->validate();

        $update = Bank::where('id', $this->id)->update([
            'code' => $this->code,
            'name' => $this->name,
            'status' => 'active'
        ]);

        if ($update) {
            flash('Bank updated successfully.', 'success');
            return redirect()->route('bank.index');
        }

        flash('Bank failed to update.', 'error');
    }
}
