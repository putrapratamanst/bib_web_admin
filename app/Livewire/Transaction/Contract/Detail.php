<?php

namespace App\Livewire\Transaction\Contract;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Currency;
use App\Models\Insurance;
use Livewire\Component;

class Detail extends Component
{
    public $id;
    public $number;
    public $client_id;
    public $period_start;
    public $period_end;
    public $insurance_id;
    public $count_of_item;
    public $description;
    public $currency_id;
    public $currency_rate = 1;
    public $discount = 0;
    public $gross_amount;

    public $clients = [];
    public $insurances = [];
    public $currencies = [];

    public function rules() {
        return [
            'client_id' => 'required',
            'period_start' => 'required|date|before:period_end',
            'period_end' => 'required|date|after:period_start',
            'insurance_id' => 'required',
            'count_of_item' => 'required|numeric',
            'currency_id' => 'required',
            'currency_rate' => 'required',
            'discount' => 'required',
            'gross_amount' => 'required',
        ];
    }

    public function mount($id)
    {
        $contract = Contract::find($id);
        if (!$contract) {
            flash('Contract not found.', 'danger');
            return redirect()->route('contract.index');
        }

        $this->id = $contract->id;
        $this->number = $contract->number;
        $this->client_id = $contract->client_id;
        $this->period_start = date('d-m-Y', strtotime($contract->period_start));
        $this->period_end = date('d-m-Y', strtotime($contract->period_end));
        $this->insurance_id = $contract->insurance_id;
        $this->count_of_item = $contract->count_of_item;
        $this->description = $contract->description;
        $this->currency_id = $contract->currency_id;
        $this->currency_rate = number_format($contract->currency_rate, 2, ',', '.');
        $this->discount = number_format($contract->discount, 2, ',', '.');
        $this->gross_amount = number_format($contract->gross_amount, 2, ',', '.');

        $this->clients = Client::where('status', 'active')->get();
        $this->insurances = Insurance::where('status', 'active')->get();
        $this->currencies = Currency::all();
    }

    public function render()
    {
        return view('livewire.transaction.contract.detail');
    }

    public function store()
    {
        $this->validate();

        $contract = Contract::find($this->id);
        $contract->client_id = $this->client_id;
        $contract->period_start = date('Y-m-d', strtotime($this->period_start));
        $contract->period_end = date('Y-m-d', strtotime($this->period_end));
        $contract->insurance_id = $this->insurance_id;
        $contract->count_of_item = $this->count_of_item;
        $contract->description = $this->description;
        $contract->currency_id = $this->currency_id;

        $this->currency_rate = str_replace('.', '', $this->currency_rate);
        $this->currency_rate = str_replace(',', '.', $this->currency_rate);
        $contract->currency_rate = $this->currency_rate;

        $this->discount = str_replace('.', '', $this->discount);
        $this->discount = str_replace(',', '.', $this->discount);
        $contract->discount = $this->discount;

        $this->gross_amount = str_replace('.', '', $this->gross_amount);
        $this->gross_amount = str_replace(',', '.', $this->gross_amount);
        $contract->gross_amount = $this->gross_amount;
        $contract->save();

        flash('Contract updated successfully.', 'success');
        return redirect()->route('contract.detail', $contract->id);
    }
}
