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
                    orderable: false,
                }
            ]
        });
    });
</script>
@endpush