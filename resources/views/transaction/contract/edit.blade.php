@extends('layouts.app')

@section('title', 'Edit Placing')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Edit Placing</span>
            @php
                $badgeClass = match($contract->approval_status) {
                    'approved' => 'bg-success',
                    'rejected' => 'bg-danger',
                    default => 'bg-warning'
                };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ ucfirst($contract->approval_status) }}</span>
        </div>
        <form autocomplete="off" method="POST" id="formEdit">
            <input type="hidden" name="contract_id" id="contract_id" value="{{ $contract->id }}" />
            <div class="card-body">
                @if($contract->approval_status === 'rejected' && $contract->rejection_reason)
                <div class="alert alert-warning">
                    <strong>Rejection Reason:</strong> {{ $contract->rejection_reason }}
                </div>
                @endif

                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_status" class="form-label">Placing Status<sup class="text-danger">*</sup></label>
                            <select name="contract_status" id="contract_status" class="form-control select2" data-placeholder="-- select placing status --" required>
                                <option value=""></option>
                                <option value="renewal" {{ $contract->contract_status === 'renewal' ? 'selected' : '' }}>Renewal</option>
                                <option value="new" {{ $contract->contract_status === 'new' ? 'selected' : '' }}>New</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_type_id" class="form-label">Placing Type<sup class="text-danger">*</sup></label>
                            <select name="contract_type_id" id="contract_type_id" class="form-control select2" data-placeholder="-- select placing type --" required>
                                <option value=""></option>
                                @foreach($contractTypes as $contractType)
                                <option value="{{ $contractType->id }}" {{ $contract->contract_type_id == $contractType->id ? 'selected' : '' }}>{{ $contractType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contact_id" class="form-label">Contact<sup class="text-danger">*</sup></label>
                            <select name="contact_id" id="contact_id" class="form-control">
                                <option value="{{ $contract->contact_id }}" selected>{{ $contract->contact->display_name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_reference_id" class="form-label">Placing Reference / Endorsement</label>
                            <select name="contract_reference_id" id="contract_reference_id" class="form-control">
                                @if($contract->contract_reference_id)
                                <option value="{{ $contract->contract_reference_id }}" selected>{{ $contract->contractReference->number }}</option>
                                @else
                                <option value=""></option>
                                @endif
                            </select>
                            <small class="text-muted">Optional - Select original placing for endorsement</small>
                        </div>
                    </div>

                    <div class="col-lg-3" style="{{ in_array($contract->contract_type_id, [1, 14]) ? '' : 'display: none;' }}" id="covered-item-field">
                        <div class="mb-3">
                            <label for="covered-item" class="form-label">Jumlah item yang dicover<sup class="text-danger">*</sup></label>
                            <input type="number" name="covered_item" id="covered-item" class="form-control" value="{{ $contract->covered_item }}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">Placing Number<sup class="text-danger">*</sup></label>
                            <input type="text" name="number" id="number" class="form-control" value="{{ $contract->number }}" readonly style="background-color: #e9ecef;" required />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="policy_number" class="form-label">Policy Number<sup class="text-danger">*</sup></label>
                            <input type="text" name="policy_number" id="policy_number" class="form-control" value="{{ $contract->policy_number }}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_start" class="form-label">Period Start<sup class="text-danger">*</sup></label>
                            <input type="text" name="period_start" id="period_start" class="form-control datepicker" value="{{ $contract->period_start ? $contract->period_start->format('d-m-Y') : '' }}" required>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_end" class="form-label">Period End<sup class="text-danger">*</sup></label>
                            <input type="text" name="period_end" id="period_end" class="form-control datepicker" value="{{ $contract->period_end ? $contract->period_end->format('d-m-Y') : '' }}" required>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_duration" class="form-label">Period Duration</label>
                            <input type="text" id="period_duration" class="form-control" readonly style="background-color: #e9ecef;" value="{{ $contract->period_start->diffInDays($contract->period_end) }} days">
                            <small class="text-muted">Auto-calculated</small>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="created_at" class="form-label">Created Date<sup class="text-danger">*</sup></label>
                            <input type="text" name="created_at" id="created_at" class="form-control datepicker" value="{{ $contract->created_at ? $contract->created_at->format('d-m-Y') : '' }}" required>
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
                                <option value="{{ $currency->code }}" {{ $contract->currency_code === $currency->code ? 'selected' : '' }}>{{ $currency->code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="exchange_rate" class="form-label">Exchange Rate<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">{{ $contract->currency_code }}</span>
                                <input type="text" name="exchange_rate" id="exchange_rate" class="form-control autonumeric" value="{{ $contract->exchange_rate }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="coverage_amount" class="form-label">Coverage Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">{{ $contract->currency_code }}</span>
                                <input type="text" name="coverage_amount" id="coverage_amount" class="form-control autonumeric" value="{{ $contract->coverage_amount }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="stamp_fee" class="form-label">Stamp Fee<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">{{ $contract->currency_code }}</span>
                                <input type="text" name="stamp_fee" id="stamp_fee" class="form-control autonumeric" value="{{ $contract->stamp_fee }}" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="gross_premium" class="form-label">Gross Premium<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">{{ $contract->currency_code }}</span>
                                <input type="text" name="gross_premium" id="gross_premium" class="form-control autonumeric" value="{{ $contract->gross_premium }}" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="discount" class="form-label">Discount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <input type="text" name="discount" id="discount" class="form-control autonumeric" value="{{ $contract->discount }}" required />
                                <span class="input-group-text" style="font-size: 14px;">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">{{ $contract->currency_code }}</span>
                                <input type="text" id="discount_amount" class="form-control autonumeric" value="{{ $contract->discount_amount }}" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Net Premium<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text curr-code" style="font-size: 14px;">{{ $contract->currency_code }}</span>
                                <input type="text" name="amount" id="amount" class="form-control autonumeric" value="{{ $contract->amount }}" required readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-md-6">
                        <div class="mb-3">
                            <label for="memo" class="form-label">Memo</label>
                            <textarea name="memo" id="memo" class="form-control" rows="3">{{ $contract->memo }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="installment_count" class="form-label">Installment Count</label>
                        <div class="mb-3">
                            <select name="installment_count" id="installment_count" class="form-select">
                                @for($i = 0; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $contract->installment_count == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="mb-3">Documents</h6>
                        <div id="documentsContainer">
                            <div class="alert alert-info">
                                <i class="bi bi-hourglass"></i> Loading documents...
                            </div>
                        </div>
                        
                        <div class="mt-3 mb-3">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                <i class="bi bi-cloud-upload"></i> Add Documents
                            </button>
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
                        @foreach($contract->details as $index => $detail)
                        <tr>
                            <td>
                                <select id="insurance_id_{{ $index }}" name="insurance_id[]" class="form-select" data-placeholder="-- select insurance --">
                                    <option value="{{ $detail->insurance_id }}" selected>{{ $detail->insurance->display_name }}</option>
                                </select>
                            </td>
                            <td>
                                <select id="description_{{ $index }}" name="description[]" class="form-select" required>
                                    <option value="">-- select type --</option>
                                    <option value="Leader" {{ $detail->description === 'Leader' ? 'selected' : '' }}>Leader</option>
                                    <option value="Member" {{ $detail->description === 'Member' ? 'selected' : '' }}>Member</option>
                                </select>
                            </td>
                            <td>
                                <input id="percentage_{{ $index }}" type="text" class="form-control" name="percentage[]" value="{{ $detail->percentage }}">
                            </td>
                            <td>
                                <input id="brokerage_fee_{{ $index }}" type="text" class="form-control" name="brokerage_fee[]" value="{{ $detail->brokerage_fee }}">
                            </td>
                            <td>
                                <input id="eng_fee_{{ $index }}" type="text" class="form-control" name="eng_fee[]" value="{{ $detail->eng_fee }}">
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <button type="button" class="removeRow btn btn-outline-danger btn-sm">Remove</button>
                            </td>
                        </tr>
                        @endforeach
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
                <button type="submit" id="btnSubmit" class="btn btn-primary">Update</button>
                <a href="{{ route('transaction.contracts.show', $contract->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadDocumentForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="documents" class="form-label">Select Documents</label>
                        <input type="file" class="form-control" id="documents" name="documents[]" multiple accept=".pdf,.xlsx,.xls,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png" required>
                        <small class="text-muted">Max file size: 10MB per file. Allowed: PDF, Office files, Images</small>
                    </div>
                    <div id="documentPreview"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnUploadDocuments">Upload</button>
            </div>
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

    var rowNumber = {{ count($contract->details) }};

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
                minimumInputLength: 0,
            },
        });

        $('#contract_reference_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select contract reference (optional) --',
            allowClear: true,
            ajax: {
                url: "{{ route('api.contracts.select2') }}",
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
                minimumInputLength: 0,
            },
        });

        // Initialize select2 for existing insurance dropdowns
        @foreach($contract->details as $index => $detail)
        $('#insurance_id_{{ $index }}').select2({
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
        @endforeach

        $("#currency_code").on("change", function() {
            var currencyCode = $(this).val();
            $(".curr-code").text(currencyCode);
        });

        $("#formEdit").submit(function(e) {
            e.preventDefault();

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

            var contractId = $("#contract_id").val();
            var formData = {
                contract_status: $("#contract_status").val(),
                contract_type_id: $("#contract_type_id").val(),
                number: $("#number").val(),
                policy_number: $("#policy_number").val(),
                contact_id: $("#contact_id").val(),
                contract_reference_id: $("#contract_reference_id").val() || null,
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
                url: "/api/contract/" + contractId,
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
                            window.location.href = "{{ route('transaction.contracts.show', $contract->id) }}";
                        }
                    });
                },
                error: function(xhr) {
                    var errorMessage = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var firstItem = Object.keys(errors)[0];
                        errorMessage = errors[firstItem][0];
                    }
                    $("#btnSubmit").attr("disabled", false);

                    Swal.fire({
                        text: errorMessage,
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

        $("#discount").on("change", function() {
            calculateDiscount();
        });

        $("#gross_premium").on("change", function() {
            calculateDiscount();
        });

        $("#stamp_fee").on("change", function() {
            calculateDiscount();
        });

        // Load documents on page load
        loadDocuments();
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

    function calculateDiscount() {
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
            $("#period_duration").val("0 days");
        }
    }

    $("#period_start").on("change", function() {
        calculatePeriodDuration();
    });

    $("#period_end").on("change", function() {
        calculatePeriodDuration();
    });

    function loadDocuments() {
        $.ajax({
            url: '/api/contract/{{ $contract->id }}/documents',
            method: 'GET',
            success: function(response) {
                let documents = response.data;
                let container = $('#documentsContainer');
                container.empty();

                if (documents.length === 0) {
                    container.html('<div class="alert alert-info">No documents uploaded yet</div>');
                } else {
                    let html = '<div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Filename</th><th>Size</th><th>Uploaded</th><th>Action</th></tr></thead><tbody>';
                    
                    documents.forEach(function(doc) {
                        html += '<tr>' +
                                '<td><i class="bi bi-file"></i> ' + doc.filename + '</td>' +
                                '<td>' + doc.file_size_formatted + '</td>' +
                                '<td>' + doc.uploaded_at + '</td>' +
                                '<td>' +
                                '<a href="/api/contract/{{ $contract->id }}/documents/' + doc.id + '/download" class="btn btn-sm btn-info" title="Download"><i class="bi bi-download"></i></a> ' +
                                '<button class="btn btn-sm btn-danger btnDeleteDocument" data-id="' + doc.id + '" data-name="' + doc.filename + '" title="Delete"><i class="bi bi-trash"></i></button>' +
                                '</td>' +
                                '</tr>';
                    });
                    
                    html += '</tbody></table></div>';
                    container.html(html);
                }
            },
            error: function(xhr) {
                $('#documentsContainer').html('<div class="alert alert-danger">Failed to load documents</div>');
            }
        });
    }

    // Upload documents
    $('#btnUploadDocuments').on('click', function() {
        var formData = new FormData();
        var files = $('#documents')[0].files;

        if (files.length === 0) {
            alert('Please select at least one file');
            return;
        }

        for (var i = 0; i < files.length; i++) {
            formData.append('documents[]', files[i]);
        }

        $.ajax({
            url: '/api/contract/{{ $contract->id }}/documents',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#btnUploadDocuments').prop('disabled', true).text('Uploading...');
            },
            success: function(response) {
                $('#uploadDocumentModal').modal('hide');
                $('#uploadDocumentForm')[0].reset();
                loadDocuments();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Documents uploaded successfully'
                });
            },
            error: function(xhr) {
                var message = xhr.responseJSON?.message || 'Failed to upload documents';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            },
            complete: function() {
                $('#btnUploadDocuments').prop('disabled', false).text('Upload');
            }
        });
    });

    // Delete document
    $(document).on('click', '.btnDeleteDocument', function() {
        var documentId = $(this).data('id');
        var documentName = $(this).data('name');

        Swal.fire({
            title: 'Delete Document?',
            text: 'Are you sure you want to delete: ' + documentName + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/api/contract/{{ $contract->id }}/documents/' + documentId,
                    method: 'DELETE',
                    success: function(response) {
                        loadDocuments();
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: 'Document deleted successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete document'
                        });
                    }
                });
            }
        });
    });

    // Document preview
    $("#documents").on("change", function() {
        var files = this.files;
        let preview = $("#documentPreview");
        preview.empty();

        for (var i = 0; i < files.length; i++) {
            let fileName = files[i].name;
            let fileSize = (files[i].size / 1024 / 1024).toFixed(2) + ' MB';
            preview.append('<div class="alert alert-secondary py-2"><i class="bi bi-file"></i> ' + fileName + ' (' + fileSize + ')</div>');
        }
    });
</script>
@endpush

