<?php

namespace App\Livewire\Report;

use App\Models\DebitNote;
use App\Models\Contact;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
    public $page = 1;

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
        $query = DebitNote::with(['contact', 'contract', 'creditNotes', 'paymentAllocations', 'billings'])
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

        // Get matching debit notes and flatten to billing rows
        $debitNotes = $query->get();

        $rows = new Collection();
        foreach ($debitNotes as $dn) {
            $creditNotesAmount = $dn->creditNotes->sum('amount');

            // Group allocations by billing id so we can attribute allocations to each billing row
            // key = debit_note_billing_id, value = sum of allocation
            $allocationsByBilling = $dn->paymentAllocations->groupBy('debit_note_billing_id')
                ->map(function ($group) {
                    return $group->sum('allocation');
                });

            if ($dn->relationLoaded('billings') && $dn->billings->count()) {
                foreach ($dn->billings as $billing) {
                    $paymentForBilling = $allocationsByBilling->get($billing->id) ?? 0;

                    $rows->push((object)[
                        'debit_note' => $dn,
                        'billing' => $billing,
                        'credit_notes_amount' => $creditNotesAmount,
                        'payment_allocations_amount' => $paymentForBilling,
                    ]);
                }
            } else {
                // No billing rows: treat allocation as total allocated on the debit note (unlinked allocations)
                $totalAllocation = $dn->paymentAllocations->sum('allocation');

                $rows->push((object)[
                    'debit_note' => $dn,
                    'billing' => null,
                    'credit_notes_amount' => $creditNotesAmount,
                    'payment_allocations_amount' => $totalAllocation,
                ]);
            }
        }
        // Manual pagination for the flattened rows
        $perPage = 50;
        $page = $this->page ?: 1;
        $total = $rows->count();
        $pagedData = $rows->forPage($page, $perPage)->values();
        $debitNotes = new LengthAwarePaginator($pagedData, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        // Calculate totals for the current filter
        $totals = $this->calculateTotals();

        // Get contacts for filter dropdown - restrict to contacts matching current filters
        $contacts = Contact::whereHas('debitNotes', function ($q) {
            $q->when($this->date_from, function ($q) {
                $q->whereDate('date', '>=', $this->date_from);
            })
                ->when($this->date_to, function ($q) {
                    $q->whereDate('date', '<=', $this->date_to);
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
        })
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
        // Build same base query used in render, but eager load relations required for per-billing totals
        $query = DebitNote::with(['billings', 'creditNotes', 'paymentAllocations'])
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

        $debitNotes = $query->get();

        $totals = [
            'total_records' => 0,
            'total_amount_idr' => 0,
            'total_amount_usd' => 0,
            'total_posted' => 0,
            'total_unposted' => 0,
        ];

        foreach ($debitNotes as $dn) {
            $creditNotesAmount = $dn->creditNotes->sum('amount');

            // Group allocations by billing id so we can decide which billing rows are fully paid
            $allocationsByBilling = $dn->paymentAllocations->groupBy('debit_note_billing_id')
                ->map(function ($group) {
                    return $group->sum('allocation');
                });

            if ($dn->relationLoaded('billings') && $dn->billings->count()) {
                foreach ($dn->billings as $billing) {
                    $amount = $billing->amount ?? 0;

                    $paymentForBilling = $allocationsByBilling->get($billing->id) ?? 0;

                    // If billing is fully allocated (allocation equals billing amount), skip it
                    if (round($amount - $paymentForBilling, 2) == 0) {
                        continue;
                    }

                    // Count this billing row
                    $totals['total_records']++;

                    if (($dn->currency_code ?? 'IDR') === 'IDR') {
                        $totals['total_amount_idr'] += $amount;
                    } else {
                        $totals['total_amount_usd'] += $amount;
                    }

                    if ($dn->is_posted) {
                        $totals['total_posted']++;
                    } else {
                        $totals['total_unposted']++;
                    }
                }
            } else {
                $amount = $dn->amount ?? 0;
                $totalAllocation = $dn->paymentAllocations->sum('allocation');

                // If the debit note amount is fully allocated, skip it
                if (round($amount - $totalAllocation, 2) == 0) {
                    continue;
                }

                $totals['total_records']++;

                if (($dn->currency_code ?? 'IDR') === 'IDR') {
                    $totals['total_amount_idr'] += $amount;
                } else {
                    $totals['total_amount_usd'] += $amount;
                }

                if ($dn->is_posted) {
                    $totals['total_posted']++;
                } else {
                    $totals['total_unposted']++;
                }
            }
        }

        // Ensure numeric formatting (no rounding here, presentation will format)
        return $totals;
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
