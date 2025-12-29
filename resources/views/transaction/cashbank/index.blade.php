@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Cash &amp; Bank
            <div class="float-end">
                <a href="{{ route('transaction.cash-banks.create') }}" class="btn btn-primary btn-sm">
                    Add New
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="contact-table">
                <thead class="table-header">
                    <tr>
                        <th>Number</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Date</th>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th width="100">Action</th>
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
            ajax: "{{ route('api.cash-banks.datatables') }}",
            columns: [
                { data: 'number', name: 'number', 
                    render: function(data, type, row) {
                        return '<a href="{{ route('transaction.cash-banks.index') }}/' + row.id + '">' + data + '</a>';
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
                    data: 'id', 
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let actions = '<div class="btn-group btn-group-sm" role="group">';
                        actions += '<a href="{{ route('transaction.cash-banks.index') }}/' + data + '" class="btn btn-info btn-sm me-1" title="View"><i class="bi bi-eye"></i></a>';
                        // Show print button for both pay and receive
                        actions += '<a href="{{ route('transaction.cash-banks.index') }}/' + data + '/print" class="btn btn-primary btn-sm" title="Print" target="_blank"><i class="bi bi-printer"></i></a>';
                        actions += '</div>';
                        return actions;
                    }
                }
            ]
        });
    });
</script>
@endpush