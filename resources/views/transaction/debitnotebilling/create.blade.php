@extends('layouts.app')

@section('title', 'Create Contract')

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

                @if ($debitNote->installment > 0)
                    {{-- Looping sesuai jumlah installment --}}
                    @for ($i = 1; $i <= $debitNote->installment; $i++)
                        <div class="row border p-3 mb-3 rounded">
                            <h6 class="mb-3">Installment {{ $i }}</h6>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="billing_number_{{ $i }}" class="form-label">Billing Number <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control" name="billing_number[]" id="billing_number_{{ $i }}" value="{{ old('billing_number.' . ($i-1)) }}">
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="date_{{ $i }}" class="form-label">Date <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control datepicker" name="date[]" id="date_{{ $i }}" value="{{ old('date.' . ($i-1)) }}">
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="due_date_{{ $i }}" class="form-label">Due Date <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control datepicker" name="due_date[]" id="due_date_{{ $i }}" value="{{ old('due_date.' . ($i-1)) }}">
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="amount_{{ $i }}" class="form-label">Amount <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control currency" name="amount[]" id="amount_{{ $i }}" value="{{ old('amount.' . ($i-1)) }}">
                                </div>
                            </div>
                        </div>
                    @endfor
                @else
                    {{-- Kalau tidak ada installment, tampilkan form biasa --}}
                    <div class="row">
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="billing_number" class="form-label">Billing Number<sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control" name="billing_number[]" id="billing_number" value="{{ old('billing_number.0') }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date<sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control datepicker" name="date[]" id="date" value="{{ old('date.0') }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date<sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control datepicker" name="due_date[]" id="due_date" value="{{ old('due_date.0') }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                                <input type="text" class="form-control currency" name="amount[]" id="amount" value="{{ old('amount.0') }}">
                            </div>
                        </div>
                    </div>
                @endif

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
