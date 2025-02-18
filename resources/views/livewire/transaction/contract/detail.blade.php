<div class="container mx-auto mb-4">
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
        <div class="col-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Detail Contract <b>#{{ $number }}</b>
                </div>
                <form wire:submit="store" autocomplete="off">
                    <div class="card-body">
                        <div class="mb-2">
                            <label for="client" class="form-label @error('client_id') text-danger @enderror">Client<sup class="text-danger">*</sup></label>
                            <div wire:ignore>
                                <select class="form-select select2" id="client_id" wire:model="client_id" data-placeholder="-- select client --">
                                    <option value="">-- select client --</option>
                                    @foreach ($clients as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('client_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="period_start" class="form-label @error('period_start') text-danger @enderror">Period Start<sup class="text-danger">*</sup></label>
                                    <div wire:ignore>
                                        <input type="text" class="form-control datepicker" id="period_start" wire:model="period_start">
                                    </div>
                                    @error('period_start')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="period_end" class="form-label @error('period_end') text-danger @enderror">Period End<sup class="text-danger">*</sup></label>
                                    <div wire:ignore>
                                        <input type="text" class="form-control datepicker" id="period_end" wire:model="period_end" onkeydown="return false">
                                    </div>
                                    @error('period_end')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="insurance" class="form-label @error('insurance_id') text-danger @enderror">Insurance<sup class="text-danger">*</sup></label>
                            <div wire:ignore>
                                <select class="form-select select2" id="insurance_id" wire:model="insurance_id" data-placeholder="-- select insurance --">
                                    <option value="">-- select insurance --</option>
                                    @foreach ($insurances as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('insurance_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="count_of_item" class="form-label @error('count_of_item') text-danger @enderror">Count of Item<sup class="text-danger">*</sup></label>
                            <input type="number" class="form-control form-co" id="count_of_item" wire:model="count_of_item">
                            @error('count_of_item')
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
                                    <label for="currency_id" class="form-label @error('currency_id') text-danger @enderror">Currency<sup class="text-danger">*</sup></label>
                                    <div wire:ignore>
                                        <select class="form-select select2" id="currency_id" wire:model="currency_id" data-placeholder="-- select currency --">
                                            <option value="">-- select currency --</option>
                                            @foreach ($currencies as $c)
                                                <option value="{{ $c->id }}">{{ $c->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('currency_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="currency_rate" class="form-label @error('currency_rate') text-danger @enderror">Rate<sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control text-end rp2" id="currency_rate" wire:model="currency_rate">
                                    @error('currency_rate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="discount" class="form-label @error('discount') text-danger @enderror">Discount<sup class="text-danger">*</sup></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-end rp2-discount" id="discount" wire:model="discount">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('discount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="gross_amount" class="form-label @error('gross_amount') text-danger @enderror">Gross Amount<sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control text-end rp2" id="gross_amount" wire:model="gross_amount">
                                    @error('gross_amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="discount_amount" class="form-label">Discount Amount</label>
                                    <input type="text" class="form-control text-end rp2 readonly" id="discount_amount" readonly>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-2">
                                    <label for="nett_amount" class="form-label">Nett Amount</label>
                                    <input type="text" class="form-control text-end rp2 readonly" id="nett_amount" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('contract.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <livewire:transaction.contract.billing :id="$id" />
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                });
                
                $('#client_id').on('change', function(e) {
                    @this.$set('client_id', e.target.value);
                });
                $('#insurance_id').on('change', function(e) {
                    @this.$set('insurance_id', e.target.value);
                });
                $('#currency_id').on('change', function(e) {
                    @this.$set('currency_id', e.target.value);
                });

                $("#period_start").on("change", function() {
                    @this.set('period_start', $(this).val());
                });
                
                $("#period_end").on("change", function() {
                    @this.set('period_end', $(this).val());
                });

                $("#gross_amount").on("change", function() {
                    @this.set('gross_amount', $(this).val());
                    changeGrossAmount();
                });

                $("#discount").on("change", function() {
                    @this.set('discount', $(this).val());
                    changeGrossAmount();
                });

                function changeGrossAmount() {
                    var grossAmount = $("#gross_amount").autoNumeric('get');
                    var discount = $("#discount").autoNumeric('get');
                    var discountAmount = (grossAmount * discount) / 100;
                    var nettAmount = grossAmount - discountAmount;

                    $("#discount_amount").autoNumeric('set', discountAmount);
                    $("#nett_amount").autoNumeric('set', nettAmount);
                }

                changeGrossAmount();
            });
        </script>
    @endpush
</div>
