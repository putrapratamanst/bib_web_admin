@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Contract
            <div class="float-end">
                <a href="{{ route('transaction.contracts.create') }}" class="btn btn-primary btn-sm">
                    Add New Contract
                </a>
            </div>
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
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
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="contract-table">
                <thead class="table-header">
                    <tr>
                        <th>Number</th>
                        <th>Policy Number</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Period</th>
                        <th>Amount</th>
                        <th>Covered Item</th>
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
        var table = $('#contract-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.contracts.datatables') }}",
                data: function(d) {
                    d.contract_type = $('#contract_type').val();
                },
            },
            columns: [{
                    data: 'number',
                    name: 'number',
                    render: function(data, type, row) {
                        return '<a href="/transaction/contracts/' + row.id + '">' + data + '</a>';
                    }
                },
                {
                    data: 'policy_number',
                    name: 'policy_number',
                    searchable: true,
                    className: 'text-left',
                    render: function(data, type, row) {
                        return data == 0 ? '-' : data;
                    }
                },
                {
                    data: 'contract_type',
                    name: 'contract_type'
                },
                {
                    data: 'contact',
                    name: 'contact'
                },
                {
                    data: 'period',
                    name: 'period'
                },
                {
                    data: 'amount_formatted',
                    name: 'amount_formatted',
                    className: 'text-end'
                },
                {
                    data: 'covered_item',
                    name: 'covered_item',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (data > 0) {
                            if (row.contract_type_id == 1) {
                                return `<a href="/transaction/contracts/add-unit/automobile/${row.id}">${data} unit</a>`;
                            } else if (row.contract_type_id == 14) {
                                return `<a href="/transaction/contracts/add-unit/property/${row.id}">${data} location</a>`;
                            }
                        }
                        return '-';
                    }
                }
            ]
        });

        $('#contract_type').on('change', function() {
            table.draw();
        });
    });
</script>
@endpush