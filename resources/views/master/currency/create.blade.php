@extends('layouts.app')

@section('title', 'Create Currency')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Create Currency
        </div>
        <div class="card-body">
            <form action="{{ route('master.currencies.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" name="code" id="code" class="form-control" required maxlength="3">
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required maxlength="100">
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('master.currencies.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection