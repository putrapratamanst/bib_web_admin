<?php

namespace App\Livewire\Transaction\CreditNote;

use App\Models\CreditNote;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public $perPage = 10;

    public $search = "";
    
    public function render()
    {
        $creditNotes = CreditNote::with(['billing'])
            ->where(function ($query) {
                $query->where('number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('billing', function ($query) {
                        $query->where('number', 'like', '%' . $this->search . '%');
                    });
            })
            ->paginate($this->perPage);

        return view('livewire.transaction.credit-note.index', [
            'creditNotes' => $creditNotes
        ]);
    }

    #[On('creditNote.created')]
    public function refreshBilling()
    {
        // $this->resetPage();
    }
}
