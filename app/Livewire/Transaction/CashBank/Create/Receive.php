<?php

namespace App\Livewire\Transaction\CashBank\Create;

use App\Models\CashBank;
use App\Models\ChartOfAccount;
use App\Models\Contact;
use App\Models\Currency;
use Livewire\Component;

class Receive extends Component
{
    public $number;
    public $type;
    public $chart_of_account_id;
    public $contact_id;
    public $date;
    public $reference;
    public $memo;
    public $currency_id;
    public $exchange_rate;
    public $amount;
    public $cash_bank_details = [];

    // for details
    public $rowNumber = 1;

    protected $rules = [
        'number' => 'required|max:100',
        'chart_of_account_id' => 'required',
        'contact_id' => 'required',
        'date' => 'required',
        'reference' => 'max:100',
        'memo' => 'max:160',
        'currency_id' => 'required',
        'exchange_rate' => 'required',
        'amount' => 'required',
        'cash_bank_details.*.chart_of_account_id' => 'required',
        'cash_bank_details.*.description' => 'max:160',
        'cash_bank_details.*.amount' => 'required',
    ];

    protected $messages = [
        'number.required' => 'The number cannot be empty.',
        'number.max' => 'The number may not be greater than 100 characters.',
        'chart_of_account_id.required' => 'The chart of account cannot be empty.',
        'contact_id.required' => 'The contact cannot be empty.',
        'date.required' => 'The date cannot be empty.',
        'reference.max' => 'The reference may not be greater than 100 characters.',
        'memo.max' => 'The memo may not be greater than 160 characters.',
        'currency_id.required' => 'The currency cannot be empty.',
        'exchange_rate.required' => 'The exchange rate cannot be empty.',
        'amount.required' => 'The amount cannot be empty.',
        'cash_bank_details.*.chart_of_account_id.required' => 'The chart of account cannot be empty.',
        'cash_bank_details.*.description.max' => 'The description may not be greater than 160 characters.',
        'cash_bank_details.*.amount.required' => 'The amount cannot be empty.',
    ];

    public $listChartOfAccountsCashBank = [];

    public $listContacts = [];

    public $listCurrencies = [];

    public $listChartOfAccounts = [];

    public function mount()
    {
        $this->listChartOfAccountsCashBank = ChartOfAccount::accountCategoryName('Kas Bank')->get();

        $this->listContacts = Contact::orderBy('name')->get();

        $this->listCurrencies = Currency::orderBy('name')->get();

        $this->listChartOfAccounts = ChartOfAccount::orderBy('name')->get();

        $this->cash_bank_details[] = [
            'row_number' => $this->rowNumber,
            'chart_of_account_id' => '',
            'description' => '',
            'amount' => '',
        ];

        $this->rowNumber++;
    }
    
    public function render()
    {
        return view('livewire.transaction.cash-bank.create.receive');
    }

    public function addCashBankDetail()
    {
        $this->cash_bank_details[] = [
            'row_number' => $this->rowNumber,
            'chart_of_account_id' => '',
            'description' => '',
            'amount' => '',
        ];

        $this->dispatch('addRow', id: $this->rowNumber);
        $this->rowNumber++;
    }

    public function removeDetail($selectedRow)
    {
        $index = array_search($selectedRow, array_column($this->cash_bank_details, 'row_number'));
        $this->dispatch('removeRow', id: $index);

        unset($this->cash_bank_details[$index]);
    }

    public function store()
    {
        $this->validate();

        $type = 'receive';

        // if ($this->number == "") {
        //     Receive #2024-00001
        //     $lastNumber = CashBank::whereYear('created_at', date('Y'))->orderBy('id', 'desc')->first();
        //     $sequenceNumber = $lastNumber == null ? 1 : (int) substr($lastNumber->number, -5) + 1;
        //     $sequenceNumber = str_pad($sequenceNumber, 5, '0', STR_PAD_LEFT);
        //     $this->number = 'Receive #' . date('Y') . '-' . $sequenceNumber;
        // }

        $this->date = date('Y-m-d', strtotime($this->date));

        $this->exchange_rate = str_replace('.', '', $this->exchange_rate);
        $this->exchange_rate = str_replace(',', '.', $this->exchange_rate);

        $this->amount = str_replace('.', '', $this->amount);
        $this->amount = str_replace(',', '.', $this->amount);

        $cashBank = CashBank::create([
            'number' => $this->number,
            'type' => $type,
            'chart_of_account_id' => $this->chart_of_account_id,
            'contact_id' => $this->contact_id,
            'date' => $this->date,
            'reference' => $this->reference ?? null,
            'memo' => $this->memo ?? null,
            'currency_id' => $this->currency_id,
            'exchange_rate' => $this->exchange_rate,
            'amount' => $this->amount,
        ]);

        if ($cashBank) {
            flash('Client created successfully.', 'success');
            return redirect()->route('transaction.cash-bank.index');
        }
        else {
            flash('Failed to create client.', 'danger');
        }
    }
}
