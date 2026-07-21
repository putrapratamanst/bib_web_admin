@extends('layouts.app')

@section('title', 'Edit Debit Note')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Edit Debit Note - {{ $debitNote->number }}
        </div>
        <form autocomplete="off" method="POST" id="formEdit" action="{{ route('transaction.debit-notes.update', $debitNote->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="contact_id" id="contact_id" value="{{ old('contact_id', $debitNote->contact_id) }}">
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Validation Error!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">DN Number</label>
                            <input type="text" class="form-control" id="number" value="{{ old('number', $debitNote->number) }}" readonly style="background-color: #e9ecef;">
                            <input type="hidden" name="number" value="{{ $debitNote->number }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contract_id" class="form-label">Placing Number<sup class="text-danger">*</sup></label>
                            <select class="form-select @error('contract_id') is-invalid @enderror" name="contract_id" id="contract_id" required>
                                <option value="">Select Placing Number</option>
                                @if($debitNote->contract)
                                <option value="{{ $debitNote->contract->id }}" selected>{{ $debitNote->contract->number }}</option>
                                @endif
                            </select>
                            @error('contract_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="policy_number" class="form-label">Policy Number</label>
                            <input type="text" class="form-control" readonly name="policy_number" id="policy_number" value="{{ $debitNote->contract->policy_number ?? old('policy_number') }}" style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="ref_sistem_lama" class="form-label">Ref Sistem Lama</label>
                            <input type="text" class="form-control @error('ref_sistem_lama') is-invalid @enderror" name="ref_sistem_lama" id="ref_sistem_lama" value="{{ old('ref_sistem_lama', $debitNote->ref_sistem_lama) }}" placeholder="Ref Sistem Lama">
                            @error('ref_sistem_lama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="billing_address_id" class="form-label">Billing Address<sup class="text-danger">*</sup></label>
                            <select class="form-select @error('billing_address_id') is-invalid @enderror" name="billing_address_id" id="billing_address_id" required>
                                <option value="">Select Billing Address</option>
                                @if($debitNote->billingAddress)
                                <option value="{{ $debitNote->billingAddress->id }}" selected>{{ $debitNote->billingAddress->address }}</option>
                                @endif
                            </select>
                            @error('billing_address_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="insured_name" class="form-label">Insured Name</label>
                            <input type="text" class="form-control" readonly id="insured_name" value="{{ $debitNote->billingAddress?->name ?? '' }}" style="background-color: #e9ecef !important;">
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="correspondence_address" class="form-label">Correspondence Address</label>
                            <input type="text" class="form-control" readonly id="correspondence_address" value="{{ $debitNote->billingAddress?->address ?? '' }}" style="background-color: #e9ecef !important;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date<sup class="text-danger">*</sup></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" id="date" value="{{ old('date', $debitNote->date->format('Y-m-d')) }}" required>
                            @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date<sup class="text-danger">*</sup></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" name="due_date" id="due_date" value="{{ old('due_date', $debitNote->due_date->format('Y-m-d')) }}" required>
                            @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="created_at" class="form-label">Created Date</label>
                            <input type="date" class="form-control" readonly name="created_at" id="created_at" value="{{ $debitNote->created_at->format('Y-m-d') }}" style="background-color: #e9ecef;">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="installment" class="form-label">Installment<sup class="text-danger">*</sup></label>
                            <input type="hidden" name="installment" id="installment_value" value="{{ old('installment', $debitNote->installment) }}">
                            <select class="form-select @error('installment') is-invalid @enderror" id="installment" required disabled>
                                @for ($i = 0; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('installment', $debitNote->installment) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                            </select>
                            @error('installment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency<sup class="text-danger">*</sup></label>
                            <input type="hidden" name="currency" id="currency_value" value="{{ old('currency', $debitNote->currency_code) }}">
                            <select class="form-select @error('currency') is-invalid @enderror" id="currency" required disabled>
                                <option value="">Select Currency</option>
                                @foreach($currencies as $currencyOption)
                                <option value="{{ $currencyOption->code }}" {{ old('currency', $debitNote->currency_code) == $currencyOption->code ? 'selected' : '' }}>{{ $currencyOption->code }} - {{ $currencyOption->name }}</option>
                                @endforeach
                            </select>
                            @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="exchange_rate" class="form-label">Exchange Rate<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" id="currency-text" style="font-size: 14px;">{{ $debitNote->currency_code }}</span>
                                <input type="text" class="form-control autonumeric text-end @error('exchange_rate') is-invalid @enderror" name="exchange_rate" id="exchange_rate" value="{{ old('exchange_rate', $debitNote->exchange_rate_formatted) }}" required readonly style="background-color: #e9ecef;">
                                @error('exchange_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" id="amount-currency-text" style="font-size: 14px;">{{ $debitNote->currency_code }}</span>
                                <input type="text" class="form-control text-end autonumeric @error('amount') is-invalid @enderror" name="amount" id="amount" value="{{ old('amount', $debitNote->amount_formatted) }}" required readonly style="background-color: #e9ecef;">
                                @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="gross_premium" class="form-label">Gross Premium</label>
                            <div class="input-group">
                                <span class="input-group-text" id="gross-premium-currency-text" style="font-size: 14px;">{{ $debitNote->currency_code }}</span>
                                <input type="text" class="form-control autonumeric text-end" id="gross_premium" value="{{ $debitNote->contract->gross_premium_formatted ?? '0' }}" readonly style="background-color: #e9ecef;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="discount" class="form-label">Discount %</label>
                            <div class="input-group">
                                <input type="text" class="form-control autonumeric text-end" id="discount" value="{{ $debitNote->contract->discount ?? '0' }}" readonly style="background-color: #e9ecef;">
                                <span class="input-group-text" style="font-size: 14px;">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount</label>
                            <div class="input-group">
                                <span class="input-group-text" id="discount-amount-currency-text" style="font-size: 14px;">{{ $debitNote->currency_code }}</span>
                                <input type="text" class="form-control autonumeric text-end" id="discount_amount" value="{{ $debitNote->contract->discount_amount_formatted ?? '0' }}" readonly style="background-color: #e9ecef;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter description">{{ old('description', $debitNote->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Status Information -->
                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <input type="text" class="form-control" readonly value="{{ ucfirst($debitNote->status) }}" style="background-color: #e9ecef;">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="approval_status" class="form-label">Approval Status</label>
                            <div class="form-control-plaintext">
                                {!! $debitNote->approval_status_badge !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Note removed: All forms can now be edited regardless of approval status --}}
            </div>

            <div class="card-footer">
                <a href="{{ route('transaction.debit-notes.show', $debitNote->id) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Debit Note
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeContractRequest = null;
    let latestRequestedContractId = null;

    // Helper functions (global scope so they can be used in loadContractData and elsewhere)
    function normalizeNumberString(s) {
        if (s === null || s === undefined) return '';
        s = String(s).trim();
        if (s === '') return '';
        var cleaned = s.replace(/[^0-9.,-]/g, '');
        var lastDot = cleaned.lastIndexOf('.');
        var lastComma = cleaned.lastIndexOf(',');
        if (lastDot > -1 && lastComma > -1) {
            if (lastDot > lastComma) {
                cleaned = cleaned.replace(/,/g, '');
            } else {
                cleaned = cleaned.replace(/\./g, '').replace(/,/g, '.');
            }
        } else if (lastComma > -1) {
            cleaned = cleaned.replace(/\./g, '').replace(/,/g, '.');
        } else {
            cleaned = cleaned.replace(/,/g, '');
        }
        return cleaned;
    }

    function isParsableNumber(s) {
        var n = normalizeNumberString(s);
        return n !== '' && !isNaN(+n);
    }

    var AN_OPTIONS = {
        digitGroupSeparator: ',',
        decimalCharacter: '.',
        decimalPlaces: 2,
        minimumValue: '0'
    };

    function setAutoNumericValue(selectorOrEl, value) {
        var $el = selectorOrEl instanceof jQuery ? selectorOrEl : $(selectorOrEl);
        if (!$el.length) return;
        var v = value === null || value === undefined ? '' : value;
        var clean = normalizeNumberString(v);
        if (clean === '' || isNaN(+clean)) {
            return;
        }
        try {
            $el.autoNumeric('set', clean);
        } catch (err) {
            try {
                $el.autoNumeric('init', AN_OPTIONS);
                $el.autoNumeric('set', clean);
            } catch (err2) {
                $el.val(clean);
            }
        }
    }

    $(document).ready(function() {
        console.log('debitnote edit script loaded');
        // Initialize Select2 for contract selection
        $('#contract_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select placing number --',
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
                            more: data.pagination && data.pagination.more
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        });
        console.log('contract select2 initialized, element count=', $('#contract_id').length);

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
                    const contractId = $('#contract_id').val();
                    return {
                        search: params.term,
                        page: params.page || 1,
                        contract_id: contractId
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
            minimumInputLength: 0
        });

        // Prevent opening billing address dropdown when locked
        $('#billing_address_id').on('select2:opening', function(e) {
            if ($(this).data('locked')) {
                e.preventDefault();
            }
        });

        // Lock billing address dropdown on page load (edit mode)
        $('#billing_address_id').data('locked', true);
        $('#billing_address_id').next('.select2').find('.select2-selection').css({
            'background-color': '#e9ecef',
            'cursor': 'not-allowed'
        });

        // Handle billing address change to populate insured name and correspondence address
        $('#billing_address_id').on('change', function() {
            var billingAddressId = $(this).val();
            if (billingAddressId) {
                $.get("{{ route('api.billing-addresses.show', '') }}/" + billingAddressId)
                    .done(function(response) {
                        if (response.data) {
                            $('#insured_name').val(response.data.name || '');
                            $('#correspondence_address').val(response.data.address || '');
                        }
                    })
                    .fail(function() {
                        $('#insured_name').val('');
                        $('#correspondence_address').val('');
                    });
            } else {
                $('#insured_name').val('');
                $('#correspondence_address').val('');
            }
        });

        // NOTE: AutoNumeric initialization moved below to avoid initializing with already-formatted values

        // Safe initialize AutoNumeric after helpers are ready
        $('.autonumeric').each(function() {
            var $el = $(this);
            var v = $el.val();
            if (!v) {
                // empty, initialize
                try { $el.autoNumeric('init', AN_OPTIONS); } catch (e) { console.warn('AN init failed (empty)', e); }
            } else if (isParsableNumber(v)) {
                // if parsable, normalize and init
                var clean = normalizeNumberString(v);
                $el.val(clean);
                try { $el.autoNumeric('init', AN_OPTIONS); $el.autoNumeric('set', clean); } catch (e) { console.warn('AN init/set failed (parsable)', e); }
            }
            // else: leave formatted value as-is (do not init AutoNumeric to preserve display)
        });

        // Handle contract selection (Select2) and fallback change
        $('#contract_id').on('select2:select', function(e) {
            var contractId = e.params && e.params.data ? e.params.data.id : null;
            if (contractId) {
                loadContractData(contractId);
            }
        });

        // Fallback: native change event
        $('#contract_id').on('change', function() {
            console.log('native change on #contract_id', $(this).val());
            var contractId = $(this).val();
            if (contractId) {
                loadContractData(contractId);
            } else {
                $('#policy_number').val('');
                $('#billing_address_id').empty().append('<option value="">Select Billing Address</option>').data('locked', false);
                $('#billing_address_id').next('.select2').find('.select2-selection').css({
                    'background-color': '',
                    'cursor': ''
                });
                $('#currency_value').val('');
                $('#currency').val('');
                $('#currency-text, #amount-currency-text, #gross-premium-currency-text, #discount-amount-currency-text').text('');
                setAutoNumericValue('#exchange_rate', '0.00');
                setAutoNumericValue('#amount', '0.00');
                setAutoNumericValue('#gross_premium', '0.00');
                setAutoNumericValue('#discount', '0.00');
                setAutoNumericValue('#discount_amount', '0.00');
                $('#installment_value').val('0');
                $('#installment').val('0');
                $('#insured_name').val('');
                $('#correspondence_address').val('');
            }
        });

        // Handle when contract is cleared
        $('#contract_id').on('select2:clear', function() {
            $('#policy_number').val('');
            $('#billing_address_id').empty().append('<option value="">Select Billing Address</option>').data('locked', false);
            $('#billing_address_id').next('.select2').find('.select2-selection').css({
                'background-color': '',
                'cursor': ''
            });
            $('#currency_value').val('');
            $('#installment_value').val('0');
            $('#installment').val('0');
            $('#insured_name').val('');
            $('#correspondence_address').val('');
        });

        // Handle currency change
        $('#currency').on('change', function() {
            var currency = $(this).val();
            $('#currency_value').val(currency);
            $('#currency-text, #amount-currency-text').text(currency);
        });
    });

    function loadContractData(contractId) {
        latestRequestedContractId = contractId;

        if (activeContractRequest) {
            activeContractRequest.abort();
        }

        activeContractRequest = $.get("{{ route('api.contracts.show', '') }}/" + contractId)
            .done(function(response) {
                if ($('#contract_id').val() !== contractId || latestRequestedContractId !== contractId) {
                    return;
                }

                if (response.data) {
                    var contract = response.data;

                    // Update policy number
                    $('#policy_number').val(contract.policy_number || '-');

                    // Update contact_id hidden field
                    if (contract.contact_id) {
                        $('#contact_id').val(contract.contact_id);
                    }

                    // Update currency if available
                    if (contract.currency_code) {
                        $('#currency_value').val(contract.currency_code);
                        $('#currency').val(contract.currency_code).trigger('change');
                    } else {
                        $('#currency_value').val('');
                        $('#currency').val('').trigger('change');
                    }

                    if (contract.installment_count !== undefined && contract.installment_count !== null) {
                        $('#installment_value').val(contract.installment_count);
                        $('#installment').val(contract.installment_count);
                    } else {
                        $('#installment_value').val('0');
                        $('#installment').val('0');
                    }

                    // Update exchange rate if available
                    if (contract.exchange_rate !== undefined && contract.exchange_rate !== null) {
                        setAutoNumericValue('#exchange_rate', contract.exchange_rate);
                    } else {
                        setAutoNumericValue('#exchange_rate', '1.00');
                    }

                    if (contract.amount !== undefined && contract.amount !== null) {
                        setAutoNumericValue('#amount', contract.amount);
                    } else {
                        setAutoNumericValue('#amount', '0.00');
                    }

                    // Set gross premium, discount, and discount amount
                    if (contract.gross_premium) {
                        setAutoNumericValue('#gross_premium', contract.gross_premium);
                    } else {
                        setAutoNumericValue('#gross_premium', '0.00');
                    }

                    if (contract.discount !== undefined && contract.discount !== null) {
                        setAutoNumericValue('#discount', contract.discount);
                    } else {
                        setAutoNumericValue('#discount', '0.00');
                    }

                    if (contract.discount_amount) {
                        setAutoNumericValue('#discount_amount', contract.discount_amount);
                    } else {
                        setAutoNumericValue('#discount_amount', '0.00');
                    }

                    // Auto-select billing address from contract if available
                    if (contract.billing_address_id && contract.billing_address) {
                        // Clear and set the billing address from contract
                        $('#billing_address_id').empty().append(
                            new Option(
                                contract.billing_address.name + (contract.billing_address.address ? ' - ' + contract.billing_address.address : ''),
                                contract.billing_address_id,
                                true,
                                true
                            )
                        ).trigger('change');

                        // Lock the billing address dropdown (prevent opening but keep submittable)
                        $('#billing_address_id').data('locked', true);
                        $('#billing_address_id').next('.select2').find('.select2-selection').css({
                            'background-color': '#e9ecef',
                            'cursor': 'not-allowed'
                        });

                        // Populate insured name and correspondence address
                        $('#insured_name').val(contract.billing_address.name || '');
                        $('#correspondence_address').val(contract.billing_address.address || '');

                        console.log('Billing address auto-selected from contract:', contract.billing_address.name);
                    } else {
                        // Fallback: try to get from contact billing addresses
                        if (contract.contact && contract.contact.billing_addresses && contract.contact.billing_addresses.length > 0) {
                            var primaryAddress = contract.contact.billing_addresses.find(function(addr) {
                                return addr.is_primary;
                            });
                            var addressToUse = primaryAddress || contract.contact.billing_addresses[0];

                            if (addressToUse) {
                                $('#billing_address_id').empty().append(
                                    new Option(
                                        addressToUse.name + (addressToUse.address ? ' - ' + addressToUse.address : ''),
                                        addressToUse.id,
                                        true,
                                        true
                                    )
                                ).trigger('change');

                                // Lock the billing address dropdown (prevent opening but keep submittable)
                                $('#billing_address_id').data('locked', true);
                                $('#billing_address_id').next('.select2').find('.select2-selection').css({
                                    'background-color': '#e9ecef',
                                    'cursor': 'not-allowed'
                                });

                                $('#insured_name').val(addressToUse.name || '');
                                $('#correspondence_address').val(addressToUse.address || '');

                                console.log('Billing address auto-selected from contact:', addressToUse.name);
                            }
                        } else {
                            // Load billing addresses for the contact (old behavior)
                            loadBillingAddresses(contract.contact_id);
                        }
                    }
                }
            })
            .fail(function(xhr, status) {
                if (status === 'abort') {
                    return;
                }

                if ($('#contract_id').val() === contractId) {
                    alert('Failed to load contract data');
                }
            })
            .always(function() {
                if (latestRequestedContractId === contractId) {
                    activeContractRequest = null;
                }
            });
    }

    function loadBillingAddresses(contactId) {
        $('#billing_address_id').empty().append('<option value="">Loading...</option>');

        $.get("{{ route('api.billing-addresses.by-contact', '') }}/" + contactId)
            .done(function(response) {
                $('#billing_address_id').empty().append('<option value="">Select Billing Address</option>');

                if (response.data && response.data.length > 0) {
                    var selectedAddress = null;
                    $.each(response.data, function(index, address) {
                        var selected = address.id == "{{ $debitNote->billing_address_id }}" ? 'selected' : '';
                        $('#billing_address_id').append('<option value="' + address.id + '" ' + selected + '>' + address.address + '</option>');

                        // Keep track of the selected address
                        if (selected) {
                            selectedAddress = address;
                        }
                    });

                    // Populate insured name and correspondence address if an address is selected
                    if (selectedAddress) {
                        $('#insured_name').val(selectedAddress.name || '');
                        $('#correspondence_address').val(selectedAddress.address || '');
                    }
                } else {
                    $('#billing_address_id').append('<option value="" disabled>No billing addresses found</option>');
                }
            })
            .fail(function() {
                $('#billing_address_id').empty().append('<option value="">Failed to load addresses</option>');
            });
    }
</script>
@endpush