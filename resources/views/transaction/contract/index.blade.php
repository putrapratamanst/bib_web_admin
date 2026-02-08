@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Placing
            <div class="float-end">
                <a href="{{ route('transaction.contracts.create') }}" class="btn btn-primary btn-sm">
                    Add New Placing
                </a>
            </div>
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
            <div>
                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        <div class="mb-0">
                            <label for="contract_type" class="form-label">Placing Type</label>
                            <select name="contract_type" id="contract_type" class="form-control select2">
                                <option value="">All</option>
                                @foreach($contractTypes as $contractType)
                                <option value="{{ $contractType->id }}">{{ $contractType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                        <div class="col-lg-3 col-md-4">
                            <div class="mb-0">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
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
                        <th>Currency</th>
                        <th>Nett Premium</th>
                        <th>Status</th>
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
                        d.status = $('#status').val();
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
                    data: 'currency_code',
                    name: 'currency_code',
                    className: 'text-center'
                },
                {
                    data: 'amount_formatted',
                    name: 'amount_formatted',
                    className: 'text-end'
                },
                {
                    data: 'approval_status',
                    name: 'approval_status',
                    className: 'text-center',
                    render: function(data, type, row) {
                        var badgeClass = '';
                        var statusText = '';
                        
                        if (data === 'approved') {
                            badgeClass = 'bg-success';
                            statusText = 'Approved';
                        } else if (data === 'rejected') {
                            badgeClass = 'bg-danger';
                            statusText = 'Rejected';
                        } else {
                            badgeClass = 'bg-warning';
                            statusText = 'Pending';
                        }
                        
                        return '<span class="badge ' + badgeClass + '">' + statusText + '</span>';
                    }
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

            $('#status').on('change', function() {
                table.draw();
            });
    });
</script>
@endpush