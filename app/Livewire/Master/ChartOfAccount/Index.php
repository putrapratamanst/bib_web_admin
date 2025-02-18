<?php

namespace App\Livewire\Master\ChartOfAccount;

use App\Models\ChartOfAccount;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = "";

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $chartOfAccounts = ChartOfAccount::with(['accountCategory'])
            ->where('number', 'like', "$this->search%")
            ->orWhere('name', 'like', "$this->search%")
            ->orderBy('number', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master.chart-of-account.index', [
            'chartOfAccounts' => $chartOfAccounts,
        ]);
    }
}
