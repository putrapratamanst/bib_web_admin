@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Contact Groups
            <div class="float-end">
                <a href="{{ route('master.contact-groups.create') }}" class="btn btn-primary btn-sm">
                    Add New Contact Group
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="contact-group-table">
                <thead class="table-header">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
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
        $('#contact-group-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.contact-groups.datatables') }}",
            columns: [
                {
                    data: 'name',
                },
                {
                    data: 'description',
                },
            ]
        });
    });
</script>
@endpush