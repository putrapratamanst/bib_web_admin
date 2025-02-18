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
                    Create New Contact
                </div>
                <form wire:submit="store" autocomplete="off">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="code" class="form-label @error('code') text-danger @enderror">Code<sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control" id="code" wire:model="code">
                                    @error('code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="contact_type" class="form-label @error('contact_type') text-danger @enderror">Contact Type<sup class="text-danger">*</sup></label>
                                    <div wire:ignore>
                                        <select class="form-select" id="contact_type" wire:model="contact_type" data-placeholder="-- select contact type --">
                                            <option value="">-- select contact type --</option>
                                            <option value="client">Client</option>
                                            <option value="agent">Agent</option>
                                            <option value="insurance">Insurance</option>
                                        </select>
                                    </div>
                                    @error('contact_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label for="name" class="form-label @error('name') text-danger @enderror">Name<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="name" wire:model="name">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="email" class="form-label @error('email') text-danger @enderror">Email<sup class="text-danger">*</sup></label>
                                    <input type="email" class="form-control" id="email" wire:model="email">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="phone" class="form-label @error('phone') text-danger @enderror">Phone<sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control" id="phone" wire:model="phone">
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="address" class="form-label @error('address') text-danger @enderror">Address<sup class="text-danger">*</sup></label>
                            <textarea class="form-control" id="address" wire:model="address"></textarea>
                            @error('address')
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

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="account_mapping_receivable" class="form-label @error('account_mapping_receivable') text-danger @enderror">
                                        Account Receivable<sup class="text-danger">*</sup>
                                    </label>
                                    <div wire:ignore>
                                        <select class="form-select" id="account_mapping_receivable" wire:model="account_mapping_receivable" data-placeholder="-- select account --">
                                            <option value="">-- select account --</option>
                                            @foreach($chartOfAccountsReceivable as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('account_mapping_receivable')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="account_mapping_payable" class="form-label @error('account_mapping_payable') text-danger @enderror">
                                        Account Payable<sup class="text-danger">*</sup>
                                    </label>
                                    <div wire:ignore>
                                        <select class="form-select" id="account_mapping_payable" wire:model="account_mapping_payable" data-placeholder="-- select account --">
                                            <option value="">-- select account --</option>
                                            @foreach($chartOfAccountsPayable as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('account_mapping_payable')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('contact.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#contact_type').select2({
                    theme: 'bootstrap-5',
                    placeholder: "-- select contact type --"
                });
                $('#contact_type').on('change', function(e) {
                    @this.$set('contact_type', e.target.value);
                });

                $('#account_mapping_receivable').select2({
                    theme: 'bootstrap-5',
                    placeholder: "-- select account --"
                });
                $('#account_mapping_receivable').on('change', function(e) {
                    @this.$set('account_mapping_receivable', e.target.value);
                });

                $('#account_mapping_payable').select2({
                    theme: 'bootstrap-5',
                    placeholder: "-- select account --"
                });
                $('#account_mapping_payable').on('change', function(e) {
                    @this.$set('account_mapping_payable', e.target.value);
                });
            });
        </script>
    @endpush
</div>
