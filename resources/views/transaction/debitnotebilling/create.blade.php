@extends('layouts.app')

@section('title', 'Create Debit Note Billing')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Create Debit Note Billing
        </div>
        <form autocomplete="off" method="POST" id="formCreate" action="{{ route('transaction.debitnotebillings.store', ['id' => $debitNote->id]) }}">
            @csrf
            <input type="hidden" name="debit_note_id" value="{{ $debitNote->id }}">
            <div class="card-body">

                {{-- Calculate fees for installments --}}
                @php
                    $policyFee = $debitNote->contract->policy_fee ?? 0;
                    $stampFee = $debitNote->contract->stamp_fee ?? 0;
                    $totalFees = $policyFee + $stampFee;
                    $baseInstallmentAmount = $debitNote->installment > 0 ? $debitNote->amount / $debitNote->installment : $debitNote->amount;
                    $firstInstallmentAmount = $baseInstallmentAmount + $totalFees;
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
                                <strong id="totalBilled">0.00 {{ $debitNote->currency_code }}</strong>
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
                @if($totalFees > 0 && $debitNote->installment > 1)
                <div class="alert alert-warning" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i> Informasi Fee</strong>
                    <div class="mt-2">
                        <p class="mb-1">Installment pertama akan ditambahkan dengan:</p>
                        <ul class="mb-0">
                            <li><strong>Policy Fee:</strong> {{ number_format($policyFee, 2, ',', '.') }} {{ $debitNote->currency_code }}</li>
                            <li><strong>Stamp Fee:</strong> {{ number_format($stampFee, 2, ',', '.') }} {{ $debitNote->currency_code }}</li>
                            <li><strong>Total Fees:</strong> {{ number_format($totalFees, 2, ',', '.') }} {{ $debitNote->currency_code }}</li>
                        </ul>
                        <hr class="my-2">
                        <small>
                            <strong>Base Amount per Installment:</strong> {{ number_format($baseInstallmentAmount, 2, ',', '.') }} {{ $debitNote->currency_code }}<br>
                            <strong>Installment 1 (+ Fees):</strong> {{ number_format($firstInstallmentAmount, 2, ',', '.') }} {{ $debitNote->currency_code }}
                        </small>
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
                
                @if ($debitNote->installment > 0)
                    {{-- Looping sesuai jumlah installment --}}
                    @for ($i = 1; $i <= $debitNote->installment; $i++)
                        @php
                            // Hitung amount untuk setiap installment
                            $installmentAmount = $baseInstallmentAmount;
                            if ($i == 1 && $totalFees > 0) {
                                $installmentAmount = $firstInstallmentAmount;
                            }
                        @endphp
                        <div class="row border p-3 mb-3 rounded {{ $i == 1 && $totalFees > 0 ? 'border-warning' : '' }}">
                            <h6 class="mb-3">
                                Installment {{ $i }}
                                @if($i == 1 && $totalFees > 0)
                                    <span class="badge bg-warning text-dark ms-2">
                                        <i class="fas fa-plus-circle"></i> Termasuk Policy Fee & Stamp Fee
                                    </span>
                                @endif
                            </h6>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="billing_number_{{ $i }}" class="form-label">Billing Number <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control @error('billing_number.' . ($i-1)) is-invalid @enderror" name="billing_number[]" id="billing_number_{{ $i }}" value="{{ old('billing_number.' . ($i-1), $debitNote->number . '-INST' . $i) }}">
                                    @error('billing_number.' . ($i-1))
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="date_{{ $i }}" class="form-label">Date <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control datepicker @error('date.' . ($i-1)) is-invalid @enderror" name="date[]" id="date_{{ $i }}" value="{{ old('date.' . ($i-1)) }}">
                                    @error('date.' . ($i-1))
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="due_date_{{ $i }}" class="form-label">Due Date <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control datepicker @error('due_date.' . ($i-1)) is-invalid @enderror" name="due_date[]" id="due_date_{{ $i }}" value="{{ old('due_date.' . ($i-1)) }}">
                                    @error('due_date.' . ($i-1))
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="amount_{{ $i }}" class="form-label">
                                        Amount <sup class="text-danger">*</sup>
                                        @if($i == 1 && $totalFees > 0)
                                            <small class="text-warning">(incl. fees)</small>
                                        @endif
                                    </label>
                                    <input type="text" class="form-control autonumeric @error('amount.' . ($i-1)) is-invalid @enderror" name="amount[]" id="amount_{{ $i }}" value="{{ old('amount.' . ($i-1), $installmentAmount) }}">
                                    @error('amount.' . ($i-1))
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($i == 1 && $totalFees > 0)
                                        <small class="text-muted">
                                            Base: {{ number_format($baseInstallmentAmount, 2, ',', '.') }} + Fees: {{ number_format($totalFees, 2, ',', '.') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endfor
                @else
                    {{-- Kalau tidak ada installment, tampilkan form biasa --}}
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="billing_number" class="form-label">Billing Number <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control @error('billing_number.0') is-invalid @enderror" name="billing_number[]" id="billing_number" value="{{ old('billing_number.0') }}">
                                @error('billing_number.0')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control datepicker @error('date.0') is-invalid @enderror" name="date[]" id="date" value="{{ old('date.0') }}">
                                @error('date.0')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control datepicker @error('due_date.0') is-invalid @enderror" name="due_date[]" id="due_date" value="{{ old('due_date.0') }}">
                                @error('due_date.0')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control autonumeric @error('amount.0') is-invalid @enderror" name="amount[]" id="amount" value="{{ old('amount.0') }}">
                                @error('amount.0')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('transaction.debit-notes.show', $debitNote->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const debitNoteAmount = {{ $debitNote->amount }};
    const currencyCode = "{{ $debitNote->currency_code }}";

    // Initialize currency formatter
    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

    // Calculate and update total billed and remaining amount
    function updateBillingTotals() {
        const amountInputs = document.querySelectorAll('input[name="amount[]"]');
        let totalBilled = 0;

        amountInputs.forEach(input => {
            // Use AutoNumeric get method if available
            let numValue = 0;
            try {
                numValue = parseFloat($(input).autoNumeric('get')) || 0;
            } catch(e) {
                // Fallback to manual parsing
                const value = input.value.replace(/\./g, '').replace(/,/g, '.');
                numValue = parseFloat(value) || 0;
            }
            totalBilled += numValue;
        });

        const remainingAmount = debitNoteAmount - totalBilled;
        
        // Update display
        document.getElementById('totalBilled').innerHTML = `${formatCurrency(totalBilled)} ${currencyCode}`;
        document.getElementById('remainingAmount').innerHTML = `${formatCurrency(remainingAmount)} ${currencyCode}`;

        // Check if total exceeds debit note amount
        if (totalBilled > debitNoteAmount) {
            document.getElementById('remainingAmount').innerHTML = `<span class="text-danger">${formatCurrency(remainingAmount)} ${currencyCode}</span>`;
        } else {
            document.getElementById('remainingAmount').innerHTML = `${formatCurrency(remainingAmount)} ${currencyCode}`;
        }

        return remainingAmount;
    }

    // Add event listener to all amount inputs
    $(document).on('change keyup', 'input[name="amount[]"]', function() {
        const remainingAmount = updateBillingTotals();
        
        // Get the current input's value
        let currentValue = 0;
        try {
            currentValue = parseFloat($(this).autoNumeric('get')) || 0;
        } catch(e) {
            currentValue = parseFloat(this.value.replace(/\./g, '').replace(/,/g, '.')) || 0;
        }
        
        // Warn if individual input exceeds remaining
        if (currentValue > debitNoteAmount) {
            Swal.fire({
                title: 'Amount Exceeds Limit',
                text: `The billing amount exceeds the total debit note amount ({{ $debitNote->amount }}). Please adjust the amount.`,
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateBillingTotals();
    });

    // Handle form submission to clean AutoNumeric values
    $('#formCreate').on('submit', function(e) {
        // Clean all amount inputs before submit
        $('input[name="amount[]"]').each(function() {
            try {
                // Get clean numeric value from AutoNumeric
                var cleanValue = $(this).autoNumeric('get');
                // Set the clean value back
                $(this).val(cleanValue);
            } catch(e) {
                // If AutoNumeric fails, manually clean
                var value = $(this).val().replace(/\./g, '').replace(/,/g, '.');
                $(this).val(value);
            }
        });
        // Allow form to submit normally
        return true;
    });

    // Show error messages if they exist in session
    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    @endif
</script>
@endpush