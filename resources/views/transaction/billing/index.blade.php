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
                        return '<a href="{{ route('transaction.billings.index') }}/' + row.id + '">' + data + '</a>';
                    }
                },
                { data: 'type', name: 'type' },
                { data: 'date_formatted', name: 'date_formatted' },
                { data: 'due_date_formatted', name: 'due_date_formatted' },
                { data: 'contact', name: 'contact' },
                { data: 'amount_formatted', name: 'amount_formatted', className: 'text-end' },
                { data: 'status', name: 'status' }
            ]
        });
    });
</script>
@endpush