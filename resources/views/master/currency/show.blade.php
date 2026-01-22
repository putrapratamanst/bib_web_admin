@extends('layouts.app')

@section('title', 'View Currency')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>View Currency</span>
            <a href="{{ route('master.currencies.edit', $currency) }}" class="btn btn-warning">Edit</a>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" class="form-control" value="{{ $currency->code }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="{{ $currency->name }}" readonly>
            </div>
            <a href="{{ route('master.currencies.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection