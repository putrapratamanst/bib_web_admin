@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Cash & Bank Detail - Payment & Allocation
            <div class="float-end">
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
                    Debit Notes Allocation
                    <div class="float-end">
                        <!-- check if all debit notes have been allocated -->
                        <?php $allAllocated = $paymentAllocations->sum('allocation') >= $cashBank->amount ? 'true' : 'false' ?>
                        @if($allAllocated == 'false')
                        <a href="{{ route('transaction.payment-allocations.create', ['cashbankID' => $cashBank->id]) }} " class="btn btn-primary btn-sm">
                            Add Allocation to Debit Note
                        </a>
                        @else
                        <span class="text-muted">All Debit Notes have been allocated</span>
                        @endif
                    </div>
                </div>

                <!-- data di bawah ini harusnya blm tampil kalo blm input allocation -->
                <div class="card-body">
                    @if($cashBank->paymentAllocation)
                    <p class="text-muted">No related debit notes.</p>
                    @else
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Number</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Allocation</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentAllocations as $alloc)

                            <tr>
                                <td>{{ $alloc->debitNote->number }}</td>
                                <td>{{ $alloc->debitNote->date }}</td>
                                <td>{{ $alloc->debitNote->due_date }}</td>
                                <td class="text-end">{{ $alloc->debitNote->currency_code }}{{ number_format($alloc->debitNote->amount, 2, ',', '.') }}</td>
                                <td class="text-end"><b>{{ $alloc->debitNote->currency_code }}{{ number_format($alloc->allocation, 2, ',', '.') }}</b></td>
                                <td>
                                    @if($alloc->status == 'posted')
                                    <span class="badge bg-success">Posted</span>
                                    @elseif($alloc->status == 'draft')
                                    <span class="badge bg-warning text-dark">Draft</span>
                                    @else
                                    <span class="badge bg-secondary">{{ ucfirst($alloc->debitNote->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($alloc->status == 'draft')
                                    <form action="{{ route('transaction.payment-allocations.post', $alloc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to post this allocation?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Posting</button>
                                    </form>
                                    @else
                                    <span class="text-muted">No actions available</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            <td class="text-end">

            </td>

        </div>
    </div>
</div>
@endsection