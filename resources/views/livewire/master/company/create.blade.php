<div>
    <div class="row">
        <div class="col-md-4">
            <div class="card" style="width: 32rem;">
                <div class="card-header">
                    Create New Company
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
                        <div class="mb-2">
                            <label for="email" class="form-label @error('email') text-danger @enderror">Email</label>
                            <input type="email" class="form-control" id="email" wire:model="email">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="description" class="form-label @error('description') text-danger @enderror">Description</label>
                            <textarea class="form-control" id="description" wire:model="description"></textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button wire:click="$dispatch('closeModal')" class="btn btn-outline-secondary">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
