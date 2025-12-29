@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Cash & Bank Detail
            <div class="float-end">
                <a href="{{ route('transaction.cash-banks.print', $cashBank->id) }}" class="btn btn-primary btn-sm" target="_blank">
                    <i class="bi bi-printer"></i> Print
                </a>
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
           
        </div>
    </div>
</div>
@endsection
