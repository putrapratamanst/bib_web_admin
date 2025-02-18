<?php

namespace App\Livewire\Transaction\Contract;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Currency;
use App\Models\Insurance;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mockery\Matcher\Contains;

class Create extends Component
{
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

    protected $rules = [
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

    public function mount()
    {
        $this->clients = Client::where('status', 'active')->get();
        $this->insurances = Insurance::where('status', 'active')->get();
        $this->currencies = Currency::all();
    }

    public function render()
    {
        return view('livewire.transaction.contract.create');
    }

    public function store()
    {
        $this->validate();

        $lastContract = Contract::whereDate('created_at', date('Y-m-d'))->orderBy('id', 'desc')->first();
        $sequenceNumber = $lastContract == null ? 1 : (int) substr($lastContract->number, -4) + 1;
        $sequenceNumber = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        $number = 'C-' . date('Ymd') . '-' . $sequenceNumber;

        $client = Client::find($this->client_id);
        if ($client == null) {
            flash('Client not found.', 'danger');
            return;
        }

        $this->period_start = date('Y-m-d', strtotime($this->period_start));
        $this->period_end = date('Y-m-d', strtotime($this->period_end));

        $this->currency_rate = str_replace('.', '', $this->currency_rate);
        $this->currency_rate = str_replace(',', '.', $this->currency_rate);

        $this->gross_amount = str_replace('.', '', $this->gross_amount);
        $this->gross_amount = str_replace(',', '.', $this->gross_amount);

        $save = Contract::create([
            'number' => $number,
            'client_id' => $this->client_id,
            'address' => $client->address,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'insurance_id' => $this->insurance_id,
            'description' => $this->description == null ? null : $this->description,
            'count_of_item' => $this->count_of_item,
            'status' => 'active',
            'currency_id' => $this->currency_id,
            'currency_rate' => $this->currency_rate,
            'discount' => $this->discount,
            'gross_amount' => $this->gross_amount,
        ]);

        if ($save) {
            flash('Client created successfully.', 'success');
            return redirect()->route('contract.detail', $save->id);
        }
        else {
            flash('Failed to create client.', 'danger');
        }
    }
}
