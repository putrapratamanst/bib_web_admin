<div>
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Laporan Hutang Asuransi
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
                                <label for="insurance_id" class="form-label">Insurance Company</label>
                                <select wire:model.live="insurance_id" class="form-select" id="insurance_id">
                                    <option value="">All Insurance Companies</option>
                                    @foreach($insurances as $insurance)
                                        <option value="{{ $insurance->id }}">{{ $insurance->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select wire:model.live="status" class="form-select" id="status">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="contract_type_id" class="form-label">Type Insurance</label>
                                <select wire:model.live="contract_type_id" class="form-select" id="contract_type_id">
                                    <option value="">All Types</option>
                                    @foreach($contractTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Pending</h6>
                            <h4 class="mb-0">{{ number_format($totals['pending_count']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
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
                            <h6 class="card-title">Paid</h6>
                            <h4 class="mb-0">{{ number_format($totals['paid_count']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Cancelled</h6>
                            <h4 class="mb-0">{{ number_format($totals['cancelled_count']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">Detail Hutang Asuransi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nomor Hutang</th>
                            <th>Billing Number</th>
                            <th>DN Number</th>
                            <th>Contract Number</th>
                            <th>Policy Number</th>
                            <th>Client</th>
                            <th>Insurance Company</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cashouts as $cashout)
                            <tr>
                                <td>
                                    <strong>{{ $cashout->number }}</strong>
                                    @if($cashout->installment_number)
                                        <br><small class="text-muted">Installment: {{ $cashout->installment_number }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($cashout->debitNoteBilling)
                                        {{ $cashout->debitNoteBilling->billing_number ?? $cashout->debitNoteBilling->id }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cashout->debitNote)
                                        {{ $cashout->debitNote->number }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cashout->debitNote && $cashout->debitNote->contract)
                                        {{ $cashout->debitNote->contract->number }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cashout->debitNote && $cashout->debitNote->contract)
                                        {{ $cashout->debitNote->contract->policy_number ?? '-' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cashout->debitNote && $cashout->debitNote->contract && $cashout->debitNote->contract->contact)
                                        {{ $cashout->debitNote->contract->contact->display_name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cashout->insurance)
                                        {{ $cashout->insurance->display_name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $cashout->date ? \Carbon\Carbon::parse($cashout->date)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $cashout->due_date ? \Carbon\Carbon::parse($cashout->due_date)->format('d/m/Y') : '-' }}</td>
                                <td class="text-center">
                                    @php
                                        $dueDate = $cashout->due_date ? \Carbon\Carbon::parse($cashout->due_date) : null;
                                        $daysOverdue = $dueDate ? (int)now()->diffInDays($dueDate, false) : null;
                                    @endphp
                                    @if($daysOverdue !== null)
                                        @if($daysOverdue < 0)
                                            <span class="badge bg-danger">{{ abs($daysOverdue) }} hari lewat</span>
                                        @elseif($daysOverdue == 0)
                                            <span class="badge bg-warning text-dark">Jatuh tempo hari ini</span>
                                        @else
                                            <span class="badge bg-success">{{ $daysOverdue }} hari lagi</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>{{ $cashout->currency_code }} {{ number_format($cashout->amount, 2, ',', '.') }}</strong>
                                    @if($cashout->currency_code !== 'IDR')
                                        <br><small class="text-muted">IDR {{ number_format($cashout->amount * $cashout->exchange_rate, 2, ',', '.') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($cashout->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($cashout->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($cashout->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($cashout->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('transaction.cashouts.show', $cashout->id) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>Tidak ada data hutang asuransi untuk kriteria yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($cashouts->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $cashouts->links() }}
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
