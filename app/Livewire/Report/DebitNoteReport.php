<?php

namespace App\Livewire\Report;

use App\Models\DebitNote;
use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class DebitNoteReport extends Component
{
    use WithPagination;

    public $date_from;
    public $date_to;
    public $contact_id = '';
    public $status = '';
    public $currency_code = '';
    public $is_posted = '';

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Set default date range to current month
        $this->date_from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->date_to = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingContactId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCurrencyCode()
    {
        $this->resetPage();
    }

    public function updatingIsPosted()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = DebitNote::with(['contact', 'contract', 'creditNotes', 'paymentAllocations'])
            ->when($this->date_from, function ($q) {
                $q->whereDate('date', '>=', $this->date_from);
            })
            ->when($this->date_to, function ($q) {
                $q->whereDate('date', '<=', $this->date_to);
            })
            ->when($this->contact_id, function ($q) {
                $q->where('contact_id', $this->contact_id);
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->currency_code, function ($q) {
                $q->where('currency_code', $this->currency_code);
            })
            ->when($this->is_posted !== '', function ($q) {
                $q->where('is_posted', $this->is_posted);
            })
            ->orderBy('date', 'desc')
            ->orderBy('number', 'desc');

        $debitNotes = $query->paginate(50);

        // Calculate totals for the current filter
        $totals = $this->calculateTotals();

        // Get contacts for filter dropdown - using whereHas instead of direct relationship
        $contacts = Contact::whereHas('debitNotes')
            ->orderBy('display_name')
            ->get();

        return view('livewire.report.debit-note-report', [
            'debitNotes' => $debitNotes,
            'totals' => $totals,
            'contacts' => $contacts
        ]);
    }

    private function calculateTotals()
    {
        $query = DebitNote::query()
            ->when($this->date_from, function ($q) {
                $q->whereDate('date', '>=', $this->date_from);
            })
            ->when($this->date_to, function ($q) {
                $q->whereDate('date', '<=', $this->date_to);
            })
            ->when($this->contact_id, function ($q) {
                $q->where('contact_id', $this->contact_id);
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->currency_code, function ($q) {
                $q->where('currency_code', $this->currency_code);
            })
            ->when($this->is_posted !== '', function ($q) {
                $q->where('is_posted', $this->is_posted);
            });

        return [
            'total_records' => $query->count(),
            'total_amount_idr' => $query->where('currency_code', 'IDR')->sum('amount'),
            'total_amount_usd' => $query->where('currency_code', 'USD')->sum('amount'),
            'total_posted' => $query->where('is_posted', true)->count(),
            'total_unposted' => $query->where('is_posted', false)->count(),
        ];
    }

    public function exportExcel()
    {
        $params = [
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'contact_id' => $this->contact_id,
            'status' => $this->status,
            'currency_code' => $this->currency_code,
            'is_posted' => $this->is_posted,
            'format' => 'excel'
        ];

        $url = route('api.reports.debit-notes') . '?' . http_build_query($params);
        
        $this->dispatch('downloadFile', ['url' => $url]);
    }
}