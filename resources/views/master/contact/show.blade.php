@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Detail Contact
        </div>
        <form autocomplete="off" method="POST" id="formShow">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="contact_group_id" class="form-label">Contact Group<sup class="text-danger">*</sup></label>
                            <select class="form-select" id="contact_group_id" name="contact_group_id">
                                <option value="">-- select contact group --</option>
                                @foreach($contactGroups as $cg)
                                <option value="{{ $cg->id }}" {{ $contact->contact_group_id == $cg->id ? 'selected' : '' }}>
                                    {{ $cg->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="display_name" class="form-label">Display Name<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="display_name" name="display_name" value="{{ $contact->display_name }}" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $contact->name }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type<sup class="text-danger">*</sup></label>
                            <select class="form-select" required id="type" name="type">
                                <option value="client" {{ $contact->type == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="agent" {{ $contact->type == 'agent' ? 'selected' : '' }}>Agent</option>
                                <option value="insurance" {{ $contact->type == 'insurance' ? 'selected' : '' }}>Insurance</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="{{ $contact->email }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $contact->phone }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">Save</button>
                <a href="{{ route('master.contacts.index') }}" class="btn btn-secondary">Cancel</a>
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
        $('#type').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select type --',
        });

        $('#contact_group_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select contact group --',
        });

        $("#formShow").submit(function(e) {
            e.preventDefault();

            var urlUpdate = "{{ route('api.contacts.update', $contact->id) }}";

            $.ajax({
                url: urlUpdate,
                method: "PUT",
                data: $(this).serialize(),
                beforeSend: function() {
                    $("#btnSubmit").attr("disabled", true);
                },
                success: function(response) {
                    Swal.fire({
                        // title: "Successfully Added!",
                        text: response.message,
                        icon: "success",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('master.contacts.index') }}";
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