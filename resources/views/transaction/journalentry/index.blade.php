@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Journal Entries
            <div class="float-end">
                <a href="{{ route('transaction.journal-entries.create') }}" class="btn btn-primary btn-sm">
                    Add New Journal Entry
                </a>
            </div>
        </div>
        <?php /*<div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
            <div>
                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        <div class="mb-0">
                            <label for="contract_type" class="form-label">Contract Type</label>
                            <select name="contract_type" id="contract_type" class="form-control select2">
                                <option value="">All</option>
                                @foreach($contractTypes as $contractType)
                                    <option value="{{ $contractType->id }}">{{ $contractType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div> */ ?>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="journal-table">
                <thead class="table-header">
                    <tr>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Amount</th>
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
        var table = $('#journal-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.journal-entries.datatables') }}",
            },
            columns: [
                { 
                    data: 'number', 
                    name: 'number',
                    render: function(data, type, row) {
                        return '<a href="{{ route('transaction.contracts.index') }}/' + row.id + '">' + data + '</a>';
                    }
                },
                { data: 'date_formatted', name: 'date_formatted' },
                { data: 'reference', name: 'reference' },
                { data: 'amount_formatted', name: 'amount_formatted', className: 'text-end' }
            ]
        });
    });
</script>
@endpush