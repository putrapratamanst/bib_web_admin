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
            <table class="table table-new table-hover table-striped table-bordered" id="cn-table">
                <thead class="table-header">
                    <tr>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Contract Number</th>
                        <th>Contact</th>
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
        var table = $('#cn-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.credit-notes.datatables') }}",
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
</script>
@endpush