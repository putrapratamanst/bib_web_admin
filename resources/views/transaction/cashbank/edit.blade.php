@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Edit Cash Bank
        </div>
        <form autocomplete="off" method="POST" id="formEdit">
            <input type="hidden" name="status" value="{{ $cashBank->status }}" />
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type<sup class="text-danger">*</sup></label>
                            <select name="type" id="type" class="form-control">
                                <option value=""></option>
                                <option value="receive" {{ $cashBank->type == 'receive' ? 'selected' : '' }}>Receive Money</option>
                                <option value="pay" {{ $cashBank->type == 'pay' ? 'selected' : '' }}>Pay Money</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="transaction_type" class="form-label">Transaction Type<sup class="text-danger">*</sup></label>
                            <select name="transaction_type" id="transaction_type" class="form-control" data-placeholder="-- Select Transaction Type --">
                                <option value="bank_transaction" {{ $cashBank->transaction_type == 'bank_transaction' ? 'selected' : '' }}>Bank Transaction</option>
                                <option value="bank_to_account" {{ $cashBank->transaction_type == 'bank_to_account' ? 'selected' : '' }}>Bank to Account</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="contact_id" id="labelContact" class="form-label">Contact<sup class="text-danger">*</sup></label>
                            <select name="contact_id" id="contact_id" class="form-control">
                                <option value="{{ $cashBank->contact_id }}" selected>
                                    {{ $cashBank->contact->display_name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="chart_of_account_id" id="labelChartOfAccount" class="form-label">Account<sup class="text-danger">*</sup></label>
                            <select name="chart_of_account_id" id="chart_of_account_id" class="form-control">
                                <option value="{{ $cashBank->chart_of_account_id }}" selected>
                                    {{ $cashBank->chartOfAccount->display_name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" id="contra_account_wrapper" style="{{ $cashBank->transaction_type == 'bank_to_account' ? 'display: block;' : 'display: none;' }}">
                        <div class="mb-3">
                            <label for="contra_account_id" class="form-label">Contra Account<sup class="text-danger">*</sup></label>
                            <select name="contra_account_id" id="contra_account_id" class="form-control">
                                @if($cashBank->contraAccount)
                                    <option value="{{ $cashBank->contra_account_id }}" selected>
                                        {{ $cashBank->contraAccount->display_name }}
                                    </option>
                                @else
                                    <option value=""></option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">Number<sup class="text-danger">*</sup></label>
                            <input type="text" name="number" id="number" class="form-control" value="{{ $cashBank->number }}" required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date<sup class="text-danger">*</sup></label>
                            <input type="text" name="date" id="date" class="form-control datepicker" value="{{ $cashBank->display_date }}" required />
                        </div>
                    </div>
                    <input type="hidden" name="currency_code" id="currency_code" value="IDR" />
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="exchange_rate" class="form-label">Exchange Rate</label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">IDR</span>
                                <input type="text" name="exchange_rate" id="exchange_rate" class="form-control autonumeric" value="{{ $cashBank->exchange_rate ?? 1 }}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">IDR</span>
                                <input type="text" name="amount" id="amount" class="form-control autonumeric" value="{{ $cashBank->amount }}" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ $cashBank->description }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">Update</button>
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

    $(document).ready(function() {
        // Initialize datepicker
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // Initialize autonumeric
        $('.autonumeric').autoNumeric('init', {
            aSep: ',',
            aDec: '.',
            mDec: 2
        });

        // Contact Select2
        $('#contact_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Select Contact --',
            ajax: {
                url: "{{ route('api.contacts.select2') }}",
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
            },
        });

        // Chart of Account Select2
        $('#chart_of_account_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Select Account --',
            ajax: {
                url: "{{ route('api.chart-of-accounts.select2') }}",
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
            }
        });

        // Contra Account Select2
        $('#contra_account_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Select Contra Account --',
            ajax: {
                url: "{{ route('api.chart-of-accounts.select2') }}",
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
            }
        });

        // Show/hide contra account based on transaction type
        $('#transaction_type').change(function() {
            if ($(this).val() === 'bank_to_account') {
                $('#contra_account_wrapper').show();
            } else {
                $('#contra_account_wrapper').hide();
                $('#contra_account_id').val('').trigger('change');
            }
        });

        // Form submission
        $("#formEdit").submit(function(e) {
            e.preventDefault();

            // Convert amount from formatted to numeric
            var formData = $(this).serializeArray();
            formData.forEach(function(item) {
                if (item.name === 'amount') {
                    item.value = $('#amount').autoNumeric('get');
                }
                if (item.name === 'exchange_rate') {
                    item.value = $('#exchange_rate').autoNumeric('get');
                }
            });

            $.ajax({
                url: "{{ route('api.cash-banks.update', $cashBank->id) }}",
                method: "PUT",
                data: $.param(formData),
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
                    var firstErrorMessage = 'Update failed';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var firstItem = Object.keys(errors)[0];
                            firstErrorMessage = errors[firstItem][0];
                        } else if (xhr.responseJSON.message) {
                            firstErrorMessage = xhr.responseJSON.message;
                        }
                    }

                    Swal.fire({
                        text: firstErrorMessage,
                        icon: "error",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                },
                complete: function() {
                    $("#btnSubmit").attr("disabled", false);
                }
            });
        });
    });
</script>
@endpush