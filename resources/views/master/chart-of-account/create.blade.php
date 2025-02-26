@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add New Chart of Account
        </div>        
        <form autocomplete="off" method="POST" id="formCreate">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Code<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="code" name="code" />
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="name" name="name" />
                        </div>
                        <div class="mb-3">
                            <label for="account_category_id" class="form-label">Category<sup class="text-danger">*</sup></label>
                            <select id="account_category_id" class="form-control" name="account_category_id">
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="balance_type" class="form-label">Balance Type<sup class="text-danger">*</sup></label>
                            <select id="balance_type" class="form-control" name="balance_type">
                                <option value="DEBIT">Debit</option>
                                <option value="CREDIT">Credit</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">Save</button>
                <a href="{{ route('master.chart-of-accounts.index') }}" class="btn btn-secondary">Cancel</a>
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
        $('#account_category_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select category --',
            ajax: {
                url: "{{ route('api.account-categories.select2') }}",
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        })
                    };
                },
                minimumInputLength: 2,
            },
        });

        $("#formCreate").submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('api.chart-of-accounts.store') }}",
                method: "POST",
                data: $(this).serialize(),
                beforeSend: function() {
                    $("#btnSubmit").attr("disabled", true);
                },
                success: function(response) {
                    alert(response.message);
                    window.location.href = "{{ route('master.chart-of-accounts.index') }}";
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var firstItem = Object.keys(errors)[0];
                    var firstErrorMessage = errors[firstItem][0];

                    alert(firstErrorMessage);
                },
            });

        });
    });

    
</script>
@endpush