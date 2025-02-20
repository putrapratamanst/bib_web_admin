<?php 
namespace App\Livewire\Report\CashFlow;

use Livewire\Component;
use App\Models\CashFlow;

class Index extends Component
{
    public $data;

    public function mount()
    {
        $this->data = CashFlow::getData();
        array_shift($this->data);
    }

    public function render()
    {
        return view('livewire.report.cashflow.index', ['data' => $this->data]);
    }
}
