@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Contacts
            <div class="float-end">
                <a href="{{ route('master.contacts.create') }}" class="btn btn-primary btn-sm">
                    Add New Contact
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="contact-table">
                <thead class="table-header">
                    <tr>
                        <th>Display Name</th>
                        <th>Group</th>
                        <th>Type</th>
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
        $('#contact-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.contacts.datatables') }}",
            columns: [
                { 
                    data: 'display_name', 
                    name: 'display_name',
                    render: function(data, type, row) {
                        return '<a href="{{ route('master.contacts.index') }}/' + row.id + '">' + data + '</a>';
                    }
                },
                {
                    data: 'contact_group',
                },
                {
                    /*
                    "type": [
                        "client"
                    ],
                    show data from type only first
                    */
                    data: 'type[0]',
                }
            ]
        });
    });
</script>
@endpush