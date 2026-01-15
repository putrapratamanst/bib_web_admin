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
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filter-type" class="form-select">
                        <option value="">-- Filter Type Contact --</option>
                        @foreach(\App\Models\ContactType::select('type')->distinct()->get() as $ct)
                            <option value="{{ $ct->type }}">{{ ucfirst($ct->type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
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
        var table = $('#contact-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.contacts.datatables') }}",
                data: function(d) {
                    d.type = $('#filter-type').val();
                }
            },
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
                    data: 'type[0]',
                }
            ]
        });

        $('#filter-type').on('change', function() {
            table.ajax.reload();
        });
    });
</script>
@endpush