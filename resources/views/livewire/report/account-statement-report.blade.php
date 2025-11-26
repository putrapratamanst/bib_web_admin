<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>Account Statement / Buku Bank
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="chart_of_account_id" class="form-label">Account <span class="text-danger">*</span></label>
                                <select wire:model.live="chart_of_account_id" class="form-select" id="chart_of_account_id">
                                    <option value="">-- Select Account --</option>
                                    @foreach($cashBankAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button wire:click="exportExcel" class="btn btn-success">
                                        <i class="fas fa-file-excel me-1"></i>Export
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

    @if($chart_of_account_id)
        <!-- Account Info and Summary -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-muted mb-2">Account Information</h6>
                        @if($chartOfAccount)
                            <h5 class="mb-0">{{ $chartOfAccount->display_name }}</h5>
                            <small class="text-muted">{{ $chartOfAccount->accountCategory->name ?? '' }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Opening Balance</h6>
                                <h4 class="mb-0">{{ number_format($openingBalance, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Closing Balance</h6>
                                <h4 class="mb-0">{{ number_format($closingBalance, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Transaction Details</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Reference</th>
                                <th>Description</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($openingBalance != 0)
                                <tr class="table-info">
                                    <td colspan="4"><strong>Opening Balance</strong></td>
                                    <td colspan="2"></td>
                                    <td class="text-end"><strong>{{ number_format($openingBalance, 2, ',', '.') }}</strong></td>
                                </tr>
                            @endif
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->transaction_type === 'Cash Receipt' ? 'success' : ($transaction->transaction_type === 'Cash Payment' ? 'danger' : 'info') }}">
                                            {{ $transaction->transaction_type }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->reference }}</td>
                                    <td>{{ $transaction->description }}</td>
                                    <td class="text-end">
                                        @if($transaction->debit > 0)
                                            <span class="text-success">{{ number_format($transaction->debit, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($transaction->credit > 0)
                                            <span class="text-danger">{{ number_format($transaction->credit, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($transaction->balance, 2, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No transactions found for the selected period.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($transactions->count() > 0)
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="4" class="text-end">TOTAL</th>
                                    <th class="text-end">{{ number_format($totalDebit, 2, ',', '.') }}</th>
                                    <th class="text-end">{{ number_format($totalCredit, 2, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                                <tr class="table-info">
                                    <th colspan="6" class="text-end">CLOSING BALANCE</th>
                                    <th class="text-end">{{ number_format($closingBalance, 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <!-- Pagination -->
                @if($transactions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Please select an account to view the statement</h5>
            </div>
        </div>
    @endif

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
