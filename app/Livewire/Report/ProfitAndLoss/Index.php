<?php

namespace App\Livewire\Report\ProfitAndLoss;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Livewire\Component;
use App\Models\ProfitAndLoss;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Chart\Chart;

class Index extends Component
{
    public $data;

    public function mount()
    {
        $this->data = ProfitAndLoss::getData();
    }

    public function render()
    {
        return view('livewire.report.profitandloss.index', ['data' => $this->data]);
    }
}
