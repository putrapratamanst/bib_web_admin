<?php

namespace App\Livewire\Master\Contact;

use App\Models\Contact;
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
        $contacts = Contact::with(['type'])
            ->where('name', 'like', "%$this->search%")
            ->orWhere('email', 'like', "%$this->search%")
            ->orWhere('phone', 'like', "%$this->search%")
            ->paginate($this->perPage);

        return view('livewire.master.contact.index', [
            'contacts' => $contacts,
        ]);
    }
}
