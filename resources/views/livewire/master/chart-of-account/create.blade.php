<div class="container mx-auto">
    @if(flash()->message)
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-{{ flash()->class ?? "success" }}" role="alert">
                    {{ flash()->message }}
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Create New Chart of Account
                </div>
                <form wire:submit="store" autocomplete="off">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="number" class="form-label @error('number') text-danger @enderror">Number<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="number" wire:model="number">
                            @error('number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="account_category_id" class="form-label @error('account_category_id') text-danger @enderror">Category<sup class="text-danger">*</sup></label>
                            <div wire:ignore>
                                <select class="form-select" id="account_category_id" wire:model="account_category_id" data-placeholder="-- select contact type --">
                                    <option value="">-- select account category --</option>
                                    @foreach($accountCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('account_category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label @error('name') text-danger @enderror">Name<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="name" wire:model="name">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label @error('description') text-danger @enderror">Description</label>
                            <textarea class="form-control" id="description" wire:model="description"></textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('coa.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#account_category_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: "-- select account category --"
                });
                $('#account_category_id').on('change', function(e) {
                    @this.$set('account_category_id', e.target.value);
                });
            });
        </script>
    @endpush
</div>
