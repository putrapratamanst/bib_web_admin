<?php

namespace App\Livewire\Transaction\Contract;

use App\Models\Contract;
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
        $contracts = Contract::with(['client', 'insurance'])
            ->where('number', 'like', '%' . $this->search . '%')
            ->orWhereHas('client', function ($query) {
                $query->where('name', 'like', "%$this->search%");
            })
            ->orWhereHas('insurance', function ($query) {
                $query->where('name', 'like', "%$this->search%");
            })
            ->orderBy('number', 'desc')
            ->paginate($this->perPage);

        return view('livewire.transaction.contract.index', [
            'contracts' => $contracts
        ]);
    }
}
