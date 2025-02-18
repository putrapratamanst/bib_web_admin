<?php

namespace App\Livewire\Master\ChartOfAccount;

use App\Models\AccountCategory;
use App\Models\ChartOfAccount;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public $id;
    public $number = '';
    public $name = '';
    public $account_category_id = '';
    public $description = '';
    public $accountCategories = [];

    public function mount($id)
    {
        $this->accountCategories = AccountCategory::get(['id', 'name']);

        $chartOfAccount = ChartOfAccount::find($id);
        if ($chartOfAccount) {
            $this->id = $chartOfAccount->id;
            $this->number = $chartOfAccount->number;
            $this->name = $chartOfAccount->name;
            $this->account_category_id = $chartOfAccount->account_category_id;
            $this->description = $chartOfAccount->description;
        }
    }

    public function render()
    {
        return view('livewire.master.chart-of-account.edit');
    }

    public function store()
    {
        $this->validate([
            'number' => 'required|max:10|unique:chart_of_accounts,number,' . $this->id,
            'name' => 'required|max:100',
            'account_category_id' => 'required',
            'description' => 'max:250',
        ], [
            'number.required' => 'The number cannot be empty.',
            'number.max' => 'The number may not be greater than 10 characters.',
            'number.unique' => 'The number has already been taken.',
            'name.required' => 'The name cannot be empty.',
            'name.max' => 'The name may not be greater than 100 characters.',
            'account_category_id.required' => 'The account category cannot be empty.',
            'description.max' => 'The description may not be greater than 250 characters.',
        ]);

        $chartOfAccount = ChartOfAccount::find($this->id);
        if ($chartOfAccount) {
            $chartOfAccount->update([
                'number' => $this->number,
                'name' => $this->name,
                'account_category_id' => $this->account_category_id,
                'description' => $this->description,
            ]);

            flash('Chart of account updated successfully.', 'success');
            return redirect()->route('coa.index');
        }

        flash('Failed to update chart of account.', 'error');
    }
}
