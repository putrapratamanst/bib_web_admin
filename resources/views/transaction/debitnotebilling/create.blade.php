@extends('layouts.app')

@section('title', 'Create Debit Note Billing')
@php
    // Calculate default dates from contract period_start
    $defaultDate = $debitNote->contract->period_start ? $debitNote->contract->period_start->format('d-m-Y') : date('d-m-Y');
    $defaultDueDate = $debitNote->contract->period_start ? $debitNote->contract->period_start->addDays(7)->format('d-m-Y') : date('d-m-Y', strtotime('+7 days'));
    $existingBilledAmount = $existingBilledAmount ?? (float) $debitNote->debitNoteBillings()->sum('amount');
    $remainingAvailableAmount = $remainingAvailableAmount ?? max(0, (float) $debitNote->amount - $existingBilledAmount);

    $grossPremiumDefault = $debitNote->gross_premium;
    if ($grossPremiumDefault === null) {
        $grossPremiumDefault = $debitNote->contract->gross_premium ?? null;
    }

    $discountAmountDefault = $debitNote->discount_amount ?? ($debitNote->contract->discount_amount ?? null);
    $discountPercentDefault = $debitNote->discount_percent ?? ($debitNote->contract->discount ?? null);
    $netPremiumDefault = $debitNote->net_premium_amount;

    if ($netPremiumDefault === null && $grossPremiumDefault !== null) {
        $netPremiumDefault = floatval($grossPremiumDefault) - floatval($discountAmountDefault ?? 0);
    }
