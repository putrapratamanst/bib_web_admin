<?php

namespace App\Livewire\Transaction\PaymentAllocation;

use App\Models\PaymentAllocation;
use Livewire\Component;

class Index extends Component
{
    public $perPage = 10;

    public $search = "";

    public function render()
    {
        $paymentAllocations = PaymentAllocation::with(['cashTransaction'])
            ->where(function ($query) {
                $query->where('number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('cashTransaction', function ($query) {
                        $query->where('number', 'like', '%' . $this->search . '%');
                    });
            })
            ->paginate($this->perPage);

        return view('livewire.transaction.payment-allocation.index', [
            'paymentAllocations' => $paymentAllocations
        ]);
    }
}
