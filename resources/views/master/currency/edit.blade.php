@extends('layouts.app')

@section('title', 'Edit Currency')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Edit Currency
        </div>
        <div class="card-body">
            <form action="{{ route('master.currencies.update', $currency) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" name="code" id="code" class="form-control" value="{{ $currency->code }}" required maxlength="3">
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $currency->name }}" required maxlength="100">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('master.currencies.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection