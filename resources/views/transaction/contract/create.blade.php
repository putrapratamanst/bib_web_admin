@extends('layouts.app')

@section('title', 'Create Placing')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add New Placing
        </div>
        <form autocomplete="off" method="POST" id="formCreate">
            <input type="hidden" name="status" value="approved" />
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_status" class="form-label">Placing Status<sup class="text-danger">*</sup></label>
                            <select name="contract_status" id="contract_status" class="form-control select2" data-placeholder="-- select placing status --" required>
                                <option value=""></option>
                                <option value="renewal">Renewal</option>
                                <option value="new">New</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_type_id" class="form-label">Placing Type<sup class="text-danger">*</sup></label>
                            <select name="contract_type_id" id="contract_type_id" class="form-control select2" data-placeholder="-- select placing type --" required>
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
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="billing_address_id" class="form-label">Billing Address</label>
                            <select name="billing_address_id" id="billing_address_id" class="form-control select2" data-placeholder="-- select billing address --">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
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
                            <label for="number" class="form-label">Placing Number<sup class="text-danger">*</sup></label>
                            <input type="text" name="number" id="number" class="form-control" readonly style="background-color: #e9ecef;" required />
                            <small class="text-muted">Auto-generated when insurance is selected</small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="policy_number" class="form-label">Policy Number<sup class="text-danger">*</sup></label>
                            <input type="text" name="policy_number" id="policy_number" class="form-control" required />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="insured_name" class="form-label">Insured Name</label>
                            <input type="text" id="insured_name" class="form-control" readonly style="background-color: #e9ecef !important;">
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="correspondence_address" class="form-label">Correspondence Address</label>
                            <input type="text" id="correspondence_address" class="form-control" readonly style="background-color: #e9ecef !important;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_start" class="form-label">Period Start<sup class="text-danger">*</sup></label>
                            <input type="text" name="period_start" id="period_start" class="form-control datepicker" required>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_end" class="form-label">Period End<sup class="text-danger">*</sup></label>
                            <input type="text" name="period_end" id="period_end" class="form-control datepicker">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_duration" class="form-label">Period Duration</label>
                            <input type="text" id="period_duration" class="form-control" readonly style="background-color: #e9ecef;" placeholder="0 days">
                            <small class="text-muted">Auto-calculated</small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="created_at" class="form-label">Created Date<sup class="text-danger">*</sup></label>
                            <input type="text" name="created_at" id="created_at" class="form-control datepicker" value="{{ date('d-m-Y') }}" required>
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
                            <label for="exchange_rate" class="form-label">Exchange Rate</label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;"></span>
                                <input type="text" name="exchange_rate" id="exchange_rate" class="form-control autonumeric" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="coverage_amount" class="form-label">Total Sum Insured (TSI)<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;"></span>
                                <input type="text" name="coverage_amount" id="coverage_amount" class="form-control autonumeric" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="stamp_fee" class="form-label">Stamp Fee<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;"></span>
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
                                <span class="input-group-text curr-code" style="font-size: 14px;"></span>
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
                                <span class="input-group-text curr-code" style="font-size: 14px;"></span>
                                <input type="text" id="discount_amount" class="form-control autonumeric" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Net Premium<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;"></span>
                                <input type="text" name="amount" id="amount" class="form-control autonumeric" required readonly />
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="policy_fee" class="form-label">Policy Fee</label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;"></span>
                                <input type="text" name="policy_fee" id="policy_fee" class="form-control autonumeric" />
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
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="documents" class="form-label">Upload Documents (PDF, XLS, XLSX, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG)</label>
                            <input type="file" name="documents" id="documents" class="form-control" multiple accept=".pdf,.xlsx,.xls,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png" />
                            <small class="text-muted">Max file size: 10MB per file. Multiple files allowed.</small>
                        </div>
                        <div id="documentPreview"></div>
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

                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mb-3">Endorsement / Placing Reference</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contract_reference_id" class="form-label">Placing Reference</label>
                                    <select id="contract_reference_id" name="contract_reference_id[]" class="form-select" data-placeholder="-- select contract reference --" multiple></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="endorsement_number" class="form-label">Endorsement No</label>
                                    <input id="endorsement_number" type="text" class="form-control" name="endorsement_number" placeholder="Enter endorsement number">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mb-3">Insurance Details</h6>
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
                                <select id="description_0" name="description[]" class="form-select" required>
                                    <option value="">-- select type --</option>
                                    <option value="Leader">Leader</option>
                                    <option value="Member">Member</option>
                                </select>
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
        // Set default required for period_end
        $('#period_end').prop('required', true);
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

        // Initialize Select2 for billing address
        $('#billing_address_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select billing address --',
            allowClear: true,
            ajax: {
                url: '{{ route("api.billing-addresses.select2") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    const contactId = $('#contact_id').val();
                    return {
                        search: params.term,
                        page: params.page || 1,
                        contact_id: contactId
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data,
                        pagination: {
                            more: data.pagination && data.pagination.more
                        }
                    };
                },
                cache: true
            },
            templateResult: function(data) {
                if (!data.id) return data.text;
                if (data.is_primary) {
                    return $('<span>' + data.text + ' <span class="badge bg-info ms-2">Primary</span></span>');
                }
                return data.text;
            },
            templateSelection: function(data) {
                if (!data.id) return data.text;
                if (data.is_primary) {
                    return data.text + ' ★';
                }
                return data.text;
            }
        });

        // Reset billing address when contact changes
        $('#contact_id').on('change', function() {
            $('#billing_address_id').val(null).trigger('change');
            $('#insured_name').val('');
            $('#correspondence_address').val('');
        });

        // Handle billing address change to populate insured name and correspondence address
        $('#billing_address_id').on('select2:select', function(e) {
            var data = e.params.data;
            if (data && data.id) {
                // Get billing address details from API
                $.get('/api/billing-address/' + data.id, function(response) {
                    if (response.data) {
                        $('#insured_name').val(response.data.name || '');
                        $('#correspondence_address').val(response.data.address || '');
                    }
                }).fail(function() {
                    $('#insured_name').val('');
                    $('#correspondence_address').val('');
                });
            }
        });

        $('#billing_address_id').on('select2:clear select2:unselect', function() {
            $('#insured_name').val('');
            $('#correspondence_address').val('');
        });

        $("#currency_code").on("change", function() {
            var currencyCode = $(this).val();
            $(".curr-code").text(currencyCode);
        });

        $("#formCreate").submit(function(e) {
            e.preventDefault();

            // Validate billing address
            var billingAddressId = $('#billing_address_id').val();
            console.log('DEBUG billing_address_id:', billingAddressId);
            if (!billingAddressId) {
                Swal.fire({
                    text: 'Billing address tidak ditemukan untuk kontak ini. Silakan cek data kontak.',
                    icon: 'error',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
                return;
            }

            var details = [];
            $('#tableDetails tbody tr').each(function() {
                var insuranceId = $(this).find('select[name="insurance_id[]"]').val();
                var description = $(this).find('select[name="description[]"]').val();
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

            var endorsements = [];
            var contractReferenceIds = $('#contract_reference_id').val(); // array of selected IDs
            var endorsementNumber = $('#endorsement_number').val();

            if (contractReferenceIds && contractReferenceIds.length > 0) {
                contractReferenceIds.forEach(function(refId) {
                    endorsements.push({
                        contract_reference_id: refId,
                        endorsement_number: endorsementNumber,
                    });
                });
            }

            var formData = {
                contract_status: $("#contract_status").val(),
                contract_type_id: $("#contract_type_id").val(),
                number: $("#number").val(),
                policy_number: $("#policy_number").val(),
                policy_fee: $("#policy_fee").autoNumeric('get'),
                contact_id: $("#contact_id").val(),
                billing_address_id: billingAddressId,
                endorsements: endorsements,
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
                    console.log('Contract created successfully:', response);

                    // Upload documents if any
                    uploadDocuments(response.data.id, function(uploadSuccess) {
                        if (uploadSuccess || uploadSuccess === undefined) {
                            // Success or no files uploaded
                            var message = response.message;
                            var files = document.getElementById('documents').files;
                            if (files.length > 0) {
                                message += '\nDocuments uploaded successfully!';
                            }

                            Swal.fire({
                                text: message,
                                icon: "success",
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('transaction.contracts.index') }}";
                                }
                            });
                        } else {
                            // Document upload failed but contract was created
                            Swal.fire({
                                title: 'Contract Created',
                                text: 'Contract created successfully but document upload failed. You can upload documents later.',
                                icon: 'warning',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('transaction.contracts.index') }}";
                                }
                            });
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
                    <select id="description_` + rowNumber + `" name="description[]" class="form-select" required>
                            <option value="">-- select type --</option>
                            <option value="Leader">Leader</option>
                            <option value="Member">Member</option>
                        </select>
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

        // Initialize Select2 for contract reference
        $('#contract_reference_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select contract reference --',
            allowClear: true,
            ajax: {
                url: "{{ route('api.contracts.select2') }}",
                dataType: 'json',
                delay: 250,
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
                cache: true
            }
        });

        // Generate contract number when contract type is selected
        $("#contract_type_id").on("change", function() {
            tryGenerateContractNumber();
        });

        $("#discount").on("change", function() {
            calculateDiscount();
        });

        $("#gross_premium").on("change", function() {
            calculateDiscount();
        });

        $("#stamp_fee").on("change", function() {
            calculateDiscount();
        });

        $("#policy_fee").on("change", function() {
            calculateDiscount();
        });

        // Calculate period duration
        function calculatePeriodDuration() {
            var startDate = $("#period_start").datepicker('getDate');
            var endDate = $("#period_end").datepicker('getDate');

            if (startDate && endDate) {
                var timeDiff = endDate.getTime() - startDate.getTime();
                var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

                if (daysDiff >= 0) {
                    $("#period_duration").val(daysDiff + " days");
                } else {
                    $("#period_duration").val("Invalid date range");
                }
            } else {
                $("#period_duration").val("0");
            }
        }

        $("#period_start").on("change", function() {
            calculatePeriodDuration();
        });

        $("#period_end").on("change", function() {
            calculatePeriodDuration();
        });

        // Document upload preview
        $("#documents").on("change", function() {
            let files = this.files;
            let preview = $("#documentPreview");
            preview.empty();

            if (files.length > 0) {
                preview.append('<div class="mt-3"><strong>Selected files:</strong></div>');
                preview.append('<ul class="list-group mt-2" id="fileList"></ul>');

                let fileList = $("#fileList");
                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    let fileSize = (file.size / (1024 * 1024)).toFixed(2);

                    if (fileSize > 10) {
                        fileList.append('<li class="list-group-item text-danger">' + file.name + ' (' + fileSize + 'MB) - <strong>Exceeds 10MB limit</strong></li>');
                    } else {
                        fileList.append('<li class="list-group-item">' + file.name + ' (' + fileSize + 'MB)</li>');
                    }
                }
            }
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
    }

    function tryGenerateContractNumber() {
        // Check if contract type is selected
        var contractTypeId = $("#contract_type_id").val();
        if (!contractTypeId) {
            return;
        }

        // Generate contract number based on contract type
        generateContractNumber(contractTypeId);
    }

    function generateContractNumber(contractTypeId) {
        $.ajax({
            url: "{{ route('api.contracts.generate-number') }}",
            method: "GET",
            data: {
                contract_type_id: contractTypeId
            },
            success: function(response) {
                $("#number").val(response.data.number);
            },
            error: function(xhr) {
                console.error('Failed to generate contract number:', xhr);
            }
        });
    }

    function calculateDiscount() {
        // check if gross premium is empty var grossPremium = 0;
        var grossPremium = 0;
        var discount = 0;
        var stampFee = 0;
        var policyFee = 0;
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

        if ($("#policy_fee").val() != "") {
            policyFee = $("#policy_fee").autoNumeric('get');
        }

        if (discount > 0) {
            discountAmount = grossPremium * discount / 100;
        }

        netPremium = parseFloat(grossPremium) - parseFloat(discountAmount) + parseFloat(policyFee) + parseFloat(stampFee);

        $("#discount_amount").autoNumeric('set', discountAmount);
        $("#amount").autoNumeric('set', netPremium);
    }

    $('#contract_type_id').on('change', function() {
        const selectedVal = $(this).val();
        const selectedText = $(this).find('option:selected').text();
        const $coveredItemField = $('#covered-item-field');
        const $periodEndField = $('#period_end');

        if (selectedVal == 1 || selectedVal == 14) {
            $coveredItemField.show();
            $coveredItemField.find('input, select, textarea').prop('required', true);
        } else {
            $coveredItemField.hide();
            $coveredItemField.find('input, select, textarea').val('').prop('required', false);
        }

        // For Marine Cargo Export & Import, make period_end not required
        if (selectedText === 'MARINE CARGO EXPORT INSURANCE' || selectedText === 'MARINE CARGO IMPORT INSURANCE' || selectedText === 'IN LAND TRANSIT INSURANCE') {
            $periodEndField.prop('required', false);
            $periodEndField.closest('.mb-3').find('label sup').hide(); // Hide the asterisk
        } else {
            $periodEndField.prop('required', true);
            $periodEndField.closest('.mb-3').find('label sup').show(); // Show the asterisk
        }
    });

    function uploadDocuments(contractId, callback) {
        var files = document.getElementById('documents').files;

        console.log('uploadDocuments called with contractId:', contractId);
        console.log('Number of files:', files.length);

        if (files.length === 0) {
            console.log('No files to upload, calling callback immediately');
            callback(true);
            return;
        }

        var formData = new FormData();
        for (var i = 0; i < files.length; i++) {
            console.log('Adding file to FormData:', files[i].name, 'Size:', files[i].size);
            formData.append('documents[]', files[i]);
        }

        console.log('Sending AJAX request to:', '/api/contract/' + contractId + '/documents');

        $.ajax({
            url: '/api/contract/' + contractId + '/documents',
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('✓ Documents uploaded successfully:', response);
                callback(true);
            },
            error: function(xhr) {
                console.error('✗ Error uploading documents:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseJSON);
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    console.error('Error message:', xhr.responseJSON.message);
                }
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    console.error('Validation errors:', xhr.responseJSON.errors);
                }

                // Show error to user
                Swal.fire({
                    title: 'Document Upload Failed',
                    text: xhr.responseJSON?.message || 'Failed to upload documents. Please try again.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });

                callback(false);
            },
        });
    }
</script>
@endpush