@extends('layouts.app')

@section('title', 'Edit Debit Note Billings')

@section('content')
@php
    $grossPremiumDefault = $debitNote->gross_premium;
    if ($grossPremiumDefault === null) {
        $grossPremiumDefault = $debitNote->contract->gross_premium ?? null;
    }

    $discountPercentDefault = $debitNote->discount_percent ?? ($debitNote->contract->discount ?? null);
    $discountAmountDefault = $debitNote->discount_amount ?? ($debitNote->contract->discount_amount ?? null);
    $netPremiumDefault = $debitNote->net_premium_amount;

    if ($netPremiumDefault === null && $grossPremiumDefault !== null) {
        $netPremiumDefault = floatval($grossPremiumDefault) - floatval($discountAmountDefault ?? 0);
    }
@endphp
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
                <input type="hidden" id="total_fees" value="{{ $totalFees }}">

                {{-- Display Debit Note Amount Information --}}
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-info-circle"></i> Billing Amount Information</strong>
                    <div class="mt-2">
                        <small><strong>Total Debit Note Amount:</strong></small><br>
                        <strong id="totalDebitNoteAmount">0,00 {{ $debitNote->currency_code }}</strong>
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
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="total_net_premium_amount" class="form-label">Net Premi</label>
                            <input type="text" class="form-control autonumeric total-premium-input" name="total_net_premium_amount" id="total_net_premium_amount" value="{{ old('total_net_premium_amount', $netPremiumDefault) }}" readonly style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>

                <div style="max-height: 420px; overflow-y: auto;">
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
                    <div class="row border p-3 mb-3 rounded billing-block {{ $hasFees ? 'border-warning' : '' }}" data-index="{{ $index }}">
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

                        <input type="hidden"
                               class="billing-amount"
                               name="amount[]"
                               id="amount_{{ $index }}"
                               value="{{ old('amount.' . $index, $hasFees ? $billing->amount - $totalFees : $billing->amount) }}"
                               data-billing-number="{{ $billing->billing_number }}"
                               data-is-first="{{ $isFirstBilling ? '1' : '0' }}"
                               data-fees-included="{{ $hasFees ? '0' : '1' }}">

                        <div class="col-12">
                            <div class="row mt-2">
                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="gross_premium_{{ $index }}" class="form-label">Gross Premium</label>
                                    <input type="text" class="form-control autonumeric premium-input gross-premium" name="gross_premium[]" id="gross_premium_{{ $index }}" value="{{ old('gross_premium.' . $index, $billing->gross_premium ?? $grossPremiumDefault) }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="discount_percent_{{ $index }}" class="form-label">Discount %</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control autonumeric premium-input discount-percent" name="discount_percent[]" id="discount_percent_{{ $index }}" value="{{ old('discount_percent.' . $index, $billing->discount_percent ?? $discountPercentDefault) }}">
                                        <span class="input-group-text" style="font-size: 14px;">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="discount_amount_{{ $index }}" class="form-label">Discount Amount</label>
                                    <input type="text" class="form-control autonumeric premium-input discount-amount" name="discount_amount[]" id="discount_amount_{{ $index }}" value="{{ old('discount_amount.' . $index, $billing->discount_amount ?? $discountAmountDefault) }}">
                                </div>
                            </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="mb-3">
                                        <label for="net_premium_amount_{{ $index }}" class="form-label">Net Premi</label>
                                        <input type="text" class="form-control autonumeric premium-input net-premium @error('amount.' . $index) is-invalid @enderror" name="net_premium_amount[]" id="net_premium_amount_{{ $index }}" value="{{ old('net_premium_amount.' . $index, $billing->net_premium_amount ?? ($hasFees ? $billing->amount - $totalFees : $billing->amount)) }}">
                                        @error('amount.' . $index)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>

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
    const currencyCode = "{{ $debitNote->currency_code }}";
    const totalFees = parseFloat(document.getElementById('total_fees').value || '0');
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

    function parseAmountInput(input) {
        if (!input.value || input.value.trim() === '') {
            return 0;
        }

        try {
            const autoNumericValue = $(input).autoNumeric('get');
            if (autoNumericValue !== null && autoNumericValue !== undefined) {
                return parseFloat(autoNumericValue) || 0;
            }
        } catch (e) {
        }

        const value = input.value.trim().replace(/,/g, '');
        return parseFloat(value) || 0;
    }

    function syncBillingAmountFromNetPremium($block) {
        const $netPremium = $block.find('.net-premium');
        const $amount = $block.find('input[name="amount[]"]');
        if ($netPremium.length === 0 || $amount.length === 0) {
            return;
        }

        const amountValue = parseAmountInput($netPremium.get(0));
        $amount.val(amountValue || 0);
    }

    // Calculate and update total debit note amount from all billing amounts.
    function updateBillingTotals() {
        const amountInputs = document.querySelectorAll('input[name="amount[]"]');
        let totalDebitNoteAmount = 0;

        amountInputs.forEach((input) => {
            let amountValue = parseAmountInput(input);
            const isFirstInstallment = input.dataset.isFirst === '1';
            const feesIncluded = input.dataset.feesIncluded === '1';

            if (isFirstInstallment && totalFees > 0 && !feesIncluded) {
                amountValue += totalFees;
            }

            if (!isNaN(amountValue) && isFinite(amountValue)) {
                totalDebitNoteAmount += amountValue;
            }
        });

        document.getElementById('totalDebitNoteAmount').innerHTML = `${formatCurrency(totalDebitNoteAmount)} ${currencyCode}`;

        return totalDebitNoteAmount;
    }

    // Recalculate totals when Net Premi changes.
    $(document).on('change keyup', '.net-premium', function() {
        syncBillingAmountFromNetPremium($(this).closest('.billing-block'));
        updateBillingTotals();
    });

    // Initialize on page load
    $(document).ready(function() {
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
        
        // Update totals after initialization with slight delay to ensure autoNumeric is ready
        setTimeout(function() {
            $('.billing-block').each(function() {
                recomputeNetPremiumForBlock($(this));
                syncBillingAmountFromNetPremium($(this));
            });
            updateBillingTotals();
        }, 100);
    });

    $(document).on('change keyup', '.gross-premium, .discount-percent, .discount-amount', function() {
        const $block = $(this).closest('.billing-block');
        const source = $(this).hasClass('discount-amount') ? 'amount' : ($(this).hasClass('gross-premium') ? 'gross' : 'percent');
        recomputeNetPremiumForBlock($block, source);
        recomputeTotalPremiums();
    });

    // Handle form submission
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();

        let premiumInvalid = false;
        $('.billing-block').each(function() {
            const grossPremium = $(this).find('.gross-premium').autoNumeric('get');
            const netPremium = $(this).find('.net-premium').val();

            if (grossPremium && netPremium && parseFloat(netPremium) > parseFloat(grossPremium)) {
                premiumInvalid = true;
            }
        });

        if (premiumInvalid) {
            $('#validationAlert').html(`
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i> Error!</strong>
                    Net Amount Premi tidak boleh lebih besar dari Gross Premium.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `).show();
            return false;
        }
        
        $('.billing-block').each(function() {
            syncBillingAmountFromNetPremium($(this));
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
        
        // Disable submit button to prevent double submission
        $('#btnSave').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Submit the form
        this.submit();
    });
</script>
@endpush
