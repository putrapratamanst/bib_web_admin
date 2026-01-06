@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Cash & Bank Detail
            <div class="float-end">
                @if($cashBank->type == 'receive')
                <a href="{{ route('transaction.payment-allocations.print', $cashBank->id) }}" class="btn btn-primary btn-sm me-1" target="_blank">
                    <i class="bi bi-printer"></i> Print Jurnal Penerimaan
                </a>
                @else
                <a href="{{ route('transaction.payment-allocations.print-payment', $cashBank->id) }}" class="btn btn-primary btn-sm me-1" target="_blank">
                    <i class="bi bi-printer"></i> Print Jurnal Pembayaran
                </a>
                @endif
                <a href="{{ route('transaction.payment-allocations.index') }}" class="btn btn-secondary btn-sm">
                    Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Number</th>
                    <td>{{ $cashBank->number }}</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td>
                        @if($cashBank->type == 'receive')
                        <span class="badge bg-success">Receive</span>
                        @else
                        <span class="badge bg-danger">Pay</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Contact</th>
                    <td>{{ $cashBank->contact->display_name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>{{ $cashBank->date }}</td>
                </tr>
                <tr>
                    <th>Currency</th>
                    <td>{{ $cashBank->currency_code ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>{{ $cashBank->currency_code }}{{ number_format($cashBank->amount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $cashBank->description ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Reference</th>
                    <td>{{ $cashBank->reference ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if($cashBank->status == 'approved')
                        <span class="badge bg-success">{{ $cashBank->status }}</span>
                        @else
                        {{ $cashBank->status ?? '-' }}
                        @endif
                    </td>
                </tr>
            </table>
            <div class="card mt-4">
                <div class="card-header">
                    @if($cashBank->type == 'receive')
                        Related Debit Note Billings
                    @else
                        Related Hutang Asuransi
                    @endif
                </div>
                <div class="card-body">
                    @php
                    // Total allocated from THIS cash bank (only posted allocations)
                    $totalAllocated = \App\Models\PaymentAllocation::where('cash_bank_id', $cashBank->id)->where('status', 'posted')->sum('allocation');
                    $totalAvailable = $cashBank->amount - $totalAllocated;
                    $isFullyAllocated = $totalAvailable <= 0;
                    $hasAdvance = \App\Models\PaymentAllocation::where('cash_bank_id', $cashBank->id)
                        ->where('type', 'advance')
                        ->where('status', 'posted')
                        ->exists();
                    @endphp
                    
                    @if($hasAdvance)
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-lock-fill"></i>
                        <strong>Advance Applied!</strong> Cash bank ini sudah diterapkan untuk Advance Payment dan tidak bisa dialokasikan untuk pembayaran lain.
                    </div>
                    @elseif($isFullyAllocated)
                    <div class="alert alert-secondary mb-3">
                        <i class="bi bi-check-circle"></i>
                        <strong>Fully Allocated!</strong> Cash bank ini sudah dialokasikan sepenuhnya.
                    </div>
                    @endif
                    
                    @if($cashBank->type == 'receive')
                        @if($debitNoteBillings->isEmpty())
                        <div class="alert alert-warning">
                            No related debit note billings found.
                        </div>
                        @endif
                    @else
                        @if($cashouts->isEmpty())
                        <div class="alert alert-warning">
                            No pending hutang asuransi found.
                        </div>
                        @endif
                    @endif
                    
                    <div class="alert alert-info mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Total Cash Bank:</strong>
                                {{ $cashBank->currency_code }} {{ number_format($cashBank->amount, 2, ',', '.') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Total Allocated:</strong>
                                {{ $cashBank->currency_code }} {{ number_format($totalAllocated, 2, ',', '.') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Available for Allocation:</strong>
                                {{ $cashBank->currency_code }} {{ number_format($totalAvailable, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    
                    @if($cashBank->type == 'receive')
                        <!-- Debit Note Billings Table -->
                        @if(!$debitNoteBillings->isEmpty())
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 40px;">✓</th>
                                    <th>Debit Note Number</th>
                                    <th>Number</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Placing</th>
                                    <th>Installment</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Status Alokasi</th>
                                    <th>New Allocation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($debitNoteBillings as $debitNoteBilling)
                                <tr class="{{ $debitNoteBilling->allocated_amount > 0 ? 'table-success' : '' }}">
                                    <td class="text-center align-middle">
                                        @php
                                        // Check if this is the first installment
                                        preg_match('/-INST(\d+)/i', $debitNoteBilling->billing_number, $matches);
                                        $installmentNumber = isset($matches[1]) ? (int)$matches[1] : 0;
                                        
                                        $displayAmount = $debitNoteBilling->amount;
                                        $displayRemainingAmount = $debitNoteBilling->remaining_amount;
                                        
                                        // Add policy fee and stamp fee for first installment only
                                        //if ($installmentNumber == 1) {
                                          //  $policyFee = $debitNoteBilling->debitNote->contract->policy_fee ?? 0;
                                            // $stampFee = $debitNoteBilling->debitNote->contract->stamp_fee ?? 0;
                                            // $displayAmount += $policyFee + $stampFee;
                                        // } 
                                        
                                        // Get existing write off data for this billing
                                        $existingWriteOff = \App\Models\PaymentAllocation::where('cash_bank_id', $cashBank->id)
                                            ->where('debit_note_billing_id', $debitNoteBilling->id)
                                            ->first();
                                        $isWriteOff = $existingWriteOff && $existingWriteOff->write_off_type !== 'none';
                                        $writeOffAmount = $existingWriteOff->write_off_amount ?? 0;
                                        $writeOffType = $existingWriteOff->write_off_type ?? 'none';
                                        @endphp
                                        @if($debitNoteBilling->allocated_amount > 0)
                                        <input type="checkbox" 
                                            class="form-check-input write-off-check" 
                                            id="writeoff_{{ $debitNoteBilling->id }}"
                                            data-billing-id="{{ $debitNoteBilling->id }}"
                                            data-billing-amount="{{ $displayAmount }}"
                                            data-allocated="{{ $debitNoteBilling->allocated_amount }}"
                                            data-cashbank-total="{{ $cashBank->amount }}"
                                            data-cashbank-available="{{ $totalAvailable }}"
                                            {{ $isWriteOff ? 'checked' : '' }}
                                            title="{{ $isWriteOff ? ($writeOffType == 'loss' ? 'Loss: ' : 'Gain: ') . number_format($writeOffAmount, 2, ',', '.') : 'Klik untuk tutup selisih' }}">
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $debitNoteBilling->debitNote->number }}</td>
                                    <td>{{ $debitNoteBilling->billing_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($debitNoteBilling->date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($debitNoteBilling->due_date)->format('d M Y') }}</td>
                                    <td>{{ $debitNoteBilling->debitNote->contract->number ?? '-' }}</td>
                                    <td>{{ str_replace('INST', '', substr(strrchr($debitNoteBilling->billing_number, "-"), 1)) }}</td>
                                    <td class="text-end">
                                        {{ $debitNoteBilling->debitNote->currency_code ?? 'IDR' }} {{ number_format($displayAmount, 2, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        <div>Allocated: {{ number_format($debitNoteBilling->allocated_amount, 2, ',', '.') }}</div>
                                        <div class="text-muted">Available: {{ number_format($displayRemainingAmount, 2, ',', '.') }}</div>
                                    </td>
                                    <td>
                                        @php
                                        $maxAllocation = min($totalAvailable, $displayRemainingAmount);
                                        @endphp
                                        <div class="input-group">
                                            <input type="number"
                                                name="allocation[{{ $debitNoteBilling->id }}]"
                                                class="form-control form-control-sm allocation-input"
                                                value="{{ $maxAllocation }}"
                                                max="{{ $maxAllocation }}"
                                                data-cashbank-amount="{{ $totalAvailable }}"
                                                data-billing-amount="{{ $displayRemainingAmount }}"
                                                step="0.01"
                                                {{ $maxAllocation <= 0 ? 'disabled' : '' }}>
                                            <button type="button"
                                                class="btn btn-primary btn-sm save-allocation"
                                                data-billing-id="{{ $debitNoteBilling->id }}"
                                                {{ $maxAllocation <= 0 ? 'disabled' : '' }}>
                                                Save
                                            </button>
                                        </div>
                                        @if($totalAvailable <= 0)
                                            <small class="text-muted d-block mt-1">Fully allocated</small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    @else
                        <!-- Hutang Asuransi Table -->
                        @if(!$cashouts->isEmpty())
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nomor Hutang</th>
                                    <th>Insurance</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Debit Note</th>
                                    <th>Installment</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">Status Alokasi</th>
                                    <th>New Allocation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cashouts as $cashout)
                                <tr class="{{ $cashout->allocated_amount > 0 ? 'table-success' : '' }}">
                                    <td>{{ $cashout->number }}</td>
                                    <td>{{ $cashout->insurance->display_name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cashout->date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cashout->due_date)->format('d M Y') }}</td>
                                    <td>{{ $cashout->debitNote->number ?? '-' }}</td>
                                    <td>{{ $cashout->installment_number ?? '-' }}</td>
                                    <td class="text-end">{{ $cashout->currency_code ?? 'IDR' }} {{ number_format($cashout->amount, 2, ',', '.') }}</td>
                                    <td class="text-end">
                                        <div>Allocated: {{ number_format($cashout->allocated_amount, 2, ',', '.') }}</div>
                                        <div class="text-muted">Available: {{ number_format($cashout->remaining_amount, 2, ',', '.') }}</div>
                                    </td>
                                    <td>
                                        @php
                                        $maxAllocation = min($totalAvailable, $cashout->remaining_amount);
                                        @endphp
                                        <div class="input-group">
                                            <input type="number"
                                                name="allocation[{{ $cashout->id }}]"
                                                class="form-control form-control-sm allocation-input-cashout"
                                                value="{{ $maxAllocation }}"
                                                max="{{ $maxAllocation }}"
                                                data-cashbank-amount="{{ $totalAvailable }}"
                                                data-cashout-amount="{{ $cashout->remaining_amount }}"
                                                step="0.01"
                                                {{ $maxAllocation <= 0 ? 'disabled' : '' }}>
                                            <button type="button"
                                                class="btn btn-primary btn-sm save-allocation-cashout"
                                                data-cashout-id="{{ $cashout->id }}"
                                                {{ $maxAllocation <= 0 ? 'disabled' : '' }}>
                                                Save
                                            </button>
                                        </div>
                                        @if($totalAvailable <= 0)
                                            <small class="text-muted d-block mt-1">Fully allocated</small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#allocateAll').on('click', function() {
            Swal.fire({
                title: 'Auto Allocate Payment',
                text: 'This will automatically allocate the available amount to all outstanding debit notes. Continue?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, allocate all',
                cancelButtonText: 'No, cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('api.payment-allocations.storeAll', ['cashbankID' => $cashBank->id]) }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Allocations saved successfully'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred while saving the allocations';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });

        // Save allocation for debit note billing (type receive)
        $('.save-allocation').on('click', function() {
            const billingId = $(this).data('billing-id');
            const allocation = $(this).closest('.input-group').find('.allocation-input').val();

            $.ajax({
                url: "{{ route('api.payment-allocations.storeByCashBankID', ['cashbankID' => $cashBank->id]) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    debit_note_billing_id: billingId,
                    allocation: allocation,
                    cash_bank_id: "{{ $cashBank->id }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Allocation saved successfully'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while saving the allocation';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        });

        // Save allocation for cashout (type pay)
        $('.save-allocation-cashout').on('click', function() {
            const cashoutId = $(this).data('cashout-id');
            const allocation = $(this).closest('.input-group').find('.allocation-input-cashout').val();

            $.ajax({
                url: "{{ route('api.payment-allocations.storeByCashBankIDForCashout', ['cashbankID' => $cashBank->id]) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    cashout_id: cashoutId,
                    allocation: allocation,
                    cash_bank_id: "{{ $cashBank->id }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Allocation saved successfully'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while saving the allocation';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        });

        // Validate allocation input for billing
        $('.allocation-input').on('input', function() {
            const totalAvailable = parseFloat($(this).data('cashbank-amount')); // now this is totalAvailable
            const billingAvailable = parseFloat($(this).data('billing-amount')); // remaining_amount
            const currentValue = parseFloat($(this).val());
            const maxAmount = Math.min(totalAvailable, billingAvailable);

            if (currentValue > maxAmount) {
                $(this).val(maxAmount);
                let message = 'Maksimum alokasi yang tersedia adalah ' + maxAmount.toLocaleString('id-ID', {
                    maximumFractionDigits: 2
                });
                if (totalAvailable < billingAvailable) {
                    message += '\nSisa cash bank yang tersedia: ' + totalAvailable.toLocaleString('id-ID', {
                        maximumFractionDigits: 2
                    });
                }
                Swal.fire({
                    icon: 'warning',
                    text: message
                });
            }
            if (currentValue < 0) {
                $(this).val(0);
            }
        });

        // Validate allocation input for cashout
        $('.allocation-input-cashout').on('input', function() {
            const totalAvailable = parseFloat($(this).data('cashbank-amount'));
            const cashoutAvailable = parseFloat($(this).data('cashout-amount'));
            const currentValue = parseFloat($(this).val());
            const maxAmount = Math.min(totalAvailable, cashoutAvailable);

            if (currentValue > maxAmount) {
                $(this).val(maxAmount);
                let message = 'Maksimum alokasi yang tersedia adalah ' + maxAmount.toLocaleString('id-ID', {
                    maximumFractionDigits: 2
                });
                if (totalAvailable < cashoutAvailable) {
                    message += '\nSisa cash bank yang tersedia: ' + totalAvailable.toLocaleString('id-ID', {
                        maximumFractionDigits: 2
                    });
                }
                Swal.fire({
                    icon: 'warning',
                    text: message
                });
            }
            if (currentValue < 0) {
                $(this).val(0);
            }
        });

        // Handle write off checkbox
        $('.write-off-check').on('change', function() {
            const billingId = $(this).data('billing-id');
            const billingAmount = parseFloat($(this).data('billing-amount'));
            const allocatedAmount = parseFloat($(this).data('allocated'));
            const isChecked = $(this).is(':checked');
            const checkbox = $(this);

            if (isChecked) {
                // Get additional data
                const cashBankTotal = parseFloat($(this).data('cashbank-total'));
                const cashBankAvailable = parseFloat($(this).data('cashbank-available'));
                
                // Calculate difference between billing and allocated
                const billingDifference = billingAmount - allocatedAmount;
                
                let writeOffType = 'none';
                let writeOffAmount = 0;

                if (billingDifference > 0) {
                    // Billing > Allocated = Loss on Collection (customer bayar kurang)
                    writeOffType = 'loss';
                    writeOffAmount = billingDifference;
                } else if (billingDifference < 0) {
                    // Allocated > Billing = Gain on Collection (customer bayar lebih dari billing ini)
                    writeOffType = 'gain';
                    writeOffAmount = Math.abs(billingDifference);
                } else if (cashBankAvailable > 0) {
                    // Billing == Allocated tapi masih ada sisa cash bank
                    // Ini berarti customer bayar lebih dari total tagihan = Gain on Collection
                    writeOffType = 'gain';
                    writeOffAmount = cashBankAvailable;
                }

                if (writeOffType === 'none') {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Difference',
                        text: 'Alokasi sudah sesuai dengan billing dan tidak ada sisa cash bank.'
                    });
                    checkbox.prop('checked', false);
                    return;
                }

                const typeLabel = writeOffType === 'loss' ? 'Loss on Forex Different Rate' : 'Gain on Forex Different Rate';
                const explanation = writeOffType === 'loss' 
                    ? 'Customer membayar kurang dari tagihan'
                    : (billingDifference < 0 
                        ? 'Alokasi lebih besar dari tagihan' 
                        : 'Sisa cash bank yang tidak dialokasikan');
                
                Swal.fire({
                    title: 'Konfirmasi',
                    html: `<p>Billing Amount: <strong>Rp ${billingAmount.toLocaleString('id-ID', {minimumFractionDigits: 2})}</strong></p>
                           <p>Allocated Amount: <strong>Rp ${allocatedAmount.toLocaleString('id-ID', {minimumFractionDigits: 2})}</strong></p>
                           ${cashBankAvailable > 0 && billingDifference === 0 ? '<p>Sisa Cash Bank: <strong>Rp ' + cashBankAvailable.toLocaleString('id-ID', {minimumFractionDigits: 2}) + '</strong></p>' : ''}
                           <hr>
                           <p><small>${explanation}</small></p>
                           <p>Selisih akan dicatat sebagai:</p>
                           <p><strong>${typeLabel}: Rp ${writeOffAmount.toLocaleString('id-ID', {minimumFractionDigits: 2})}</strong></p>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveWriteOff(billingId, writeOffAmount, writeOffType);
                    } else {
                        checkbox.prop('checked', false);
                    }
                });
            } else {
                // Remove write off
                Swal.fire({
                    title: 'Hapus Write Off?',
                    text: 'Write off untuk billing ini akan dihapus.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        saveWriteOff(billingId, 0, 'none');
                    } else {
                        checkbox.prop('checked', true);
                    }
                });
            }
        });

        function saveWriteOff(billingId, amount, type) {
            $.ajax({
                url: "{{ route('api.payment-allocations.writeOff', ['cashbankID' => $cashBank->id]) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    debit_note_billing_id: billingId,
                    write_off_amount: amount,
                    write_off_type: type
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Write off berhasil disimpan'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan write off';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        }
    });
</script>
@endpush