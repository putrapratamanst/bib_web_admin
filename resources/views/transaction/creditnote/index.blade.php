@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Credit Notes
            <div class="float-end">
                <a href="{{ route('transaction.credit-notes.create') }}" class="btn btn-primary btn-sm">
                    Add New Credit Note
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="status-filter" class="form-label">Status</label>
                    <select class="form-select" id="status-filter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="draft">Draft</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="approval-status-filter" class="form-label">Approval Status</label>
                    <select class="form-select" id="approval-status-filter">
                        <option value="">All Approval Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="insurance-type-filter" class="form-label">Insurance Type</label>
                    <select class="form-select" id="insurance-type-filter">
                        <option value="">All Types</option>
                        <!-- Options will be loaded via AJAX -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="currency-filter" class="form-label">Currency</label>
                    <select class="form-select" id="currency-filter">
                        <option value="">All Currency</option>
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
            </div>
            
            <table class="table table-new table-hover table-striped table-bordered" id="cn-table">
                <thead class="table-header">
                    <tr>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Contract Number</th>
                        <th>Contact</th>
                        <th>Insurance Type</th>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Approval Status</th>
                        <th width="200px">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Load insurance types for filter
        loadInsuranceTypes();
        
        // Check URL parameters and set default filters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('filter') === 'pending') {
            $('#approval-status-filter').val('pending');
        }
        
        var table = $('#cn-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.credit-notes.datatables') }}",
                data: function(d) {
                    d.status = $('#status-filter').val();
                    d.approval_status = $('#approval-status-filter').val();
                    d.insurance_type = $('#insurance-type-filter').val();
                    d.currency_code = $('#currency-filter').val();
                }
            },
            columns: [
                { 
                    data: 'number', 
                    name: 'number',
                    render: function(data, type, row) {
                        return '<a href="{{ route('transaction.credit-notes.index') }}/' + row.id + '">' + data + '</a>';
                    }
                },
                { data: 'date_formatted', name: 'date_formatted',searchable: false },
                { 
                    data: 'contract_number', 
                    name: 'contract_number',
                    render: function(data, type, row) {
                        return '<a href="{{ route('transaction.contracts.index') }}/' + row.contract_id + '">' + data + '</a>';
                    },
                },
                { data: 'contact', name: 'contact' },
                {
                    data: 'insurance_type',
                    name: 'insurance_type',
                    render: function(data, type, row) {
                        return data ? '<span class="badge bg-info">' + data + '</span>' : '-';
                    }
                },
                { data: 'currency_code', name: 'currency_code' },
                { 
                    data: 'amount_formatted', 
                    name: 'amount_formatted', 
                    className: 'text-end',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row) {
                        let badgeClass = 'bg-secondary';
                        if (data === 'active') badgeClass = 'bg-success';
                        else if (data === 'draft') badgeClass = 'bg-warning';
                        else if (data === 'cancelled') badgeClass = 'bg-danger';

                        return '<span class="badge ' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                    }
                },
                { 
                    data: 'approval_status_badge', 
                    name: 'approval_status',
                    orderable: false,
                    searchable: false
                },
                { 
                    data: 'actions', 
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        
        // Handle filter changes
        $('#status-filter, #approval-status-filter, #insurance-type-filter, #currency-filter').on('change', function() {
            table.ajax.reload();
        });
        
        // Handle approve button click
        $(document).on('click', '.approve-btn', function() {
            const creditNoteId = $(this).data('id');
            const notes = prompt('Enter approval notes (optional):');
            
            if (confirm('Are you sure you want to approve this Credit Note?')) {
                $.ajax({
                    url: `/api/credit-note/${creditNoteId}/approve`,
                    type: 'POST',
                    data: {
                        notes: notes,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Credit Note approved successfully!');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let message = 'Error: ';
                        if (xhr.status === 403) {
                            message += 'You are not authorized to perform this action. Only users with approver role can approve Credit Notes.';
                        } else {
                            message += xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred';
                        }
                        alert(message);
                    }
                });
            }
        });
        
        // Handle reject button click
        $(document).on('click', '.reject-btn', function() {
            const creditNoteId = $(this).data('id');
            const notes = prompt('Enter rejection reason:');
            
            if (notes && confirm('Are you sure you want to reject this Credit Note?')) {
                $.ajax({
                    url: `/api/credit-note/${creditNoteId}/reject`,
                    type: 'POST',
                    data: {
                        notes: notes,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Credit Note rejected!');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let message = 'Error: ';
                        if (xhr.status === 403) {
                            message += 'You are not authorized to perform this action. Only users with approver role can reject Credit Notes.';
                        } else {
                            message += xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred';
                        }
                        alert(message);
                    }
                });
            }
        });
        
        // Handle print button click
        // $(document).on('click', '.print-btn', function() {
        //     const creditNoteId = $(this).data('id');
        //     alert('Print functionality will be implemented here');
        //     // TODO: Implement actual print functionality
        // });
    });

    function loadInsuranceTypes() {
        $.ajax({
            url: "{{ route('api.contract-types.index') }}",
            method: "GET",
            success: function(contractTypes) {
                const select = $('#insurance-type-filter');
                contractTypes.forEach(function(type) {
                    select.append(`<option value="${type.id}">${type.name}</option>`);
                });
            },
            error: function() {
                console.log('Failed to load contract types');
            }
        });
    }
</script>
@endpush