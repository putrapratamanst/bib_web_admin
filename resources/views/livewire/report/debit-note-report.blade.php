<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Debit Note Report
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" wire:model.live="date_from" class="form-control" id="date_from">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" wire:model.live="date_to" class="form-control" id="date_to">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="contact_id" class="form-label">Contact</label>
                                <select wire:model.live="contact_id" class="form-select" id="contact_id">
                                    <option value="">All Contacts</option>
                                    @foreach($contacts as $contact)
                                        <option value="{{ $contact->id }}">{{ $contact->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select wire:model.live="status" class="form-select" id="status">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="currency_code" class="form-label">Currency</label>
                                <select wire:model.live="currency_code" class="form-select" id="currency_code">
                                    <option value="">All Currencies</option>
                                    <option value="IDR">IDR</option>
                                    <option value="USD">USD</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="is_posted" class="form-label">Posted Status</label>
                                <select wire:model.live="is_posted" class="form-select" id="is_posted">
                                    <option value="">All</option>
                                    <option value="1">Posted</option>
                                    <option value="0">Not Posted</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button wire:click="exportExcel" class="btn btn-success">
                                        <i class="fas fa-file-excel me-1"></i>Export Excel
                                    </button>
                                    <button wire:click="$refresh" class="btn btn-primary">
                                        <i class="fas fa-sync me-1"></i>Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Records</h6>
                            <h4 class="mb-0">{{ number_format($totals['total_records']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-invoice fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total IDR</h6>
                            <h4 class="mb-0">{{ number_format($totals['total_amount_idr'], 0, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total USD</h6>
                            <h4 class="mb-0">{{ number_format($totals['total_amount_usd'], 2, ',', '.') }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Posted</h6>
                            <h4 class="mb-0">{{ number_format($totals['total_posted']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Not Posted</h6>
                            <h4 class="mb-0">{{ number_format($totals['total_unposted']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">Debit Note Details</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>DN Number</th>
                            <th>Billing Number</th>
                            <th>Contract</th>
                            <th>Contact</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>Outstanding</th>
                            <th>Status</th>
                            <th>Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debitNotes as $row)
                            @php
                                $debitNote = $row->debit_note;
                                $billing = $row->billing;

                                // Use billing amount if available, otherwise debit note amount
                                $amount = $billing ? $billing->amount : $debitNote->amount;

                                $creditNotesAmount = $row->credit_notes_amount;
                                $paymentAllocationsAmount = $row->payment_allocations_amount;

                                $proportion = 0;
                                if ($billing && $debitNote->amount > 0) {
                                    $proportion = $amount / $debitNote->amount;
                                }

                                $creditApplied = $billing ? round($creditNotesAmount * $proportion, 2) : $creditNotesAmount;
                                $paymentApplied = $billing ? round($paymentAllocationsAmount * $proportion, 2) : $paymentAllocationsAmount;

                                $outstandingAmount = $amount - $creditApplied - $paymentApplied;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $debitNote->number }}</strong>
                                    @if($debitNote->installment > 0)
                                        <br><small class="text-muted">Installment: {{ $debitNote->installment }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($billing)
                                        {{ $billing->billing_number ?? $billing->id }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($debitNote->contract)
                                        {{ $debitNote->contract->number }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($debitNote->contact)
                                        {{ $debitNote->contact->display_name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $billing && $billing->date ? \Carbon\Carbon::parse($billing->date)->format('d/m/Y') : ($debitNote->date ? \Carbon\Carbon::parse($debitNote->date)->format('d/m/Y') : '-') }}</td>
                                <td>
                                    {{ $billing && $billing->due_date ? \Carbon\Carbon::parse($billing->due_date)->format('d/m/Y') : ($debitNote->due_date ? \Carbon\Carbon::parse($debitNote->due_date)->format('d/m/Y') : '-') }}
                                    @if(($billing && $billing->due_date && \Carbon\Carbon::parse($billing->due_date)->isPast()) || ($debitNote->due_date && \Carbon\Carbon::parse($debitNote->due_date)->isPast()))
                                        <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Overdue</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $debitNote->currency_code }}</span>
                                    @if($debitNote->currency_code !== 'IDR')
                                        <br><small class="text-muted">Rate: {{ number_format($debitNote->exchange_rate, 2, ',', '.') }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>{{ $debitNote->currency_code }} {{ number_format($amount, 2, ',', '.') }}</strong>
                                    @if($debitNote->currency_code !== 'IDR')
                                        <br><small class="text-muted">IDR {{ number_format($amount * $debitNote->exchange_rate, 2, ',', '.') }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $outstandingAmount > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $debitNote->currency_code }} {{ number_format($outstandingAmount, 2, ',', '.') }}
                                    </strong>
                                    @if($creditApplied > 0 || $paymentApplied > 0)
                                        <br><small class="text-muted">
                                            CN: {{ number_format($creditApplied, 2, ',', '.') }} | 
                                            PA: {{ number_format($paymentApplied, 2, ',', '.') }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $debitNote->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($debitNote->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $debitNote->is_posted ? 'success' : 'warning' }}">
                                        {{ $debitNote->is_posted ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('transaction.debit-notes.show', $debitNote->id) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>No debit notes found for the selected criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($debitNotes->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $debitNotes->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Handle file download
        window.addEventListener('downloadFile', event => {
            const url = event.detail[0].url;
            window.open(url, '_blank');
        });
    </script>
    @endpush
</div>