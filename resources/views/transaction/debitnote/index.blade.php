@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Debit Notes
            <div class="float-end">
                <a href="{{ route('transaction.debit-notes.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New Debit Note
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
                <!-- <div class="col-md-3">
                    <label for="posted-filter" class="form-label">Posted Status</label>
                    <select class="form-select" id="posted-filter">
                        <option value="">All</option>
                        <option value="1">Posted</option>
                        <option value="0">Not Posted</option>
                    </select>
                </div> -->
            </div>
            
            <table class="table table-new table-hover table-striped table-bordered" id="dn-table">
                <thead class="table-header">
                    <tr>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Placing</th>
                        <th>Policy Number</th>
                        <th>Insurance Type</th>
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
        
        var table = $('#dn-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.debit-notes.datatables') }}",
                data: function(d) {
                    d.status = $('#status-filter').val();
                    d.approval_status = $('#approval-status-filter').val();
                    d.insurance_type = $('#insurance-type-filter').val();
                    d.is_posted = $('#posted-filter').val();
                }
            },
            columns: [{
                    data: 'number',
                    name: 'number',
                    render: function(data, type, row) {
                        return '<a href="{{ route("transaction.debit-notes.show", "") }}/' + row.id + '">' + data + '</a>';
                    }
                },
                {
                    data: 'date',
                    name: 'date',
                    render: function(data, type, row) {
                        if (!data) return '';
                        var date = new Date(data);
                        var day = String(date.getDate()).padStart(2, '0');
                        var month = String(date.getMonth() + 1).padStart(2, '0');
                        var year = date.getFullYear();
                        return day + '-' + month + '-' + year;
                    }
                },
                {
                    data: 'due_date',
                    name: 'due_date',
                    render: function(data, type, row) {
                        if (!data) return '';
                        var date = new Date(data);
                        var day = String(date.getDate()).padStart(2, '0');
                        var month = String(date.getMonth() + 1).padStart(2, '0');
                        var year = date.getFullYear();
                        return day + '-' + month + '-' + year;
                    }
                },
                {
                    data: 'contract',
                    name: 'contract'
                },
                {
                    data: 'policy_number',
                    name: 'policy_number',
                    render: function(data, type, row) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'insurance_type',
                    name: 'insurance_type',
                    render: function(data, type, row) {
                        return data ? '<span class="badge bg-info">' + data + '</span>' : '-';
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    className: 'text-end',
                    render: function(data, type, row) {
                        return parseFloat(data).toLocaleString('de-DE', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
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
        $('#status-filter, #approval-status-filter, #insurance-type-filter, #posted-filter').on('change', function() {
            table.ajax.reload();
        });

        // Handle approve button click
        $(document).on('click', '.approve-btn', function() {
            const debitNoteId = $(this).data('id');
            const notes = prompt('Enter approval notes (optional):');

            if (confirm('Are you sure you want to approve this Debit Note?')) {
                $.ajax({
                    url: `/api/debit-note/${debitNoteId}/approve`,
                    type: 'POST',
                    data: {
                        notes: notes,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Debit Note approved successfully!');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let message = 'Error: ';
                        if (xhr.status === 403) {
                            message += 'You are not authorized to perform this action. Only users with approver role can approve Debit Notes.';
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
            const debitNoteId = $(this).data('id');
            const notes = prompt('Enter rejection reason:');

            if (notes && confirm('Are you sure you want to reject this Debit Note?')) {
                $.ajax({
                    url: `/api/debit-note/${debitNoteId}/reject`,
                    type: 'POST',
                    data: {
                        notes: notes,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Debit Note rejected!');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        let message = 'Error: ';
                        if (xhr.status === 403) {
                            message += 'You are not authorized to perform this action. Only users with approver role can reject Debit Notes.';
                        } else {
                            message += xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred';
                        }
                        alert(message);
                    }
                });
            }
        });

        // Handle print button click
        $(document).on('click', '.print-btn', function() {
            const debitNoteId = $(this).data('id');
            alert('Print functionality will be implemented here');
            // TODO: Implement actual print functionality
        });
    });

    function postDebitNote(debitNoteId) {
        Swal.fire({
            title: 'Post Debit Note',
            text: 'Are you sure you want to post this Debit Note? This will automatically create cashouts to insurance companies.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Post it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Posting...',
                    text: 'Please wait while we process your request.',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/api/debit-note/${debitNoteId}/post`,
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Posted Successfully!',
                                html: `
                                    <div class="text-start">
                                        <p><strong>Message:</strong> ${response.message}</p>
                                        ${response.data && response.data.cashouts_count ? 
                                            `<p><strong>Cashouts Created:</strong> ${response.data.cashouts_count}</p>` 
                                            : ''
                                        }
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Reload DataTable
                                    $('#dn-table').DataTable().ajax.reload();
                                }
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to post Debit Note';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                    }
                });
            }
        });
    }

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