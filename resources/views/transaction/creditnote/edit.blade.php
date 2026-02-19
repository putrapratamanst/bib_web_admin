@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Edit Credit Note - {{ $creditNote->number }}
        </div>
        <form autocomplete="off" method="POST" id="formEdit">
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">Credit Note Number</label>
                            <input type="text" name="number" id="number" class="form-control" value="{{ $creditNote->number }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="billing_id" class="form-label">Billing<sup class="text-danger">*</sup></label>
                            <select name="billing_id" id="billing_id" class="form-control" required>
                                <option value=""></option>
                                @if($creditNote->billing)
                                    <option value="{{ $creditNote->billing->id }}" selected>
                                        {{ $creditNote->billing->billing_number }}
                                        @if($creditNote->billing->debitNote)
                                            - {{ $creditNote->billing->debitNote->number }}
                                            @if($creditNote->billing->debitNote->contract)
                                                ({{ $creditNote->billing->debitNote->contract->number }})
                                            @endif
                                        @endif
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">Credit Note Date<sup class="text-danger">*</sup></label>
                            <input type="text" name="date" id="date" class="form-control datepicker" value="{{ $creditNote->date ? $creditNote->date->format('Y-m-d') : '' }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="currency_code" class="form-label">Currency<sup class="text-danger">*</sup></label>
                            <select name="currency_code" id="currency_code" class="form-control select2" data-placeholder="-- select currency --" required>
                                <option value=""></option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->code }}" {{ $creditNote->currency_code === $currency->code ? 'selected' : '' }}>{{ $currency->code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="exchange_rate" class="form-label">Exchange Rate<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text currency-prefix" style="font-size: 14px;">{{ $creditNote->currency_code ?? 'IDR' }}</span>
                                <input type="text" name="exchange_rate" id="exchange_rate" class="form-control autonumeric" value="{{ $creditNote->exchange_rate }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text currency-prefix" style="font-size: 14px;">{{ $creditNote->currency_code ?? 'IDR' }}</span>
                                <input type="text" name="amount" id="amount" class="form-control autonumeric" value="{{ $creditNote->amount }}" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-lg-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ $creditNote->description }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="approval_status" class="form-label">Approval Status</label>
                            <div class="form-control-plaintext">
                                {!! $creditNote->approval_status_badge !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> This Credit Note can only be edited while it is in pending approval status. Once approved or rejected, it cannot be modified.
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('transaction.credit-notes.show', $creditNote->id) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" id="btnSubmit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Credit Note
                </button>
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
        function updateCurrencyPrefix() {
            var selectedCurrency = $("#currency_code").val();
            var prefix = selectedCurrency ? selectedCurrency : "IDR";
            $(".currency-prefix").text(prefix);
        }

        $('#billing_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select billing --',
            ajax: {
                url: "{{ route('api.debit-note-billings.select2') }}",
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.items
                    };
                },
                minimumInputLength: 2,
            },
        });

        $("#currency_code").on("change", updateCurrencyPrefix);
        updateCurrencyPrefix();

        $("#formEdit").submit(function(e) {
            e.preventDefault();

            var formData = {
                billing_id: $("#billing_id").val(),
                date: $("#date").val(),
                currency_code: $("#currency_code").val(),
                exchange_rate: $("#exchange_rate").autoNumeric('get'),
                amount: $("#amount").autoNumeric('get'),
                description: $("#description").val(),
                status: "{{ $creditNote->status }}",
            };

            $.ajax({
                url: "{{ route('api.credit-notes.update', $creditNote->id) }}",
                method: "PUT",
                data: formData,
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
                            window.location.href = "{{ route('transaction.credit-notes.show', $creditNote->id) }}";
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

                    $("#btnSubmit").attr("disabled", false);

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
