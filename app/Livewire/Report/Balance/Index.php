<?php 
namespace App\Livewire\Report\Balance;

use Livewire\Component;
use App\Models\Balance;

class Index extends Component
{
    public $data;

    public function mount()
    {
        $this->data = Balance::getData();
        array_shift($this->data);
    }

    public function render()
    {
        return view('livewire.report.balance.index', ['data' => $this->data]);
    }
}
