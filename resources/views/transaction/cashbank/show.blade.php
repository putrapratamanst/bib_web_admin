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
                {{--
                <a href="{{ route('transaction.cash-banks.edit', $cashBank->id) }}" class="btn btn-warning btn-sm">
                Edit
                </a>
                --}}
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
                    Related Debit Notes
                </div>
                <div class="card-body">
                    @if($cashBank->debitNote)
                    <p class="text-muted">No related debit notes.</p>
                    @else
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Number</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Contract</th>
                                <th>Installment</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $debitNote->number }}</td>
                                <td>{{ $debitNote->date }}</td>
                                <td>{{ $debitNote->due_date }}</td>
                                <td>{{ $debitNote->contract->number ?? '-' }}</td>
                                <td>{{ $debitNote->installment ?? '-' }}</td>
                                <td class="text-end">{{ $debitNote->currency_code }}{{ number_format($debitNote->amount, 2, ',', '.') }}</td>
                                <td>
                                    @if($debitNote->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                    @elseif($debitNote->status == 'unpaid')
                                    <span class="badge bg-warning text-dark">Unpaid</span>
                                    @else
                                    <span class="badge bg-secondary">{{ ucfirst($debitNote->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            <td class="text-end">

                <div class="mt-2">
                    @php
                    $allocation = min($cashBank->amount, $debitNote->amount);
                    $remaining = $debitNote->amount - $allocation;
                    @endphp

                    <input type="number"
                        name="allocation[{{ $debitNote->id }}]"
                        value="{{ $allocation }}"
                        class="form-control form-control-sm text-end"
                        max="{{ $debitNote->amount }}"
                        step="0.01">

                    @if($remaining > 0)
                    <small class="text-danger">
                        Remaining receivable: {{ $debitNote->currency_code }}{{ number_format($remaining, 2, ',', '.') }}
                    </small>
                    @elseif($cashBank->amount > $debitNote->amount)
                    <small class="text-success">
                        Fully paid. Excess cash: {{ $debitNote->currency_code }}{{ number_format($cashBank->amount - $debitNote->amount, 2, ',', '.') }}
                    </small>
                    @endif
                </div>
            </td>

        </div>
    </div>
</div>
@endsection