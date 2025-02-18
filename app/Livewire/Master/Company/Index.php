<?php

namespace App\Livewire\Master\Company;

use App\Models\Company;
use Livewire\Attributes\On;
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
        $companies = Company::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);

        return view('livewire.master.company.index', [
            'companies' => $companies
        ]);
    }

    #[On('company.created')]
    public function resetSearch()
    {
        flash('Company created successfully.', 'success');
        $companies = Company::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);
    }
}
