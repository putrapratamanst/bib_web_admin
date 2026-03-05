@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Contract Types
            <div class="float-end">
                <a href="{{ route('master.contract-types.create') }}" class="btn btn-primary btn-sm">
                    Add New Contract Type
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="contract-type-table">
                <thead class="table-header">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th class="text-center" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        const table = $('#contract-type-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.contract-types.datatables') }}",
            columns: [
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'name',
                    name: 'name',
                    render: function(data, type, row) {
                        const url = "{{ url('master/contract-types') }}/" + row.id + "/edit";
                        return '<a href="' + url + '" class="text-primary fw-bold">' + data + '</a>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        const editUrl = "{{ url('master/contract-types') }}/" + row.id + "/edit";
                        return `
                            <a href="${editUrl}" class="btn btn-sm btn-outline-secondary me-1">Edit</a>
                            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${row.id}" data-name="${row.name}">Delete</button>
                        `;
                    }
                }
            ]
        });

        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Delete Contract Type?',
                text: `Data ${name} will be deleted.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: "{{ url('api/contract-types') }}/" + id,
                    method: 'DELETE',
                    success: function(response) {
                        Swal.fire('Deleted!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Failed to delete Contract Type';

                        Swal.fire('Error!', message, 'error');
                    }
                });
            });
        });
    });
</script>
@endpush
