<?php

namespace App\Livewire\Transaction\Billing;

use App\Models\Billing;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = "";

    public $status = "unpaid";

    public function render()
    {
        // for comment above, add search feature where like number and contact number
        $billings = Billing::with(['contract'])
            ->where('status', $this->status)
            ->where(function ($query) {
                $query->where('number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('contract', function ($query) {
                        $query->where('number', 'like', '%' . $this->search . '%');
                    });
            })
            ->paginate($this->perPage);

        return view('livewire.transaction.billing.index', [
            'billings' => $billings
        ]);
    }

    #[On('billing.created')]
    public function refreshBilling()
    {
        $this->resetPage();
    }
}
