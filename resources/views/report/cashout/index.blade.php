@extends('layouts.app')

@section('title', 'Cashout Report')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-money-bill-wave me-2"></i>
                Cashout Report
            </h5>
            <small class="text-muted">Generate cashout reports with filtering options</small>
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
                        <button type="button" class="btn btn-primary" onclick="generateReport()">
                            <i class="fas fa-search me-1"></i> Generate Report
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

            <!-- Summary Cards -->
            <div id="summaryCards" class="row mb-4" style="display: none;">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Records</h6>
                            <h4 id="totalRecords">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">Pending</h6>
                            <h4 id="pendingCount">0</h4>
                            <small id="pendingAmount">IDR 0</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Paid</h6>
                            <h4 id="paidCount">0</h4>
                            <small id="paidAmount">IDR 0</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6 class="card-title">Cancelled</h6>
                            <h4 id="cancelledCount">0</h4>
                            <small id="cancelledAmount">IDR 0</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Table -->
            <div id="reportTable" style="display: none;">
                <table class="table table-striped table-bordered" id="cashout-report-table">
                    <thead class="table-header">
                        <tr>
                            <th>Cashout Number</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th>Debit Note</th>
                            <th>Contract</th>
                            <th>Client</th>
                            <th>Insurance</th>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                    </tbody>
                </table>
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

function generateReport() {
    const formData = $('#filterForm').serialize();
    
    $('#reportTable').hide();
    $('#summaryCards').hide();
    
    $.ajax({
        url: "{{ route('api.report.cashout.index') }}",
        method: "GET",
        data: formData + "&format=json",
        beforeSend: function() {
            Swal.fire({
                title: 'Generating Report...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            Swal.close();
            
            if (response.cashouts && response.summary) {
                displaySummary(response.summary);
                displayReport(response.cashouts);
                $('#summaryCards').show();
                $('#reportTable').show();
            } else {
                Swal.fire('No Data', 'No data found for the selected criteria', 'info');
            }
        },
        error: function(xhr) {
            Swal.close();
            Swal.fire('Error', 'Failed to generate report', 'error');
        }
    });
}

function displaySummary(summary) {
    $('#totalRecords').text(summary.total_records);
    $('#pendingCount').text(summary.pending_count);
    $('#pendingAmount').text('IDR ' + formatNumber(summary.pending_amount));
    $('#paidCount').text(summary.paid_count);
    $('#paidAmount').text('IDR ' + formatNumber(summary.paid_amount));
    $('#cancelledCount').text(summary.cancelled_count);
    $('#cancelledAmount').text('IDR ' + formatNumber(summary.cancelled_amount));
}

function displayReport(cashouts) {
    const tbody = $('#reportTableBody');
    tbody.empty();
    
    if (cashouts.length === 0) {
        tbody.append('<tr><td colspan="10" class="text-center">No data available</td></tr>');
        return;
    }
    
    cashouts.forEach(function(cashout) {
        const statusBadge = getStatusBadge(cashout.status);
        const row = `
            <tr>
                <td><a href="{{ route('transaction.cashouts.index') }}/${cashout.id || ''}">${cashout.cashout_number}</a></td>
                <td>${cashout.cashout_date}</td>
                <td>${cashout.due_date}</td>
                <td>${cashout.debit_note_number}</td>
                <td>${cashout.contract_number}</td>
                <td>${cashout.client_name}</td>
                <td>${cashout.insurance_name}</td>
                <td>${cashout.currency_code}</td>
                <td class="text-end">${cashout.amount_formatted}</td>
                <td>${statusBadge}</td>
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

function exportExcel() {
    const formData = $('#filterForm').serialize();
    window.open("{{ route('api.report.cashout.index') }}?" + formData + "&format=excel", '_blank');
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num || 0);
}
</script>
@endpush
