@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Billing
            <div class="float-end">
                <a href="{{ route('transaction.billings.create') }}" class="btn btn-primary btn-sm">
                    Add New Billing
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="billing-table">
                <thead class="table-header">
                    <tr>
                        <th>Number</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Contact</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
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
        var table = $('#billing-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.billings.datatables') }}",
            },
            columns: [
                { 
                    data: 'number', 
                    name: 'number',
                    render: function(data, type, row) {
                        return '<a href="/transaction/billings/' + row.id + '">' + data + '</a>';
                    }
                },
                { data: 'type', name: 'type' },
                { data: 'date_formatted', name: 'date_formatted' },
                { data: 'due_date_formatted', name: 'due_date_formatted' },
                { data: 'contact', name: 'contact' },
                { data: 'amount_formatted', name: 'amount_formatted', className: 'text-end' },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data, type, row) {
                        let statusBadge = '';
                        if (data === 'pending') {
                            statusBadge = '<span class="badge bg-warning">Pending</span>';
                        } else if (data === 'posted') {
                            statusBadge = '<span class="badge bg-success">Posted</span>';
                        } else {
                            statusBadge = '<span class="badge bg-secondary">' + data + '</span>';
                        }
                        return statusBadge;
                    }
                },
                { 
                    data: 'id', 
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let actions = '';
                        if (row.status === 'pending') {
                            actions += '<button class="btn btn-sm btn-success me-1" onclick="postBilling(\'' + data + '\')" title="Post to Cashout">';
                            actions += '<i class="fas fa-check-circle"></i> Post</button>';
                        }
                        actions += '<a href="javascript:void(0);" class="btn btn-sm btn-info me-1" onclick="printBilling('\'' + data + '\'')" title="Print Billing">';
                        actions += '<i class="fas fa-print"></i> Print</a>';
                        actions += '<a href="javascript:void(0);" class="btn btn-sm btn-success" onclick="printBillingDirectory('\'' + data + '\'')" title="Print Billing Directory">';
                        actions += '<i class="fas fa-print"></i> Print Directory</a>';
                        return actions;
                    }
                }
            ]
        });

        // Function to post billing
        window.postBilling = function(billingId) {
            if (confirm('Are you sure you want to post this billing to cashout?')) {
                $.ajax({
                    url: '/api/debit-note-billing/' + billingId + '/post',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    data: {
                        id: billingId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        };

        // Function to print billing
        window.printBilling = function(billingId) {
            window.open('/transaction/billings/print/' + billingId, '_blank');
        };

        // Function to print billing directory
        window.printBillingDirectory = function(billingId) {
            window.open('/transaction/billings/print-directory/' + billingId, '_blank');
        };
    });
</script>
@endpush