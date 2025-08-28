@extends('layouts.app')

@section('title', 'Detail Debit Note')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Detail Debit Note
        </div>
        <form autocomplete="off" method="POST" id="formCreate">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">DN Number<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="number" id="number" value="{{ $debitNote->number }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contract_id" class="form-label">Contract Number<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="contract_id" id="contract_id" value="{{ $debitNote->contract->number }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly id="contact" value="{{ $debitNote->contract->contact->display_name }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="date" id="date" value="{{ $debitNote->date_formatted }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="due_date" id="due_date" value="{{ $debitNote->due_date_formatted }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="installment" class="form-label">Installment<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="installment" id="installment" value="{{ $debitNote->installment }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="currency_code" class="form-label">Currency<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="currency_code" id="currency_code" value="{{ $debitNote->currency_code }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="exchange_rate" class="form-label">Exchange Rate<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$debitNote->currency_code}}</span>
                                <input type="text" class="form-control autonumeric text-end" readonly name="exchange_rate" id="exchange_rate" value="{{ $debitNote->exchange_rate_formatted }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$debitNote->currency_code}}</span>
                                <input type="text" class="form-control text-end autonumeric" readonly name="amount" id="amount" value="{{ $debitNote->amount_formatted }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('transaction.debit-notes.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
    @if ($debitNote->installment > 0)
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>List Billing</span>
            <a href="{{ route('transaction.debit-notes-billing.create', $debitNote->id) }}" class="btn btn-primary btn-sm">Create Billing</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($debitNote->debitNoteBillings as $billing)
                        <tr>
                            <td>{{ $billing->number }}</td>
                            <td>{{ $billing->date }}</td>
                            <td>{{ $billing->due_date }}</td>
                            <td class="text-end">{{ $billing->amount }}</td>
                            <td>
                                @if($billing->status === 'unpaid')
                                <span class="badge bg-danger">Unpaid</span>
                                @else
                                <span class="badge bg-success">Paid</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No billing found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection