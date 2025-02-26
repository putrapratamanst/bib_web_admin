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
                        <th>Account Code</th>
                        <th>Account Name</th>
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
                    data: 'code', 
                    name: 'code',
                    className: 'text-start',
                },
                { 
                    data: 'name', 
                    name: 'name',
                },
                {
                    data: 'account_category.name',
                }
            ]
        });
    });
</script>
@endpush