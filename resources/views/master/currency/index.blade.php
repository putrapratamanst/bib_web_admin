@extends('layouts.app')

@section('title', 'Currencies')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Currencies</span>
            <a href="{{ route('master.currencies.create') }}" class="btn btn-primary">Add Currency</a>
        </div>
        <div class="card-body">
            <table id="currenciesTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Actions</th>
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
    $('#currenciesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("master.currencies.datatables") }}',
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush