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
                    <!-- <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">DN Number<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control @error('number') is-invalid @enderror" name="number" id="number" value="{{ old('number') }}" placeholder="Auto generated">
                            @error('number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> -->
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contract_id" class="form-label">Contract<sup class="text-danger">*</sup></label>
                            <select class="form-select select2 @error('contract_id') is-invalid @enderror" name="contract_id" id="contract_id" required>
                                <option value="">Select Contract</option>
                            </select>
                            @error('contract_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contact_id" class="form-label">Contact<sup class="text-danger">*</sup></label>
                            <select class="form-select select2 @error('contact_id') is-invalid @enderror" name="contact_id" id="contact_id" required>
                                <option value="">Select Contact</option>
                            </select>
                            @error('contact_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date<sup class="text-danger">*</sup></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date<sup class="text-danger">*</sup></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" name="due_date" id="due_date" value="{{ old('due_date') }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
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
                                <input type="text" class="form-control text-end autonumeric @error('amount') is-invalid @enderror" name="amount" id="amount" value="{{ old('amount') }}" required>
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
        placeholder: 'Select Contact',
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

    // Initialize AutoNumeric for currency fields
    new AutoNumeric('#exchange_rate', {
        allowDecimalPadding: false,
        currencySymbol: '',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        decimalPlaces: 0,
        minimumValue: '0'
    });

    new AutoNumeric('#amount', {
        allowDecimalPadding: false,
        currencySymbol: '',
        decimalCharacter: '.',
        digitGroupSeparator: ',',
        decimalPlaces: 2,
        minimumValue: '0'
    });

    // Handle contract selection change
    $('#contract_id').on('change', function() {
        const contractId = $(this).val();
        if (contractId) {
            // Get contract details
            $.get(`/api/contract/${contractId}`)
                .done(function(response) {
                    if (response.data) {
                        const contract = response.data;
                        
                        // Auto-select contact if available
                        if (contract.contact_id) {
                            // Create option for contact and select it
                            const contactOption = new Option(contract.contact.display_name, contract.contact_id, true, true);
                            $('#contact_id').append(contactOption).trigger('change');
                        }
                        
                        // Update currency if available
                        if (contract.currency_code) {
                            $('#currency').val(contract.currency_code).trigger('change');
                        }
                        
                        // Update exchange rate if available
                        if (contract.exchange_rate) {
                            AutoNumeric.getAutoNumericElement('#exchange_rate').set(contract.exchange_rate);
                        }
                        
                        // Update amount from contract total premium
                        if (contract.amount) {
                            AutoNumeric.getAutoNumericElement('#amount').set(contract.amount);
                        }
                    }
                })
                .fail(function() {
                    console.log('Failed to fetch contract details');
                });
        } else {
            // Clear fields when no contract selected
            $('#contact_id').val(null).trigger('change');
            $('#currency').val('').trigger('change');
            AutoNumeric.getAutoNumericElement('#exchange_rate').set('1');
            AutoNumeric.getAutoNumericElement('#amount').set('0');
        }
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
            AutoNumeric.getAutoNumericElement('#exchange_rate').set('1');
        } else {
            // You can set default exchange rates for other currencies here
            // For now, keep current value or set to 1
            const currentRate = AutoNumeric.getAutoNumericElement('#exchange_rate').getNumber();
            if (currentRate === 0 || currentRate === 1) {
                AutoNumeric.getAutoNumericElement('#exchange_rate').set('1');
            }
        }
    });

    // Handle date change to auto-set due date
    $('#date').on('change', function() {
        const selectedDate = new Date($(this).val());
        if (selectedDate) {
            // Add 30 days to the selected date for due date
            selectedDate.setDate(selectedDate.getDate() + 30);
            const dueDateString = selectedDate.toISOString().split('T')[0];
            $('#due_date').val(dueDateString);
        }
    });

    // Form submission
        $("#formCreate").submit(function(e) {
        e.preventDefault();
        
        
        // Get numeric values from AutoNumeric fields
        const exchangeRate = AutoNumeric.getAutoNumericElement('#exchange_rate').getNumber();
        const amount = AutoNumeric.getAutoNumericElement('#amount').getNumber();
        
        // Create form data
        const formData = new FormData(this);
        formData.set('exchange_rate', exchangeRate);
        formData.set('amount', amount);
        console.log(formData);
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
