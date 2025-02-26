@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Debit Notes
            <div class="float-end">
                {{-- <a href="{{ route('transaction.credit-notes.create') }}" class="btn btn-primary btn-sm">
                    Add New Credit Note
                </a> --}}
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
                        return '<a href="{{ route('transaction.debit-notes.index') }}/' + row.id + '">' + data + '</a>';
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
                    name: 'status' 
                }
            ]
        });
    });
</script>
@endpush