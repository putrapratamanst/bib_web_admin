<?php

namespace App\Livewire\Master\ChartOfAccount;

use App\Models\AccountCategory;
use App\Models\ChartOfAccount;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|max:10|unique:chart_of_accounts,number')]
    public $number = '';

    #[Validate('required|max:100')]
    public $name = '';

    #[Validate('required')]
    public $account_category_id = '';

    #[Validate('max:250')]
    public $description = '';

    public $accountCategories = [];

    // message
    protected $messages = [
        'number.required' => 'The number cannot be empty.',
        'number.max' => 'The number may not be greater than 10 characters.',
        'number.unique' => 'The number has already been taken.',
        'name.required' => 'The name cannot be empty.',
        'name.max' => 'The name may not be greater than 100 characters.',
        'account_category_id.required' => 'The account category cannot be empty.',
        'description.max' => 'The description may not be greater than 250 characters.',
    ];

    public function mount()
    {
        $this->accountCategories = AccountCategory::get(['id', 'name']);
    }

    public function render()
    {
        return view('livewire.master.chart-of-account.create');
    }

    public function store()
    {
        $this->validate();

        $chartOfAccount = ChartOfAccount::create([
            'number' => $this->number,
            'name' => $this->name,
            'account_category_id' => $this->account_category_id,
            'description' => $this->description,
            'is_active' => true,
        ]);

        if ($chartOfAccount) {
            flash('Chart of account created successfully.', 'success');
            return redirect()->route('coa.index');
        }
        else {
            flash('Failed to create chart of account.', 'error');
        }
    }
}
