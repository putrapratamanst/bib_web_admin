@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Chart of Accounts
            <div class="float-end">
                <a href="{{ route('master.chart-of-accounts.create') }}" class="btn btn-primary btn-sm">
                    Add New Account
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="coa-table">
                <thead class="table-header">
                    <tr>
                        <th>Account Name</th>
                        <th>Prefix</th>
                        <th>Account Code</th>
                        <th>Category</th>
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
        $('#coa-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.chart-of-accounts.datatables') }}",
            columns: [
                { 
                    data: 'name', 
                    name: 'name',
                    render: function(data, type, row) {
                        var url = "{{ url('master/chart-of-accounts') }}/" + row.id + "/edit";
                        return '<a href="' + url + '" class="text-primary fw-bold">' + data + '</a>';
                    }
                },
                { 
                    data: 'prefix', 
                    name: 'prefix',
                },
                { 
                    data: 'code', 
                    name: 'code',
                    className: 'text-start',
                },
                {
                    data: 'account_category.name',
                }
            ]
        });
    });
</script>
@endpush