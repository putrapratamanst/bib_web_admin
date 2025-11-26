<?php

namespace App\Livewire\Report;

use App\Models\Cashout;
use App\Models\Contact;
use App\Models\ContractType;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class CashoutReport extends Component
{
    use WithPagination;

    public $date_from;
    public $date_to;
    public $insurance_id = '';
    public $contract_type_id = '';
    public $status = '';

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Set default dates to empty
        $this->date_from = null;
        $this->date_to = null;
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingInsuranceId()
    {
        $this->resetPage();
    }

    public function updatingContractTypeId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Cashout::with(['debitNote.contract.contact', 'debitNote.contract.contractType', 'insurance'])
            ->when($this->date_from, function ($q) {
                $q->whereDate('date', '>=', $this->date_from);
            })
            ->when($this->date_to, function ($q) {
                $q->whereDate('date', '<=', $this->date_to);
            })
            ->when($this->insurance_id, function ($q) {
                $q->where('insurance_id', $this->insurance_id);
            })
            ->when($this->contract_type_id, function ($q) {
                $q->whereHas('debitNote.contract', function ($q) {
                    $q->where('contract_type_id', $this->contract_type_id);
                });
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->orderBy('date', 'desc')
            ->orderBy('number', 'desc');

        $cashouts = $query->paginate(50);

        // Calculate totals
        $totals = $this->calculateTotals();

        // Get insurance companies for filter dropdown
        $insurances = Contact::whereHas('contactTypes', function ($q) {
                $q->where('type', 'insurance');
            })
            ->orderBy('display_name')
            ->get();

        // Get contract types for filter dropdown
        $contractTypes = ContractType::orderBy('name')->get();

        return view('livewire.report.cashout-report', [
            'cashouts' => $cashouts,
            'totals' => $totals,
            'insurances' => $insurances,
            'contractTypes' => $contractTypes
        ]);
    }

    private function calculateTotals()
    {
        $query = Cashout::with(['debitNote.contract'])
            ->when($this->date_from, function ($q) {
                $q->whereDate('date', '>=', $this->date_from);
            })
            ->when($this->date_to, function ($q) {
                $q->whereDate('date', '<=', $this->date_to);
            })
            ->when($this->insurance_id, function ($q) {
                $q->where('insurance_id', $this->insurance_id);
            })
            ->when($this->contract_type_id, function ($q) {
                $q->whereHas('debitNote.contract', function ($q) {
                    $q->where('contract_type_id', $this->contract_type_id);
                });
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            });

        $cashouts = $query->get();

        return [
            'total_records' => $cashouts->count(),
            'total_amount' => $cashouts->sum('amount'),
            'pending_count' => $cashouts->where('status', 'pending')->count(),
            'pending_amount' => $cashouts->where('status', 'pending')->sum('amount'),
            'paid_count' => $cashouts->where('status', 'paid')->count(),
            'paid_amount' => $cashouts->where('status', 'paid')->sum('amount'),
            'cancelled_count' => $cashouts->where('status', 'cancelled')->count(),
            'cancelled_amount' => $cashouts->where('status', 'cancelled')->sum('amount'),
        ];
    }

    public function exportExcel()
    {
        $params = [
            'from_date' => $this->date_from,
            'to_date' => $this->date_to,
            'insurance_id' => $this->insurance_id,
            'contract_type_id' => $this->contract_type_id,
            'status' => $this->status,
            'format' => 'excel'
        ];

        $url = route('api.report.cashout.index') . '?' . http_build_query($params);

        $this->dispatch('downloadFile', ['url' => $url]);
    }
}
