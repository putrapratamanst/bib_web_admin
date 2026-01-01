@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Payment &amp; Allocation
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-bordered" id="contact-table">
                <thead class="table-header">
                    <tr>
                        <th>Number</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Date</th>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th>Available</th>
                        <th>Status</th>
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
        $('#contact-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.payment-allocations.datatables') }}",
            columns: [
                { data: 'number', name: 'number', 
                    render: function(data, type, row) {
                        if (row.has_advance) {
                            return '<span class="text-muted">' + data + '</span>';
                        }
                        return '<a href="{{ route('transaction.payment-allocations.index') }}/' + row.id + '">' + data + '</a>';
                 },
                },
                { 
                    data: 'type', 
                    render: function (data, type) {
                        if (data == 'receive') {
                            return '<span class="badge bg-success">Receive</span>';
                        } else {
                            return '<span class="badge bg-danger">Pay</span>';
                        }
                    }
                },
                { data: 'contact_name', name: 'contact_name' },
                { data: 'display_date', name: 'date', className: 'text-start' },
                { data: 'currency_code', name: 'currency_code', className: 'text-start' },
                { data: 'display_amount', name: 'amount', className: 'text-end' },
                { 
                    data: 'available_for_allocation_formatted', 
                    name: 'available_for_allocation',
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (row.has_advance) {
                            return '<span class="text-muted">Rp ' + data + '</span>';
                        }
                        return 'Rp ' + data;
                    }
                },
                { 
                    data: 'has_advance',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (data) {
                            return '<span class="badge bg-warning text-dark"><i class="bi bi-lock-fill"></i> Advance Applied</span>';
                        }
                        if (row.is_fully_allocated) {
                            return '<span class="badge bg-secondary">Fully Allocated</span>';
                        }
                        return '<span class="badge bg-success-subtle text-success border border-success">Available</span>';
                    }
                }
            ],
            rowCallback: function(row, data) {
                if (data.has_advance) {
                    $(row).addClass('table-warning');
                } else if (data.is_fully_allocated) {
                    $(row).addClass('table-secondary');
                }
            }
        });
    });
</script>
@endpush