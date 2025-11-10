@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add New Cash Bank
        </div>
        <form autocomplete="off" method="POST" id="formCreate">
            <input type="hidden" name="status" value="approved" />
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type<sup class="text-danger">*</sup></label>
                            <select name="type" id="type" class="form-control">
                                <option value=""></option>
                                <option value="receive">Receive Money</option>
                                <option value="pay">Pay Money</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="contact_id" id="labelContact" class="form-label">Contact<sup class="text-danger">*</sup></label>
                            <select name="contact_id" id="contact_id" class="form-control">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="chart_of_account_id" id="labelChartOfAccount" class="form-label">Account<sup class="text-danger">*</sup></label>
                            <select name="chart_of_account_id" id="chart_of_account_id" class="form-control">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">Number<sup class="text-danger">*</sup></label>
                            <input type="text" name="number" id="number" class="form-control" required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- <div class="col-md-3">
                        <div class="mb-3">
                            <label for="reference" class="form-label">Ref Number (Billing)</label>
                            <select name="reference" id="reference" class="form-control">
                                <option value=""></option>
                            </select>
                        </div>
                    </div> -->
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date<sup class="text-danger">*</sup></label>
                            <input type="text" name="date" id="date" class="form-control datepicker" value="{{ $currentDate }}" required />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;" id="basic-addon1">Rp</span>
                                <input type="text" name="amount" id="amount" class="form-control" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" rows="4" id="description" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">Save</button>
                <a href="{{ route('transaction.cash-banks.index') }}" class="btn btn-secondary">Cancel</a>
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

    // Debug: Check if CSRF token exists
    console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));

    // Test API call
    setTimeout(function() {
        console.log('Testing API call...');
        $.ajax({
            url: "{{ route('api.contacts.select2') }}",
            method: 'GET',
            data: {
                search: 'test',
                page: 1
            },
            success: function(response) {
                console.log('Direct API call successful:', response);
            },
            error: function(xhr, status, error) {
                console.error('Direct API call failed:', status, error, xhr.responseText);
            }
        });
    }, 1000);

    $(document).ready(function() {
        $('#type').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select type --',
        });

        $('#contact_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select contact --',
            ajax: {
                url: "{{ route('api.contacts.select2') }}",
                dataType: 'json',
                delay: 500,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                minimumInputLength: 2,
            },
        });

        $('#chart_of_account_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select chart of account --',
            ajax: {
                url: "{{ route('api.chart-of-accounts.select2') }}?c=3",
                dataType: 'json',
                delay: 500,
                data: function(params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function(data) {
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

        $('#reference').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select billing reference --',
            ajax: {
                url: "{{ route('api.debit-note-billings.select2') }}",
                dataType: 'json',
                delay: 500,
                data: function(params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.items
                    };
                },
                minimumInputLength: 2,
            },
        });

        $('#type').on('change', function() {
            var type = $(this).val();
            var labelContact = $('#labelContact');
            var labelChartOfAccount = $('#labelChartOfAccount');

            if (type == 'receive') {
                labelContact.html('From<sup class="text-danger">*</sup>');
                labelChartOfAccount.html('Deposit To<sup class="text-danger">*</sup>');
            } else if (type == 'pay') {
                labelContact.html('To<sup class="text-danger">*</sup>');
                labelChartOfAccount.html('Pay From<sup class="text-danger">*</sup>');
            }
        });

        // Auto-populate data when billing reference is selected
        $('#reference').on('select2:select', function(e) {
            var billingId = e.params.data.id;

            if (billingId) {
                // Get billing details via API
                $.ajax({
                    url: "{{ route('api.debit-note-billings.show', '') }}/" + billingId,
                    method: 'GET',
                    success: function(response) {
                        if (response.data) {
                            var billing = response.data;

                            // Auto-populate amount
                            $('#amount').val(billing.amount);

                            // Auto-populate contact if available
                            if (billing.debit_note && billing.debit_note.contract && billing.debit_note.contract.contact) {
                                var contact = billing.debit_note.contract.contact;
                                var option = new Option(contact.display_name, contact.id, true, true);
                                $('#contact_id').append(option).trigger('change');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to get billing details:', error);
                    }
                });
            }
        });

        $("#formCreate").submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('api.cash-banks.store') }}",
                method: "POST",
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
                            window.location.href = "{{ route('transaction.cash-banks.index') }}";
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