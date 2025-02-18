<div class="container mx-auto">
    @if(flash()->message)
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-{{ flash()->class ?? "success" }}" role="alert">
                    {{ flash()->message }}
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Create New Bank
                </div>
                <form wire:submit="store" autocomplete="off">
                    <div class="card-body">
                        <div class="mb-2">
                            <label for="code" class="form-label @error('code') text-danger @enderror">Code</label>
                            <input type="text" class="form-control" id="code" wire:model="code">
                            @error('code')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="name" class="form-label @error('name') text-danger @enderror">Name</label>
                            <input type="text" class="form-control" id="name" wire:model="name">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('bank.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
