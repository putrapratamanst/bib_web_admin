<?php 
namespace App\Livewire\Report\ProfitAndLoss;

use Livewire\Component;
use App\Models\ProfitAndLoss;

class Index extends Component
{
    public $data;

    public function mount()
    {
        $this->data = ProfitAndLoss::getData();
        array_shift($this->data);
    }

    public function render()
    {
        return view('livewire.report.profitandloss.index', ['data' => $this->data]);
    }
}
