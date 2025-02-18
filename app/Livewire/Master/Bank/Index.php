<?php

namespace App\Livewire\Master\Bank;

use App\Models\Bank;
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
        $banks = Bank::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->orderBy('code', 'asc')
            ->paginate($this->perPage);

        return view('livewire.master.bank.index', [
            'banks' => $banks
        ]);
    }
}
