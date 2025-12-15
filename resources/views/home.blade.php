@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h3>
                    <p class="mb-0">Welcome to Insurance Management System</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Active Contracts</h6>
                            <h3 class="mb-0" id="activeContracts">-</h3>
                            <small class="text-muted">Total active policies</small>
                        </div>
                        <div>
                            <i class="fas fa-file-contract fa-3x text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending Cashouts</h6>
                            <h3 class="mb-0" id="pendingCashouts">-</h3>
                            <small class="text-muted">Awaiting payment</small>
                        </div>
                        <div>
                            <i class="fas fa-money-bill-wave fa-3x text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-3">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Outstanding Debit</h6>
                            <h3 class="mb-0" id="outstandingDebit">-</h3>
                            <small class="text-muted">Total receivables</small>
                        </div>
                        <div>
                            <i class="fas fa-file-invoice-dollar fa-3x text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="col-md-4">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Clients</h6>
                            <h3 class="mb-0" id="totalClients">-</h3>
                            <small class="text-muted">Registered contacts</small>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Monthly Premium Overview
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="premiumChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Insurance Type Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="{{ route('transaction.contracts.create') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus-circle me-2"></i>New Contract
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('transaction.debit-notes.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-file-invoice me-2"></i>New Debit Note
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('transaction.cash-banks.create') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-dollar-sign me-2"></i>New Cash/Bank
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('master.contacts.create') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-user-plus me-2"></i>New Contact
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Contracts
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Contract No</th>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentContracts">
                                <tr>
                                    <td colspan="4" class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Quick Report Access
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('report.balance.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-balance-scale me-2"></i>Balance Sheet
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('report.profitandloss.index') }}" class="btn btn-outline-success">
                                    <i class="fas fa-chart-line me-2"></i>Profit & Loss
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('report.cashflow.index') }}" class="btn btn-outline-info">
                                    <i class="fas fa-money-bill-wave me-2"></i>Cash Flow
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('report.piutang.index') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-file-invoice-dollar me-2"></i>Piutang Report
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('report.debit-notes.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-receipt me-2"></i>Debit Note Report
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('report.cashout.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-hand-holding-usd me-2"></i>Cashout Report
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('report.console.index') }}" class="btn btn-outline-dark">
                                    <i class="fas fa-desktop me-2"></i>Console Report
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <a href="{{ route('report.balance-sheet.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-file-alt me-2"></i>Balance Sheet
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
$(document).ready(function() {
    loadDashboardStats();
    initCharts();
    loadRecentContracts();
});

function loadDashboardStats() {
    // Load Active Contracts
    $.ajax({
        url: "{{ route('api.contracts.datatables') }}",
        method: "GET",
        data: { length: 1 },
        success: function(response) {
            $('#activeContracts').text(response.recordsTotal || 0);
        }
    });

    // Load Pending Cashouts
    $.ajax({
        url: "{{ route('api.cashouts.datatables') }}",
        method: "GET",
        data: { status: 'pending', length: 1 },
        success: function(response) {
            $('#pendingCashouts').text(response.recordsTotal || 0);
        }
    });

    // Load Total Clients
    $.ajax({
        url: "{{ route('api.contacts.datatables') }}",
        method: "GET",
        data: { length: 1 },
        success: function(response) {
            $('#totalClients').text(response.recordsTotal || 0);
        }
    });

    // Placeholder for Outstanding Debit
    $('#outstandingDebit').text('IDR 0');
}

function loadRecentContracts() {
    $.ajax({
        url: "{{ route('api.contracts.datatables') }}",
        method: "GET",
        data: { length: 5, order: [[0, 'desc']] },
        success: function(response) {
            const tbody = $('#recentContracts');
            tbody.empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(contract) {
                    const row = `
                        <tr>
                            <td><a href="/transaction/contracts/${contract.id}">${contract.number}</a></td>
                            <td>${contract.contact_name || '-'}</td>
                            <td><span class="badge bg-info">${contract.type || '-'}</span></td>
                            <td>${contract.date || '-'}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.append('<tr><td colspan="4" class="text-center py-3 text-muted">No recent contracts</td></tr>');
            }
        },
        error: function() {
            $('#recentContracts').html('<tr><td colspan="4" class="text-center py-3 text-muted">Failed to load contracts</td></tr>');
        }
    });
}

function initCharts() {
    // Premium Overview Chart
    const premiumCtx = document.getElementById('premiumChart').getContext('2d');
    new Chart(premiumCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Premium (IDR)',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'IDR ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Insurance Type Distribution Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Property Insurance', 'Automobile Insurance'],
            datasets: [{
                data: [0, 0],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
}
</script>
@endpush
@endsection