@endphp
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

                {{-- Alert for validation during input --}}
                <div id="validationAlert" style="display: none;"></div>

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
                                <strong id="totalBilled">{{ number_format($existingBilledAmount, 2, ',', '.') }} {{ $debitNote->currency_code }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small><strong>Remaining Available:</strong></small><br>
                                <strong id="remainingAmount">{{ number_format($remainingAvailableAmount, 2, ',', '.') }} {{ $debitNote->currency_code }}</strong>
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

                <div class="row mb-2">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="total_gross_premium" class="form-label">Gross Premium</label>
                            <input type="text" class="form-control autonumeric total-premium-input" name="total_gross_premium" id="total_gross_premium" value="{{ old('total_gross_premium', $grossPremiumDefault) }}" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="total_discount_percent" class="form-label">Discount %</label>
                            <div class="input-group">
                                <input type="text" class="form-control autonumeric total-premium-input" name="total_discount_percent" id="total_discount_percent" value="{{ old('total_discount_percent', $discountPercentDefault) }}" readonly style="background-color: #e9ecef;">
                                <span class="input-group-text" style="font-size: 14px;">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="total_discount_amount" class="form-label">Discount Amount</label>
                            <input type="text" class="form-control autonumeric total-premium-input" name="total_discount_amount" id="total_discount_amount" value="{{ old('total_discount_amount', $discountAmountDefault) }}" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                    <input type="hidden" name="total_net_premium_amount" id="total_net_premium_amount" value="{{ old('total_net_premium_amount', $netPremiumDefault) }}">
                </div>

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
                        <div class="row border p-3 mb-3 rounded billing-block {{ $i == 1 && $totalFees > 0 ? 'border-warning' : '' }}" data-index="{{ $i - 1 }}">
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
                                    <input type="text" class="form-control @error('billing_number.' . ($i-1)) is-invalid @enderror" name="billing_number[]" id="billing_number_{{ $i }}" value="{{ old('billing_number.' . ($i-1), $debitNote->number . '-INST' . $i) }}" readonly style="background-color: #e9ecef;">
                                    @error('billing_number.' . ($i-1))
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Auto-generated</small>
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="date_{{ $i }}" class="form-label">Date <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control datepicker @error('date.' . ($i-1)) is-invalid @enderror" name="date[]" id="date_{{ $i }}" value="{{ old('date.' . ($i-1), $defaultDate) }}">
                                    @error('date.' . ($i-1))
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="due_date_{{ $i }}" class="form-label">Due Date <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control datepicker @error('due_date.' . ($i-1)) is-invalid @enderror" name="due_date[]" id="due_date_{{ $i }}" value="{{ old('due_date.' . ($i-1), $defaultDueDate) }}">
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
                                        <small class="text-muted d-block mt-2">
                                            Base: {{ number_format($baseInstallmentAmount, 2, ',', '.') }} + Fees: {{ number_format($totalFees, 2, ',', '.') }}
                                        </small>
                                    @endif
                                    <input type="hidden" class="net-premium" name="net_premium_amount[]" id="net_premium_amount_{{ $i }}" value="{{ old('net_premium_amount.' . ($i-1)) }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row mt-2">
                                <div class="col-md-4 col-lg-3">
                                    <div class="mb-3">
                                        <label for="gross_premium_{{ $i }}" class="form-label">Gross Premium</label>
                                        <input type="text" class="form-control autonumeric premium-input gross-premium" name="gross_premium[]" id="gross_premium_{{ $i }}" value="{{ old('gross_premium.' . ($i-1)) }}">
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="mb-3">
                                        <label for="discount_percent_{{ $i }}" class="form-label">Discount %</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control autonumeric premium-input discount-percent" name="discount_percent[]" id="discount_percent_{{ $i }}" value="{{ old('discount_percent.' . ($i-1)) }}">
                                            <span class="input-group-text" style="font-size: 14px;">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="mb-3">
                                        <label for="discount_amount_{{ $i }}" class="form-label">Discount Amount</label>
                                        <input type="text" class="form-control autonumeric premium-input discount-amount" name="discount_amount[]" id="discount_amount_{{ $i }}" value="{{ old('discount_amount.' . ($i-1), $discountAmountDefault) }}">
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                @else
                    {{-- Kalau tidak ada installment, tampilkan form biasa --}}
                    <div class="row billing-block" data-index="0">
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="billing_number" class="form-label">Billing Number <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control @error('billing_number.0') is-invalid @enderror" name="billing_number[]" id="billing_number" value="{{ old('billing_number.0', $debitNote->number . '-INST1') }}" readonly style="background-color: #e9ecef;">
                                @error('billing_number.0')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Auto-generated</small>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control datepicker @error('date.0') is-invalid @enderror" name="date[]" id="date" value="{{ old('date.0', $defaultDate) }}">
                                @error('date.0')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date <sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control datepicker @error('due_date.0') is-invalid @enderror" name="due_date[]" id="due_date" value="{{ old('due_date.0', $defaultDueDate) }}">
                                @error('due_date.0')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="amount" class="form-label">
                                    Amount <sup class="text-danger">*</sup>
                                </label>
                                <input type="text" class="form-control autonumeric @error('amount.0') is-invalid @enderror" name="amount[]" id="amount" value="{{ old('amount.0') }}">
                                @error('amount.0')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <input type="hidden" class="net-premium" name="net_premium_amount[]" id="net_premium_amount_1" value="{{ old('net_premium_amount.0') }}">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row mt-2">
                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="gross_premium_1" class="form-label">Gross Premium</label>
                                    <input type="text" class="form-control autonumeric premium-input gross-premium" name="gross_premium[]" id="gross_premium_1" value="{{ old('gross_premium.0') }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="discount_percent_1" class="form-label">Discount %</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control autonumeric premium-input discount-percent" name="discount_percent[]" id="discount_percent_1" value="{{ old('discount_percent.0') }}">
                                        <span class="input-group-text" style="font-size: 14px;">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="discount_amount_1" class="form-label">Discount Amount</label>
                                    <input type="text" class="form-control autonumeric premium-input discount-amount" name="discount_amount[]" id="discount_amount_1" value="{{ old('discount_amount.0', $discountAmountDefault) }}">
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            <div class="card-footer">
                <a href="{{ route('transaction.debit-notes.show', $debitNote->id) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const debitNoteAmount = {{ $debitNote->amount }};
    const existingBilledAmount = {{ $existingBilledAmount }};
    const currencyCode = "{{ $debitNote->currency_code }}";
    const netPremiumManuallyEdited = {};

    // Initialize currency formatter (US format: 1,234.56)
    function formatCurrency(value) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

    function setAutoNumericValue(target, value) {
        const $target = typeof target === 'string' ? $(target) : $(target);
        if (value === null || value === undefined || value === '') {
            $target.val('');
            return;
        }

        if ($target.data('autoNumeric')) {
            $target.autoNumeric('set', value);
            return;
        }

        $target.val(value);
    }

    function recomputeNetPremiumForBlock($block, source) {
        const grossPremium = $block.find('.gross-premium').autoNumeric('get');
        const discountPercent = $block.find('.discount-percent').autoNumeric('get');
        let discountAmount = $block.find('.discount-amount').autoNumeric('get');

        if ((source === 'gross' || source === 'percent') && grossPremium && discountPercent) {
            discountAmount = parseFloat(grossPremium) * parseFloat(discountPercent) / 100;
            setAutoNumericValue($block.find('.discount-amount'), discountAmount);
        }

        if (!grossPremium) {
            setAutoNumericValue($block.find('.net-premium'), null);
            return;
        }

        const netPremium = parseFloat(grossPremium) - parseFloat(discountAmount || 0);
        setAutoNumericValue($block.find('.net-premium'), netPremium);
    }

    function recomputeTotalPremiums() {
        let totalGrossPremium = 0;
        let totalDiscountAmount = 0;

        $('.billing-block').each(function() {
            const $block = $(this);
            const grossPremium = parseFloat($block.find('.gross-premium').autoNumeric('get') || 0);
            const discountAmount = parseFloat($block.find('.discount-amount').autoNumeric('get') || 0);

            if (!isNaN(grossPremium) && isFinite(grossPremium)) {
                totalGrossPremium += grossPremium;
            }

            if (!isNaN(discountAmount) && isFinite(discountAmount)) {
                totalDiscountAmount += discountAmount;
            }
        });

        const totalDiscountPercent = totalGrossPremium > 0 ? (totalDiscountAmount / totalGrossPremium) * 100 : null;
        const totalNetPremium = totalGrossPremium > 0 ? totalGrossPremium - totalDiscountAmount : null;

        setAutoNumericValue('#total_gross_premium', totalGrossPremium > 0 ? totalGrossPremium : null);
        setAutoNumericValue('#total_discount_amount', totalDiscountAmount > 0 ? totalDiscountAmount : null);
        setAutoNumericValue('#total_discount_percent', totalDiscountPercent);
        $('#total_net_premium_amount').val(totalNetPremium === null ? '' : totalNetPremium);
    }

    // Calculate and update total billed and remaining amount
    function updateBillingTotals() {
        const amountInputs = document.querySelectorAll('input[name="amount[]"]');
        let totalBilled = 0;

        amountInputs.forEach(input => {
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
                // Fallback to manual parsing for US format (1,234.56)
                const value = input.value.trim()
                    .replace(/,/g, '');  // Remove thousand separators (commas)
                numValue = parseFloat(value) || 0;
            }

            // Validate parsed value
            if (!isNaN(numValue) && isFinite(numValue)) {
                totalBilled += numValue;
            }
        });

        totalBilled += existingBilledAmount;

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

        // No validation warning - billing allowed as long as remaining available > 0
        // The server-side validation will check if remaining available = 0
        $('#validationAlert').html('').hide();
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Re-initialize AutoNumeric for all amount inputs to ensure proper formatting (US format)
        $('input[name="amount[]"]').each(function() {
            // Destroy existing autoNumeric instance if any (from global config)
            if ($(this).data('autoNumeric')) {
                $(this).autoNumeric('destroy');
            }
            // Re-initialize with US format
            $(this).autoNumeric('init', {
                aSep: ',',  // Thousand separator: comma
                aDec: '.',  // Decimal separator: dot (period)
                aForm: true,
            });
        });

        $('.premium-input').each(function() {
            if ($(this).data('autoNumeric')) {
                $(this).autoNumeric('destroy');
            }

            $(this).autoNumeric('init', {
                aSep: ',',
                aDec: '.',
                aForm: true,
            });
        });

        $('.total-premium-input').each(function() {
            if ($(this).data('autoNumeric')) {
                $(this).autoNumeric('destroy');
            }

            $(this).autoNumeric('init', {
                aSep: ',',
                aDec: '.',
                aForm: true,
            });
        });
        
        // Update totals after initialization
        updateBillingTotals();
        $('.billing-block').each(function() {
            recomputeNetPremiumForBlock($(this));
        });
    });

    $(document).on('change keyup', '.gross-premium, .discount-percent, .discount-amount', function() {
        const $block = $(this).closest('.billing-block');
        const source = $(this).hasClass('discount-amount') ? 'amount' : ($(this).hasClass('gross-premium') ? 'gross' : 'percent');
        recomputeNetPremiumForBlock($block, source);
        recomputeTotalPremiums();
    });

    // Handle form submission to clean AutoNumeric values
    $('#formCreate').on('submit', function(e) {
        let premiumInvalid = false;
        $('.billing-block').each(function() {
            const grossPremium = $(this).find('.gross-premium').autoNumeric('get');
            const netPremium = $(this).find('.net-premium').val();

            if (grossPremium && netPremium && parseFloat(netPremium) > parseFloat(grossPremium)) {
                premiumInvalid = true;
            }
        });

        if (premiumInvalid) {
            e.preventDefault();
            $('#validationAlert').html(`
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i> Error!</strong>
                    Net Amount Premi tidak boleh lebih besar dari Gross Premium.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `).show();
            return false;
        }

        // Clean all amount inputs before submit
        $('input[name="amount[]"]').each(function() {
            try {
                // Get clean numeric value from AutoNumeric
                var cleanValue = $(this).autoNumeric('get');
                // Set the clean value back
                $(this).val(cleanValue);
            } catch(e) {
                // If AutoNumeric fails, manually clean (remove commas)
                var value = $(this).val().replace(/,/g, '');
                $(this).val(value);
            }
        });

        $('.premium-input').each(function() {
            try {
                const cleanValue = $(this).autoNumeric('get');
                $(this).val(cleanValue);
            } catch (err) {
                const value = $(this).val().replace(/,/g, '');
                $(this).val(value);
            }
        });

        $('.total-premium-input').each(function() {
            try {
                const cleanValue = $(this).autoNumeric('get');
                $(this).val(cleanValue);
            } catch (err) {
                const value = $(this).val().replace(/,/g, '');
                $(this).val(value);
            }
        });
        // Allow form to submit normally
        return true;
    });

    // Show error messages if they exist in session
    // Error and success messages are shown via HTML alerts above

    // Auto-update due_date when date changes (+7 days)
    function setDueDateFromDate(dateInput, dueDateInput) {
        const dateValue = $(dateInput).val();
        if (dateValue) {
            // Parse d-m-Y format
            const parts = dateValue.split('-');
            if (parts.length === 3) {
                const selectedDate = new Date(parts[2], parts[1] - 1, parts[0]);
                if (!isNaN(selectedDate)) {
                    const dueDate = new Date(selectedDate);
                    dueDate.setDate(dueDate.getDate() + 7); // Add 7 days
                    const day = String(dueDate.getDate()).padStart(2, '0');
                    const month = String(dueDate.getMonth() + 1).padStart(2, '0');
                    const year = dueDate.getFullYear();
                    const dueDateString = day + '-' + month + '-' + year;
                    $(dueDateInput).val(dueDateString);
                }
            }
        }
    }

    // Attach change handlers for all date inputs
    @if($debitNote->installment > 0)
        @for ($i = 1; $i <= $debitNote->installment; $i++)
            $('#date_{{ $i }}').on('change', function() {
                setDueDateFromDate('#date_{{ $i }}', '#due_date_{{ $i }}');
            });
        @endfor
    @else
        $('#date').on('change', function() {
            setDueDateFromDate('#date', '#due_date');
        });
    @endif

</script>
@endpush
