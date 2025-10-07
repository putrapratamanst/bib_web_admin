@extends('layouts.app')

@section('title', 'Cashout Reconciliation Report')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-balance-scale me-2"></i>
                Cashout Reconciliation Report
            </h5>
            <small class="text-muted">Reconcile cashouts against journal entries and financial records</small>
        </div>
        <div class="card-body">
            <!-- Filters Form -->
            <form id="filterForm" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date" name="from_date">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date" name="to_date">
                    </div>
                    <div class="col-md-3">
                        <label for="insurance_id" class="form-label">Insurance Company</label>
                        <select class="form-select select2" id="insurance_id" name="insurance_id">
                            <option value="">All Insurance Companies</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" onclick="generateReconciliation()">
                            <i class="fas fa-search me-1"></i> Generate Reconciliation
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportExcel()">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>

            <!-- Reconciliation Summary -->
            <div id="reconciliationSummary" class="row mb-4" style="display: none;">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Cashouts</h6>
                            <h4 id="totalCashouts">0</h4>
                            <small id="totalCashoutsAmount">IDR 0</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Matched</h6>
                            <h4 id="matchedCount">0</h4>
                            <small id="matchedAmount">IDR 0</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">Unmatched</h6>
                            <h4 id="unmatchedCount">0</h4>
                            <small id="unmatchedAmount">IDR 0</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6 class="card-title">Variance</h6>
                            <h4 id="varianceAmount">IDR 0</h4>
                            <small id="variancePercentage">0%</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reconciliation Table -->
            <div id="reconciliationTable" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="cashout-reconciliation-table">
                        <thead class="table-header">
                            <tr>
                                <th>Cashout Number</th>
                                <th>Date</th>
                                <th>Insurance</th>
                                <th>Debit Note</th>
                                <th>Contract</th>
                                <th>Amount</th>
                                <th>Journal Entry</th>
                                <th>Journal Amount</th>
                                <th>Variance</th>
                                <th>Status</th>
                                <th>Reconciliation</th>
                            </tr>
                        </thead>
                        <tbody id="reconciliationTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load insurance companies for dropdown
    loadInsuranceDropdown();
    
    // Set default dates (current month)
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    $('#from_date').val(firstDay.toISOString().split('T')[0]);
    $('#to_date').val(today.toISOString().split('T')[0]);
});

function loadInsuranceDropdown() {
    $.ajax({
        url: "{{ route('api.contacts.select2') }}",
        method: "GET",
        data: { contact_type: 'insurance' },
        success: function(response) {
            const select = $('#insurance_id');
            if (response.items) {
                response.items.forEach(function(item) {
                    select.append(new Option(item.text, item.id));
                });
            }
        }
    });
}

function generateReconciliation() {
    const formData = $('#filterForm').serialize();
    
    $('#reconciliationTable').hide();
    $('#reconciliationSummary').hide();
    
    $.ajax({
        url: "{{ route('api.report.cashout-reconciliation.index') }}",
        method: "GET",
        data: formData + "&format=json",
        beforeSend: function() {
            Swal.fire({
                title: 'Generating Reconciliation...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            Swal.close();
            
            if (response.reconciliation && response.summary) {
                displayReconciliationSummary(response.summary);
                displayReconciliation(response.reconciliation);
                $('#reconciliationSummary').show();
                $('#reconciliationTable').show();
            } else {
                Swal.fire('No Data', 'No data found for the selected criteria', 'info');
            }
        },
        error: function(xhr) {
            Swal.close();
            Swal.fire('Error', 'Failed to generate reconciliation', 'error');
        }
    });
}

function displayReconciliationSummary(summary) {
    $('#totalCashouts').text(summary.total_cashouts);
    $('#totalCashoutsAmount').text('IDR ' + formatNumber(summary.total_cashouts_amount));
    $('#matchedCount').text(summary.matched_count);
    $('#matchedAmount').text('IDR ' + formatNumber(summary.matched_amount));
    $('#unmatchedCount').text(summary.unmatched_count);
    $('#unmatchedAmount').text('IDR ' + formatNumber(summary.unmatched_amount));
    $('#varianceAmount').text('IDR ' + formatNumber(summary.variance_amount));
    $('#variancePercentage').text(summary.variance_percentage + '%');
}

function displayReconciliation(reconciliation) {
    const tbody = $('#reconciliationTableBody');
    tbody.empty();
    
    if (reconciliation.length === 0) {
        tbody.append('<tr><td colspan="11" class="text-center">No data available</td></tr>');
        return;
    }
    
    reconciliation.forEach(function(item) {
        const statusBadge = getStatusBadge(item.status);
        const reconciliationBadge = getReconciliationBadge(item.reconciliation_status);
        const varianceClass = item.variance > 0 ? 'text-danger' : item.variance < 0 ? 'text-warning' : 'text-success';
        
        const row = `
            <tr>
                <td><a href="{{ route('transaction.cashouts.index') }}/${item.id || ''}">${item.cashout_number}</a></td>
                <td>${item.cashout_date}</td>
                <td>${item.insurance_name}</td>
                <td>${item.debit_note_number}</td>
                <td>${item.contract_number}</td>
                <td class="text-end">${item.amount_formatted}</td>
                <td>${item.journal_entry_number || '-'}</td>
                <td class="text-end">${item.journal_amount_formatted || '-'}</td>
                <td class="text-end ${varianceClass}">${item.variance_formatted}</td>
                <td>${statusBadge}</td>
                <td>${reconciliationBadge}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

function getStatusBadge(status) {
    const badges = {
        'Pending': '<span class="badge bg-warning">Pending</span>',
        'Paid': '<span class="badge bg-success">Paid</span>',
        'Cancelled': '<span class="badge bg-danger">Cancelled</span>'
    };
    return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
}

function getReconciliationBadge(status) {
    const badges = {
        'Matched': '<span class="badge bg-success">Matched</span>',
        'Unmatched': '<span class="badge bg-warning">Unmatched</span>',
        'Variance': '<span class="badge bg-danger">Variance</span>',
        'No Journal': '<span class="badge bg-secondary">No Journal</span>'
    };
    return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
}

function exportExcel() {
    const formData = $('#filterForm').serialize();
    window.open("{{ route('api.report.cashout-reconciliation.index') }}?" + formData + "&format=excel", '_blank');
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num || 0);
}
</script>
@endpush
