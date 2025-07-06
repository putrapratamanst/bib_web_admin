@extends('layouts.app')

@section('title', 'Create Contract')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add New Contract
        </div>
        <form autocomplete="off" method="POST" id="formCreate">
            <input type="hidden" name="status" value="approved" />
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_status" class="form-label">Contract Status<sup class="text-danger">*</sup></label>
                            <select name="contract_status" id="contract_status" class="form-control select2" data-placeholder="-- select contract status --" required>
                                <option value=""></option>
                                <option value="renewal">Renewal</option>
                                <option value="new">New</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_type_id" class="form-label">Contract Type<sup class="text-danger">*</sup></label>
                            <select name="contract_type_id" id="contract_type_id" class="form-control select2" data-placeholder="-- select contract type --" required>
                                <option value=""></option>
                                @foreach($contractTypes as $contractType)
                                <option value="{{ $contractType->id }}">{{ $contractType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contact_id" class="form-label">Contact<sup class="text-danger">*</sup></label>
                            <select name="contact_id" id="contact_id" class="form-control">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3" style="display: none;" id="covered-item-field">
                        <div class="mb-3">
                            <label for="covered-item" class="form-label">Jumlah item yang dicover<sup class="text-danger">*</sup></label>
                            <input type="number" name="number" id="covered-item" class="form-control" required />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">Contract Number<sup class="text-danger">*</sup></label>
                            <input type="text" name="number" id="number" class="form-control" required />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="policy_number" class="form-label">Policy Number<sup class="text-danger">*</sup></label>
                            <input type="text" name="policy_number" id="policy_number" class="form-control" required />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_start" class="form-label">Period Start<sup class="text-danger">*</sup></label>
                            <input type="text" name="period_start" id="period_start" class="form-control datepicker" required>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_end" class="form-label">Period End<sup class="text-danger">*</sup></label>
                            <input type="text" name="period_end" id="period_end" class="form-control datepicker" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="currency_code" class="form-label">Currency<sup class="text-danger">*</sup></label>
                            <select name="currency_code" id="currency_code" class="form-control select2" data-placeholder="-- select currency --" required>
                                <option value=""></option>
                                @foreach($currencies as $currency)
                                <option value="{{ $currency->code }}">{{ $currency->code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="exchange_rate" class="form-label">Exchange Rate<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">Rp</span>
                                <input type="text" name="exchange_rate" id="exchange_rate" class="form-control autonumeric" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="coverage_amount" class="form-label">Coverage Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">Rp</span>
                                <input type="text" name="coverage_amount" id="coverage_amount" class="form-control autonumeric" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="stamp_fee" class="form-label">Stamp Fee<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">Rp</span>
                                <input type="text" name="stamp_fee" id="stamp_fee" class="form-control autonumeric" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="gross_premium" class="form-label">Gross Premium<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">Rp</span>
                                <input type="text" name="gross_premium" id="gross_premium" class="form-control autonumeric" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="discount" class="form-label">Discount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <input type="text" name="discount" id="discount" class="form-control autonumeric" required />
                                <span class="input-group-text" style="font-size: 14px;">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">Rp</span>
                                <input type="text" id="discount_amount" class="form-control autonumeric" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Net Premium<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">Rp</span>
                                <input type="text" name="amount" id="amount" class="form-control autonumeric" required readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-md-6">
                        <div class="mb-3">
                            <label for="memo" class="form-label">Memo</label>
                            <textarea name="memo" id="memo" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="installment_count" class="form-label">Installment Count</label>
                        <div class="mb-3">
                            <select name="installment_count" id="installment_count" class="form-select">
                                @for($i = 0; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <table id="tableDetails" class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="30%">Insurance</th>
                            <th>Description</th>
                            <th width="10%">Share</th>
                            <th width="10%">Brokerage Fee</th>
                            <th width="10%">Eng Fee</th>
                            <th width="10%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select id="insurance_id_0" name="insurance_id[]" class="form-select" data-placeholder="-- select insurance --"></select>
                            </td>
                            <td>
                                <input id="description_0" type="text" class="form-control" name="description[]">
                            </td>
                            <td>
                                <input id="percentage_0" type="text" class="form-control" name="percentage[]">
                            </td>
                            <td>
                                <input id="brokerage_fee_0" type="text" class="form-control" name="brokerage_fee[]">
                            </td>
                            <td>
                                <input id="eng_fee_0" type="text" class="form-control" name="eng_fee[]">
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <button type="button" class="removeRow btn btn-outline-danger btn-sm">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddRow">Add Row</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">Save</button>
                <a href="{{ route('transaction.contracts.index') }}" class="btn btn-secondary">Cancel</a>
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

    var rowNumber = 1;

    $(document).ready(function() {
        $('#contact_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select contact --',
            ajax: {
                url: "{{ route('api.contacts.select2') }}?type=client",
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

        $("#currency_code").on("change", function() {
            var currencyCode = $(this).val();
            $(".curr-code").text(currencyCode);
        });

        $("#formCreate").submit(function(e) {
            e.preventDefault();

            var details = [];
            $('#tableDetails tbody tr').each(function() {
                var insuranceId = $(this).find('select[name="insurance_id[]"]').val();
                var description = $(this).find('input[name="description[]"]').val();
                var percentage = $(this).find('input[name="percentage[]"]').val();
                var brokerageFee = $(this).find('input[name="brokerage_fee[]"]').val();
                var engFee = $(this).find('input[name="eng_fee[]"]').val();

                details.push({
                    insurance_id: insuranceId,
                    description: description,
                    percentage: percentage,
                    brokerage_fee: brokerageFee,
                    eng_fee: engFee,
                });
            });

            var formData = {
                contract_status: $("#contract_status").val(),
                contract_type_id: $("#contract_type_id").val(),
                number: $("#number").val(),
                policy_number: $("#policy_number").val(),
                contact_id: $("#contact_id").val(),
                period_start: $("#period_start").val(),
                period_end: $("#period_end").val(),
                currency_code: $("#currency_code").val(),
                exchange_rate: $("#exchange_rate").autoNumeric('get'),
                coverage_amount: $("#coverage_amount").autoNumeric('get'),
                gross_premium: $("#gross_premium").autoNumeric('get'),
                discount: $("#discount").autoNumeric('get'),
                stamp_fee: $("#stamp_fee").autoNumeric('get'),
                amount: $("#amount").autoNumeric('get'),
                memo: $("#memo").val(),
                details: details,
                covered_item: $("#covered-item").val(),
                installment_count: $("#installment_count").val(),
            };

            $.ajax({
                url: "{{ route('api.contracts.store') }}",
                method: "POST",
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
                            window.location.href = "{{ route('transaction.contracts.index') }}";
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

        $('#btnAddRow').click(function() {
            $('#tableDetails tbody').append(`
                <tr>
                    <td>
                        <select id="insurance_id_` + rowNumber + `" name="insurance_id[]" class="form-select" data-placeholder="-- select insurance --"></select>
                    </td>
                    <td>
                        <input id="description_` + rowNumber + `" type="text" class="form-control" name="description[]">
                    </td>
                    <td>
                        <input id="percentage_` + rowNumber + `" type="text" class="form-control" name="percentage[]">
                    </td>
                    <td>
                        <input id="brokerage_fee_` + rowNumber + `" type="text" class="form-control" name="brokerage_fee[]">
                    </td>
                    <td>
                        <input id="eng_fee_` + rowNumber + `" type="text" class="form-control" name="eng_fee[]">
                    </td>
                    <td class="text-center" style="vertical-align: middle;">
                        <button type="button" class="removeRow btn btn-outline-danger btn-sm">Remove</button>
                    </td>
                </tr>
            `);

            assignInsuranceData(rowNumber.toString());
            rowNumber++;
        });

        assignInsuranceData("0");

        $("#discount").on("change", function() {
            calculateDiscount();
        });

        $("#gross_premium").on("change", function() {
            calculateDiscount();
        });

        $("#stamp_fee").on("change", function() {
            calculateDiscount();
        });
    });

    $(document).on('click', '.removeRow', function() {
        if ($('#tableDetails tbody tr').length === 1) {
            return;
        }
        $(this).closest('tr').remove();
    });

    function assignInsuranceData(number) {
        $('#insurance_id_' + number).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select insurance --',
            ajax: {
                url: "{{ route('api.contacts.select2') }}?type=insurance",
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
    }

    function calculateDiscount() {
        // check if gross premium is empty var grossPremium = 0;
        var grossPremium = 0;
        var discount = 0;
        var stampFee = 0;
        var discountAmount = 0;
        var netPremium = 0;

        if ($("#gross_premium").val() != "") {
            grossPremium = $("#gross_premium").autoNumeric('get');
        }

        if ($("#discount").val() != "") {
            discount = $("#discount").autoNumeric('get');
        }

        if ($("#stamp_fee").val() != "") {
            stampFee = $("#stamp_fee").autoNumeric('get');
        }

        if (discount > 0) {
            discountAmount = grossPremium * discount / 100;
        }

        netPremium = parseFloat(grossPremium) - parseFloat(discountAmount) + parseFloat(stampFee);

        $("#discount_amount").autoNumeric('set', discountAmount);
        $("#amount").autoNumeric('set', netPremium);
    }

    $('#contract_type_id').on('change', function() {
        const selectedVal = $(this).val();
        const $coveredItemField = $('#covered-item-field');

        if (selectedVal == 1 || selectedVal == 14) {
            $coveredItemField.show();
            $coveredItemField.find('input, select, textarea').prop('required', true);
        } else {
            $coveredItemField.hide();
            $coveredItemField.find('input, select, textarea').val('').prop('required', false);
        }
    });
</script>
@endpush