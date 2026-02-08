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
                            <input type="text" class="form-control @error('number') is-invalid @enderror" name="number" id="number" value="{{ old('number', $debitNote->number) }}" placeholder="Enter DN Number" required>
                            @error('number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <select class="form-select @error('installment') is-invalid @enderror" name="installment" id="installment" required>
                                @for ($i = 0; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('installment', $debitNote->installment) == $i ? 'selected' : '' }}>{{ $i == 0 ? 'Single Payment' : $i }}</option>
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
                            <select class="form-select @error('currency') is-invalid @enderror" name="currency" id="currency" required>
                                <option value="">Select Currency</option>
                                <option value="IDR" {{ old('currency', $debitNote->currency_code) == 'IDR' ? 'selected' : '' }}>IDR</option>
                                <option value="USD" {{ old('currency', $debitNote->currency_code) == 'USD' ? 'selected' : '' }}>USD</option>
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
                                <input type="text" class="form-control autonumeric text-end @error('exchange_rate') is-invalid @enderror" name="exchange_rate" id="exchange_rate" value="{{ old('exchange_rate', $debitNote->exchange_rate_formatted) }}" required>
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
                                <input type="text" class="form-control text-end autonumeric @error('amount') is-invalid @enderror" name="amount" id="amount" value="{{ old('amount', $debitNote->amount_formatted) }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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

                <!-- Warning Message -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Note:</strong> This Debit Note can only be edited while it is in draft status. Once submitted for approval, it cannot be modified.
                </div>
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
    $(document).ready(function() {
        // Initialize Select2 for contract selection
        $('#contract_id').select2({
            ajax: {
                url: "{{ route('api.contracts.select2') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.number + ' - ' + item.insured_name
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Search for a contract...',
            minimumInputLength: 2
        });

        // Initialize Select2 for billing address
        $('#billing_address_id').select2({
            placeholder: 'Select Billing Address'
        });

        // Initialize AutoNumeric for decimal inputs
        $('.autonumeric').autoNumeric('init', {
            digitGroupSeparator: ',',
            decimalCharacter: '.',
            decimalPlaces: 2,
            minimumValue: '0'
        });

        // Handle contract change to load billing addresses
        $('#contract_id').on('change', function() {
            var contractId = $(this).val();
            if (contractId) {
                loadContractData(contractId);
            } else {
                $('#policy_number').val('');
                $('#billing_address_id').empty().append('<option value="">Select Billing Address</option>');
            }
        });

        // Handle currency change
        $('#currency').on('change', function() {
            var currency = $(this).val();
            $('#currency-text, #amount-currency-text').text(currency);
            
            // Reset exchange rate if currency changes
            if (currency === 'IDR') {
                $('#exchange_rate').autoNumeric('set', '1.00');
            } else {
                $('#exchange_rate').autoNumeric('set', '15000.00');
            }
        });
    });

    function loadContractData(contractId) {
        $.get("{{ route('api.contracts.show', '') }}/" + contractId)
            .done(function(response) {
                if (response.data) {
                    // Update policy number
                    $('#policy_number').val(response.data.policy_number || '-');
                    
                    // Update currency if available
                    if (response.data.currency_code) {
                        $('#currency').val(response.data.currency_code).trigger('change');
                    }
                    
                    // Update exchange rate if available
                    if (response.data.exchange_rate) {
                        $('#exchange_rate').autoNumeric('set', response.data.exchange_rate);
                    }
                    
                    // Load billing addresses for the contact
                    loadBillingAddresses(response.data.contact_id);
                }
            })
            .fail(function() {
                alert('Failed to load contract data');
            });
    }

    function loadBillingAddresses(contactId) {
        $('#billing_address_id').empty().append('<option value="">Loading...</option>');
        
        $.get("{{ route('api.billing-addresses.by-contact', '') }}/" + contactId)
            .done(function(response) {
                $('#billing_address_id').empty().append('<option value="">Select Billing Address</option>');
                
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(index, address) {
                        var selected = address.id == "{{ $debitNote->billing_address_id }}" ? 'selected' : '';
                        $('#billing_address_id').append('<option value="' + address.id + '" ' + selected + '>' + address.address + '</option>');
                    });
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