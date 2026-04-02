@extends('layouts.app')

@section('title', 'Edit Debit Note Billings')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Edit Debit Note Billings
        </div>
        <form autocomplete="off" method="POST" id="formEdit" action="{{ route('transaction.debit-notes.update-billings', $debitNote->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="debit_note_id" value="{{ $debitNote->id }}">
            <div class="card-body">

                {{-- Alert for validation during input --}}
                <div id="validationAlert" style="display: none;"></div>

                {{-- Calculate fees for installments --}}
                @php
                    $policyFee = $debitNote->contract->policy_fee ?? 0;
                    $stampFee = $debitNote->contract->stamp_fee ?? 0;
                    $totalFees = $policyFee + $stampFee;
                @endphp

                {{-- Display Debit Note Amount Information --}}
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-info-circle"></i> Billing Amount Information</strong>
                    <div class="mt-2">
                        <div class="row">
                            <div class="col-md-4">
                                <small><strong>Total Debit Note Amount:</strong></small><br>
                                <strong>{{ number_format($debitNote->amount, 2, ',', '.') }} {{ $debitNote->currency_code }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small><strong>Total Billed:</strong></small><br>
                                <strong id="totalBilled">0,00 {{ $debitNote->currency_code }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small><strong>Remaining Available:</strong></small><br>
                                <strong id="remainingAmount">{{ number_format($debitNote->amount, 2, ',', '.') }} {{ $debitNote->currency_code }}</strong>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                {{-- Policy Fee & Stamp Fee Information --}}
                @if($totalFees > 0)
                <div class="alert alert-warning" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i> Informasi Fee</strong>
                    <div class="mt-2">
                        <p class="mb-1">Saat save, billing pertama akan ditambahkan:</p>
                        <ul class="mb-0">
                            <li><strong>Policy Fee:</strong> {{ number_format($policyFee, 2, ',', '.') }} {{ $debitNote->currency_code }}</li>
                            <li><strong>Stamp Fee:</strong> {{ number_format($stampFee, 2, ',', '.') }} {{ $debitNote->currency_code }}</li>
                            <li><strong>Total Fees:</strong> {{ number_format($totalFees, 2, ',', '.') }} {{ $debitNote->currency_code }}</li>
                        </ul>
                    </div>
                </div>
                @endif

                {{-- Display Error Messages --}}
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Display Success Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Display Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Validation Errors:</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                {{-- Loop through existing billings --}}
                @foreach($billings as $index => $billing)
                    @php
                        // Check if this is the first billing (might have fees included)
                        $isFirstBilling = false;
                        if (preg_match('/-INST(\d+)/i', $billing->billing_number, $matches)) {
                            $isFirstBilling = ((int)$matches[1] === 1);
                        } else {
                            // If no INST pattern, check if this is the earliest billing
                            $firstBilling = $debitNote->billings()->orderBy('created_at')->first();
                            $isFirstBilling = ($billing->id === $firstBilling->id);
                        }
                        $hasFees = $isFirstBilling && $totalFees > 0;
                    @endphp
                    <div class="row border p-3 mb-3 rounded {{ $hasFees ? 'border-warning' : '' }}">
                        <h6 class="mb-3">
                            Billing #{{ $index + 1 }}: {{ $billing->billing_number }}
                            @if($hasFees)
                                <span class="badge bg-warning text-dark ms-2">
                                    <i class="fas fa-info-circle"></i> Fees akan ditambahkan otomatis
                                </span>
                            @endif
                            @if($billing->status !== 'pending')
                                <span class="badge bg-secondary ms-2">{{ ucfirst($billing->status) }}</span>
                            @endif
                        </h6>

                        <input type="hidden" name="billing_id[]" value="{{ $billing->id }}">

                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="billing_number_{{ $index }}" class="form-label">Billing Number <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control" id="billing_number_{{ $index }}" value="{{ $billing->billing_number }}" readonly style="background-color: #e9ecef;">
                                <small class="text-muted">Cannot be changed</small>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="date_{{ $index }}" class="form-label">Date <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control datepicker @error('date.' . $index) is-invalid @enderror" name="date[]" id="date_{{ $index }}" value="{{ old('date.' . $index, $billing->date->format('d-m-Y')) }}">
                                @error('date.' . $index)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="due_date_{{ $index }}" class="form-label">Due Date <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control datepicker @error('due_date.' . $index) is-invalid @enderror" name="due_date[]" id="due_date_{{ $index }}" value="{{ old('due_date.' . $index, $billing->due_date->format('d-m-Y')) }}">
                                @error('due_date.' . $index)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="amount_{{ $index }}" class="form-label">
                                    Amount <sup class="text-danger">*</sup>
                                    @if($hasFees)
                                        <small class="text-warning">(+fees otomatis)</small>
                                    @endif
                                </label>
                                <input type="text" 
                                       class="form-control autonumeric @error('amount.' . $index) is-invalid @enderror" 
                                       name="amount[]" 
                                       id="amount_{{ $index }}" 
                                       value="{{ old('amount.' . $index, $hasFees ? $billing->amount - $totalFees : $billing->amount) }}"
                                       data-billing-number="{{ $billing->billing_number }}"
                                       data-is-first="{{ $isFirstBilling ? '1' : '0' }}">
                                @error('amount.' . $index)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($hasFees)
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Fees {{ number_format($totalFees, 2, ',', '.') }} akan ditambahkan otomatis saat save
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="card-footer">
                <a href="{{ route('transaction.debit-notes.show', $debitNote->id) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="btnSave">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const debitNoteAmount = {{ $debitNote->amount }};
    const currencyCode = "{{ $debitNote->currency_code }}";
    const totalFees = {{ $totalFees }};

    // Initialize currency formatter
    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

    // Calculate and update total billed amount
    function updateBillingTotals() {
        const amountInputs = document.querySelectorAll('input[name="amount[]"]');
        let totalBilled = 0;

        amountInputs.forEach((input, index) => {
            // Skip if input is empty
            if (!input.value || input.value.trim() === '') {
                return;
            }

            let numValue = 0;
            try {
                // Try to get value from AutoNumeric
                const autoNumericValue = $(input).autoNumeric('get');
                if (autoNumericValue !== null && autoNumericValue !== undefined) {
                    numValue = parseFloat(autoNumericValue) || 0;
                } else {
                    throw new Error('AutoNumeric not initialized');
                }
            } catch(e) {
                // Fallback to manual parsing for Indonesian format (1.234.567,89)
                const value = input.value.trim()
                    .replace(/\./g, '')  // Remove thousand separators (dots)
                    .replace(/,/g, '.'); // Replace decimal comma with dot
                numValue = parseFloat(value) || 0;
            }

            // Display total tanpa tambah fees (as-is dari input)
            // Fees akan ditambahkan saat validasi submit
            if (!isNaN(numValue) && isFinite(numValue)) {
                totalBilled += numValue;
            }
        });
        
        const remainingAmount = debitNoteAmount - totalBilled;
        
        // Update display
        document.getElementById('totalBilled').innerHTML = `${formatCurrency(totalBilled)} ${currencyCode}`;
        document.getElementById('remainingAmount').innerHTML = `${formatCurrency(remainingAmount)} ${currencyCode}`;

        // Check if total exceeds debit note amount
        if (totalBilled > debitNoteAmount) {
            document.getElementById('totalBilled').innerHTML = `<span class="text-danger">${formatCurrency(totalBilled)} ${currencyCode}</span>`;
            document.getElementById('remainingAmount').innerHTML = `<span class="text-danger">${formatCurrency(remainingAmount)} ${currencyCode}</span>`;
        } else {
            document.getElementById('totalBilled').innerHTML = `<span class="text-success">${formatCurrency(totalBilled)} ${currencyCode}</span>`;
            document.getElementById('remainingAmount').innerHTML = `${formatCurrency(remainingAmount)} ${currencyCode}`;
        }

        return totalBilled;
    }

    // Add event listener to all amount inputs
    $(document).on('change keyup', 'input[name="amount[]"]', function() {
        const totalBilled = updateBillingTotals();
        
        // Warn if total exceeds debit note amount
        if (totalBilled > debitNoteAmount) {
            const alertHtml = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i> Warning!</strong> 
                    Total billing amount (${formatCurrency(totalBilled)} ${currencyCode}) exceeds debit note amount <strong>(${formatCurrency(debitNoteAmount)} ${currencyCode})</strong>. Please adjust the amounts.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#validationAlert').html(alertHtml).show();
        } else {
            $('#validationAlert').html('').hide();
        }
    });

    // Initialize on page load
    $(document).ready(function() {
        // Re-initialize AutoNumeric for all amount inputs to ensure proper formatting
        $('input[name="amount[]"]').each(function() {
            // Destroy existing autoNumeric instance if any to prevent conflicts
            if ($(this).data('autoNumeric')) {
                $(this).autoNumeric('destroy');
            }
            // Initialize with Indonesian format
            $(this).autoNumeric('init', {
                aSep: '.',
                aDec: ',',
                aForm: true,
            });
        });
        
        // Update totals after initialization with slight delay to ensure autoNumeric is ready
        setTimeout(function() {
            updateBillingTotals();
        }, 100);
    });

    // Handle form submission
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        
        // Calculate total billing amount first (before cleaning)
        let totalBilling = 0;
        $('input[name="amount[]"]').each(function(index) {
            let numValue = 0;
            try {
                const autoNumericValue = $(this).autoNumeric('get');
                numValue = parseFloat(autoNumericValue) || 0;
            } catch(err) {
                const value = $(this).val().trim()
                    .replace(/\./g, '')
                    .replace(/,/g, '.');
                numValue = parseFloat(value) || 0;
            }
            
            totalBilling += numValue;
            
            // Check if this is INST1 - add fees (fees ditambahkan otomatis saat save)
            const isFirstBilling = $(this).attr('data-is-first') === '1';
            if (isFirstBilling && totalFees > 0) {
                totalBilling += totalFees;
            }
        });
        
      
        
        // Clean all amount inputs before submit (convert to standard format)
        $('input[name="amount[]"]').each(function() {
            var currentValue = $(this).val();
            if (currentValue && currentValue.trim() !== '') {
                // Manual cleaning: remove dots (thousand separator) and replace comma with dot
                var cleanValue = currentValue.trim()
                    .replace(/\./g, '')  // Remove all dots
                    .replace(/,/g, '.'); // Replace comma with dot
                $(this).val(cleanValue);
            }
        });
        
        // Disable submit button to prevent double submission
        $('#btnSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Submit the form
        this.submit();
    });
</script>
@endpush
