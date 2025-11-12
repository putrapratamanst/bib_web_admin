@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Cash & Bank Detail
            <div class="float-end">
                <a href="{{ route('transaction.cash-banks.index') }}" class="btn btn-secondary btn-sm">
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
                    Related Debit Note Billings
                </div>
                <div class="card-body">
                    @php
                    $totalAllocated = $debitNoteBillings->sum('allocated_amount');
                    $totalAvailable = $cashBank->amount - $totalAllocated;
                    @endphp
                    @if($debitNoteBillings->isEmpty())
                    <div class="alert alert-warning">
                        No related debit note billings found.
                    </div>
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
                            <!-- <div class="col-md-3 text-end">
                                @if($totalAvailable > 0)
                                <button type="button" class="btn btn-primary btn-sm" id="allocateAll">
                                    Auto Allocate All
                                </button>
                                @endif
                            </div> -->
                        </div>
                    </div>
                    @if($cashBank->debitNote)
                    <p class="text-muted">No related debit notes.</p>
                    @else
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Debit Note Number</th>
                                <th>Number</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Contract</th>
                                <th>Installment</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Status Alokasi</th>
                                <th>New Allocation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($debitNoteBillings as $debitNoteBilling)
                            <tr>
                                <td>{{ $debitNoteBilling->debitNote->number }}</td>
                                <td>{{ $debitNoteBilling->billing_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($debitNoteBilling->date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($debitNoteBilling->due_date)->format('d M Y') }}</td>
                                <td>{{ $debitNoteBilling->debitNote->contract->number ?? '-' }}</td>
                                <td>{{ str_replace('INST', '', substr(strrchr($debitNoteBilling->billing_number, "-"), 1)) }}</td>
                                <td class="text-end">{{ $debitNoteBilling->debitNote->currency_code ?? 'IDR' }} {{ number_format($debitNoteBilling->amount, 2, ',', '.') }}</td>
                                <td class="text-end">
                                    <div>Allocated: {{ number_format($debitNoteBilling->allocated_amount, 2, ',', '.') }}</div>
                                    <div class="text-muted">Available: {{ number_format($debitNoteBilling->remaining_amount, 2, ',', '.') }}</div>
                                </td>
                                <td>
                                    @php
                                    $maxAllocation = min($totalAvailable, $debitNoteBilling->remaining_amount);
                                    @endphp
                                    <div class="input-group">
                                        <input type="number"
                                            name="allocation[{{ $debitNoteBilling->id }}]"
                                            class="form-control form-control-sm allocation-input"
                                            value="{{ $maxAllocation }}"
                                            max="{{ $maxAllocation }}"
                                            data-cashbank-amount="{{ $totalAvailable }}"
                                            data-billing-amount="{{ $debitNoteBilling->remaining_amount }}"
                                            step="0.01"
                                            {{ $maxAllocation <= 0 ? 'disabled' : '' }}>
                                        <button type="button"
                                            class="btn btn-primary btn-sm save-allocation"
                                            data-billing-id="{{ $debitNoteBilling->id }}"
                                            {{ $maxAllocation <= 0 ? 'disabled' : '' }}>
                                            Save
                                        </button>
                                    </div>
                                    @if($totalAllocated <= 0)
                                        <small class="text-muted d-block mt-1">Fully allocated</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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

        $('.save-allocation').on('click', function() {
            const billingId = $(this).data('billing-id');
            const allocation = $(this).closest('.input-group').find('.allocation-input').val();

            $.ajax({
                url: "{{ route('api.payment-allocations.storeByCashBankID', ['cashbankID' => $cashBank->id]) }}",
                method: 'POST',
                data: {
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

        // Validate allocation input
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
    });
</script>
@endpush