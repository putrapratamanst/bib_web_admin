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
            <table class="table table-new table-hover table-striped table-bordered" id="dn-table">
                <thead class="table-header">
                    <tr>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Contract</th>
                        <th>Installment</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <!-- <th>Posted</th>
                        <th>Action</th> -->
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
        var table = $('#dn-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.debit-notes.datatables') }}",
            },
            columns: [
                { 
                    data: 'number', 
                    name: 'number',
                    render: function(data, type, row) {
                        return '<a href="{{ route("transaction.debit-notes.show", "") }}/' + row.id + '">' + data + '</a>';
                    }
                },
                { 
                    data: 'date_formatted', 
                    name: 'date_formatted' 
                },
                { 
                    data: 'due_date_formatted', 
                    name: 'due_date_formatted' 
                },
                { 
                    data: 'contract', 
                    name: 'contract' 
                },
                { 
                    data: 'installment', 
                    name: 'installment' 
                },
                { 
                    data: 'amount_formatted', 
                    name: 'amount_formatted', 
                    className: 'text-end' 
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
            ]
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
</script>
@endpush