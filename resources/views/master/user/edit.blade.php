@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Edit User
        </div>
        <form autocomplete="off" method="POST" id="formEdit">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required />
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email<sup class="text-danger">*</sup></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="mb-3">
                            <label for="role" class="form-label">Role<sup class="text-danger">*</sup></label>
                            <select class="form-control select2" id="role" name="role" required data-placeholder="-- select role --">
                                <option value=""></option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="approver" {{ $user->role === 'approver' ? 'selected' : '' }}>Approver</option>
                                <!-- <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6" />
                            <small class="text-muted">Leave blank to keep current password. Minimum 6 characters if changing.</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">Update</button>
                <a href="{{ route('master.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
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
        $("#formEdit").submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "/api/user/{{ $user->id }}",
                method: "PUT",
                data: $(this).serialize(),
                beforeSend: function() {
                    $("#btnSubmit").attr("disabled", true);
                },
                success: function(response) {
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('master.users.index') }}";
                        }
                    });
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var firstItem = Object.keys(errors)[0];
                    var firstErrorMessage = errors[firstItem][0];
                    $("#btnSubmit").attr("disabled", false);

                    Swal.fire({
                        text: firstErrorMessage,
                        icon: "error",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                },
            });
        });
    });
</script>
@endpush
