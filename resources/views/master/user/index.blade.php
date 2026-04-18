@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            User Management
            <div class="float-end">
                <a href="{{ route('master.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New User
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="user-table">
                <thead class="table-header">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th width="150">Action</th>
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
        var table = $('#user-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.users.datatables') }}",
            columns: [
                {
                    data: 'name',
                },
                {
                    data: 'email',
                },
                {
                    data: 'role_badge',
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Delete user
        $(document).on('click', '.btn-delete', function() {
            var userId = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/api/user/" + userId,
                        method: "DELETE",
                        success: function(response) {
                            Swal.fire({
                                text: response.message,
                                icon: "success",
                            });
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                text: xhr.responseJSON.message || 'Failed to delete user',
                                icon: "error",
                            });
                        },
                    });
                }
            });
        });
    });
</script>
@endpush
