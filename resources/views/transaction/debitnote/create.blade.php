@extends('layouts.app')

@section('title', 'Create Debit Note')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Create Debit Note
        </div>
        <form autocomplete="off" method="POST" id="formCreate" action="{{ route('transaction.debit-notes.store') }}">
            @csrf
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
                            <label for="number" class="form-label">DN Number<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control @error('number') is-invalid @enderror" name="number" id="number" value="{{ old('number') }}" placeholder="Enter DN Number" required>
                            @error('number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contract_id" class="form-label">Placing<sup class="text-danger">*</sup></label>
                            <select class="form-select select2 @error('contract_id') is-invalid @enderror" name="contract_id" id="contract_id" required>
                                <option value="">Select Placing</option>
                            </select>
                            @error('contract_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contact_id" class="form-label">Billing Contact<sup class="text-danger">*</sup></label>
                            <select class="form-select select2 @error('contact_id') is-invalid @enderror" name="contact_id" id="contact_id" required>
                                <option value="">Select Billing Contact</option>
                            </select>
                            @error('contact_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="billing_address_id" class="form-label">Billing Address<sup class="text-danger">*</sup></label>
                            <select class="form-select select2 @error('billing_address_id') is-invalid @enderror" name="billing_address_id" id="billing_address_id" required disabled>
                                <option value="">Select Billing Address</option>
                            </select>
                            @error('billing_address_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control datepicker @error('date') is-invalid @enderror" name="date" id="date" value="{{ old('date', date('d-m-Y')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control datepicker @error('due_date') is-invalid @enderror" name="due_date" id="due_date" value="{{ old('due_date') }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="created_at" class="form-label">Created Date<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control datepicker @error('created_at') is-invalid @enderror" name="created_at" id="created_at" value="{{ old('created_at', date('d-m-Y')) }}" required>
                            @error('created_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3" id="installment-field" style="display: none;">
                        <div class="mb-3">
                            <label for="installment" class="form-label">Installment<sup class="text-danger">*</sup></label>
                            <input type="number" class="form-control @error('installment') is-invalid @enderror" name="installment" id="installment" value="{{ old('installment', 0) }}" min="0" max="12" required>
                            @error('installment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Number of installments (0-12)</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency<sup class="text-danger">*</sup></label>
                            <select class="form-select @error('currency') is-invalid @enderror" name="currency" id="currency" required>
                                <option value="">Select Currency</option>
                                <option value="IDR" {{ old('currency') == 'IDR' ? 'selected' : '' }}>IDR - Indonesian Rupiah</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
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
                                <span class="input-group-text" style="font-size: 14px;" id="currency-prefix">IDR</span>
                                <input type="text" class="form-control autonumeric text-end @error('exchange_rate') is-invalid @enderror" name="exchange_rate" id="exchange_rate" value="{{ old('exchange_rate') }}" required>
                            </div>
                            @error('exchange_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;" id="amount-currency-prefix">IDR</span>
                                <input type="text" class="form-control autonumeric text-end @error('amount') is-invalid @enderror" name="amount" id="amount" value="{{ old('amount') }}" required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('transaction.debit-notes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <i class="fas fa-save me-1"></i> Save Debit Note
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show popup for session messages
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    @endif

    @if($errors->any())
        let errorMessages = @json($errors->all());
        let errorText = errorMessages.join('\n');
        
        Swal.fire({
            title: 'Validation Error!',
            text: errorText,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    @endif

    // Initialize Select2 for contract selection
    $('#contract_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select Contract',
        allowClear: true,
        ajax: {
            url: '{{ route("api.contracts.select2") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: data.data,
                    pagination: {
                        more: data.pagination && data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    // Initialize Select2 for contact selection
    $('#contact_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select Billing Contact',
        allowClear: true,
        ajax: {
            url: '{{ route("api.contacts.select2") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: data.data,
                    pagination: {
                        more: data.pagination && data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    // Initialize Select2 for billing address selection
    $('#billing_address_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select Billing Address',
        allowClear: true,
        escapeMarkup: function (markup) {
            return markup; // Don't escape markup
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
        },
        ajax: {
            url: '{{ route("api.billing-addresses.select2") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                const contactId = $('#contact_id').val();
                return {
                    search: params.term,
                    page: params.page || 1,
                    contact_id: contactId
                };
            },
            processResults: function (data) {
                return {
                    results: data.data,
                    pagination: {
                        more: data.pagination && data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    // Handle contract selection change
    $('#contract_id').on('select2:select', function(e) {
        const contractId = e.params.data.id;
        const contractText = e.params.data.text;
        console.log('Contract selected:', contractId, contractText);
        
        // Enable billing address dropdown when contract is selected
        $('#billing_address_id').prop('disabled', false).val(null).trigger('change');
        
        if (contractId) {
            // Get contract details - use the correct API endpoint
            const apiUrl = `/api/contract/${contractId}`;
            console.log('Making API call to:', apiUrl);
            
            $.get(apiUrl)
                .done(function(response) {
                    console.log('API Response:', response);
                    if (response.data) {
                        const contract = response.data;
                        console.log('Contract data:', contract);
                        
                        // Auto-select contact if available
                        if (contract.contact && contract.contact.id) {
                            // Clear existing options and add the selected contact
                            $('#contact_id').empty().append(
                                new Option(contract.contact.display_name || contract.contact.name, contract.contact.id, true, true)
                            ).trigger('change.select2');
                            console.log('Contact selected:', contract.contact.display_name || contract.contact.name);
                        }
                        
                        // Update currency if available
                        if (contract.currency_code) {
                            $('#currency').val(contract.currency_code).trigger('change');
                            console.log('Currency set to:', contract.currency_code);
                        }
                        
                        // Update installment count if available (use installment_count from API)
                        if (contract.installment_count !== undefined && contract.installment_count !== null) {
                            $('#installment').val(contract.installment_count);
                            console.log('Installment set to:', contract.installment_count);
                            
                            // Hide installment field if installment is 0 or 1
                            if (contract.installment_count === 0 || contract.installment_count === 1) {
                                $('#installment-field').hide();
                                $('#installment').prop('required', false);
                            } else {
                                $('#installment-field').show();
                                $('#installment').prop('required', true);
                            }
                        } else {
                            console.log('Installment count not found in contract data');
                            $('#installment-field').hide();
                            $('#installment').prop('required', false);
                        }
                        
                        // Wait a bit for AutoNumeric to be ready, then update values
                        setTimeout(function() {
                            // Update exchange rate if available (convert string to number)
                            if (contract.exchange_rate) {
                                const exchangeRateValue = parseFloat(contract.exchange_rate);
                                console.log('Trying to set exchange rate:', exchangeRateValue, 'from', contract.exchange_rate);
                                try {
                                    if ($('#exchange_rate').hasClass('autonumeric') && typeof $('#exchange_rate').autoNumeric === 'function') {
                                        $('#exchange_rate').autoNumeric('set', exchangeRateValue);
                                        console.log('Exchange rate set via AutoNumeric to:', exchangeRateValue);
                                    } else {
                                        $('#exchange_rate').val(exchangeRateValue);
                                        console.log('Exchange rate set via val() to:', exchangeRateValue);
                                    }
                                } catch (error) {
                                    console.warn('AutoNumeric not available for exchange_rate, using direct value:', error);
                                    $('#exchange_rate').val(exchangeRateValue);
                                    console.log('Exchange rate set via fallback to:', exchangeRateValue);
                                }
                            } else {
                                console.log('Exchange rate not found in contract data');
                            }
                            
                            // Update amount from contract total premium (convert string to number)
                            if (contract.amount) {
                                const amountValue = parseFloat(contract.amount);
                                console.log('Trying to set amount:', amountValue, 'from', contract.amount);
                                try {
                                    if ($('#amount').hasClass('autonumeric') && typeof $('#amount').autoNumeric === 'function') {
                                        $('#amount').autoNumeric('set', amountValue);
                                        console.log('Amount set via AutoNumeric to:', amountValue);
                                    } else {
                                        $('#amount').val(amountValue);
                                        console.log('Amount set via val() to:', amountValue);
                                    }
                                } catch (error) {
                                    console.warn('AutoNumeric not available for amount, using direct value:', error);
                                    $('#amount').val(amountValue);
                                    console.log('Amount set via fallback to:', amountValue);
                                }
                            } else {
                                console.log('Amount not found in contract data');
                            }
                        }, 200); // Increased timeout to ensure AutoNumeric is ready
                    }
                })
                .fail(function(xhr) {
                    console.log('Failed to fetch contract details:', xhr);
                    console.log('Response text:', xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load contract details',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }
    });

    // Handle when contract is cleared
    $('#contract_id').on('select2:clear', function() {
        console.log('Contract cleared');
        // Clear fields when no contract selected
        $('#contact_id').val(null).trigger('change');
        $('#billing_address_id').val(null).trigger('change').prop('disabled', true);
        $('#currency').val('').trigger('change');
        $('#installment').val('0');
        $('#installment-field').hide();
        $('#installment').prop('required', false);
        setTimeout(function() {
            try {
                $('#exchange_rate').autoNumeric('set', '1');
                $('#amount').autoNumeric('set', '0');
            } catch (error) {
                console.error('Error clearing AutoNumeric values:', error);
                $('#exchange_rate').val('1');
                $('#amount').val('0');
            }
        }, 100);
    });

    // Handle contact selection change
    $('#contact_id').on('change', function() {
        const contactId = $(this).val();
        if (contactId) {
            // Filter contracts by selected contact
            $('#contract_id').val(null).trigger('change');
            // You can add logic here to filter contract dropdown based on contact
        }
    });

    // Handle currency change
    $('#currency').on('change', function() {
        const currency = $(this).val() || 'IDR';
        $('#currency-prefix').text(currency);
        $('#amount-currency-prefix').text(currency);
        
        // Set default exchange rate based on currency
        if (currency === 'IDR') {
            try {
                if ($('#exchange_rate').hasClass('autonumeric') && $('#exchange_rate').autoNumeric) {
                    $('#exchange_rate').autoNumeric('set', '1');
                } else {
                    $('#exchange_rate').val('1');
                }
            } catch (error) {
                $('#exchange_rate').val('1');
            }
        } else {
            // You can set default exchange rates for other currencies here
            // For now, keep current value or set to 1
            try {
                if ($('#exchange_rate').hasClass('autonumeric') && $('#exchange_rate').autoNumeric) {
                    const currentRate = $('#exchange_rate').autoNumeric('get');
                    if (currentRate === 0 || currentRate === 1) {
                        $('#exchange_rate').autoNumeric('set', '1');
                    }
                } else {
                    const currentRate = parseFloat($('#exchange_rate').val()) || 0;
                    if (currentRate === 0 || currentRate === 1) {
                        $('#exchange_rate').val('1');
                    }
                }
            } catch (error) {
                $('#exchange_rate').val('1');
            }
        }
    });

    // Set initial due date and handle date change
    function setDueDate(baseDate) {
        const dueDate = new Date(baseDate);
        dueDate.setDate(dueDate.getDate() + 10); // Add 10 days
        const dueDateString = dueDate.toISOString().split('T')[0];
        $('#due_date').val(dueDateString);
    }

    // Set initial due date if empty
    if (!$('#due_date').val()) {
        setDueDate(new Date());
    }

    // Handle date change to auto-set due date
    $('#date').on('change', function() {
        const selectedDate = new Date($(this).val());
        if (selectedDate) {
            setDueDate(selectedDate);
        }
    });

    // Form submission
    $("#formCreate").submit(function(e) {
        e.preventDefault();
        
        // Get numeric values from AutoNumeric fields
        let exchangeRate, amount;
        
        try {
            if ($('#exchange_rate').hasClass('autonumeric') && typeof $('#exchange_rate').autoNumeric === 'function') {
                exchangeRate = $('#exchange_rate').autoNumeric('get');
            } else {
                exchangeRate = $('#exchange_rate').val();
            }
        } catch (error) {
            exchangeRate = $('#exchange_rate').val();
        }
        
        try {
            if ($('#amount').hasClass('autonumeric') && typeof $('#amount').autoNumeric === 'function') {
                amount = $('#amount').autoNumeric('get');
            } else {
                amount = $('#amount').val();
            }
        } catch (error) {
            amount = $('#amount').val();
        }
        
        console.log('Form submit - Exchange rate:', exchangeRate, 'Amount:', amount);
        
        // Convert date format from d-m-Y to Y-m-d for API
        function convertDateFormat(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                return parts[2] + '-' + parts[1] + '-' + parts[0]; // Y-m-d
            }
            return dateStr;
        }
        
        // Create form data
        const formData = new FormData(this);
        formData.set('exchange_rate', exchangeRate);
        formData.set('amount', amount);
        formData.set('date', convertDateFormat($('#date').val()));
        formData.set('due_date', convertDateFormat($('#due_date').val()));
        formData.set('created_at', convertDateFormat($('#created_at').val()));
        $.ajax({
            url: '{{ route("api.debit-notes.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message || 'Debit Note created successfully',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to show page or index
                            if (response.data && response.data.id) {
                                window.location.href = `{{ route("transaction.debit-notes.show", '') }}/${response.data.id}`;
                            } else {
                                window.location.href = '{{ route("transaction.debit-notes.index") }}';
                            }
                        }
                    });
                } else {
                    Swal.fire('Error!', response.message || 'Failed to create Debit Note', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to create Debit Note';
                
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    if (errors) {
                        // Clear previous error states
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();
                        
                        // Show validation errors
                        Object.keys(errors).forEach(function(field) {
                            const input = $(`[name="${field}"]`);
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                        });
                        
                        errorMessage = 'Please check the form for errors';
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire('Error!', errorMessage, 'error');
            },
            complete: function() {
                // Re-enable submit button
                $('#btnSubmit').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Debit Note');
            }
        });
    });

    // Trigger currency change on page load to set initial values
    $('#currency').trigger('change');
});
</script>
@endpush
