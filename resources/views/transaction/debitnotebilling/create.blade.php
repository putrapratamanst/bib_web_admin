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
                        <div class="row border p-3 mb-3 rounded">
                            <h6 class="mb-3">Installment {{ $i }}</h6>

                            <div class="col-md-4 col-lg-3">
                                <div class="mb-3">
                                    <label for="billing_number_{{ $i }}" class="form-label">Billing Number <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control @error('billing_number.' . ($i-1)) is-invalid @enderror" name="billing_number[]" id="billing_number_{{ $i }}" value="{{ old('billing_number.' . ($i-1)) }}">
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
                                    <label for="amount_{{ $i }}" class="form-label">Amount <sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control currency @error('amount.' . ($i-1)) is-invalid @enderror" name="amount[]" id="amount_{{ $i }}" value="{{ old('amount.' . ($i-1)) }}">
                                    @error('amount.' . ($i-1))
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                <input type="text" class="form-control currency @error('amount.0') is-invalid @enderror" name="amount[]" id="amount" value="{{ old('amount.0') }}">
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