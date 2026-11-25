@extends('layouts.app')
@section('title', 'Tambah Tagihan Luminous')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Tambah Tagihan Manual Luminous</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('luminous.receivables.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contact_id" class="form-label">Ditagihkan Kepada</label>
                        <select id="contact_id" name="contact_id" class="form-select" required>
                            <option value="">Pilih client</option>
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" @selected(old('contact_id') === $contact->id)>{{ $contact->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="currency_code" class="form-label">Mata Uang</label>
                        <select id="currency_code" name="currency_code" class="form-select" required>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->code }}" @selected(old('currency_code', 'IDR') === $currency->code)>{{ $currency->code }} - {{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="exchange_rate" class="form-label">Nilai Tukar</label>
                        <input id="exchange_rate" name="exchange_rate" type="number" step="0.01" min="0.01" class="form-control" value="{{ old('exchange_rate', 1) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date" class="form-label">Tanggal Tagihan</label>
                        <input id="date" name="date" type="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="due_date" class="form-label">Jatuh Tempo</label>
                        <input id="due_date" name="due_date" type="date" class="form-control" value="{{ old('due_date', now()->addDays(30)->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="amount" class="form-label">Nominal</label>
                        <input id="amount" name="amount" type="number" step="0.01" min="0.01" class="form-control" value="{{ old('amount') }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Keterangan</label>
                        <input id="description" name="description" type="text" maxlength="500" class="form-control" value="{{ old('description') }}" required>
                    </div>
                </div>
                <button class="btn btn-primary">Buat Debit Note &amp; Billing</button>
                <a class="btn btn-secondary" href="{{ route('luminous.receivables.index') }}">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
