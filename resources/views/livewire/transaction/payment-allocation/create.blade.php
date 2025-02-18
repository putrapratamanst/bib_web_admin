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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Create New Bank
                </div>
                <form wire:submit="store" autocomplete="off">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="number" class="form-label @error('number') text-danger @enderror">Number</label>
                                    <input type="text" class="form-control" id="number" wire:model="number">
                                    @error('number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="cash_transaction_id" class="form-label @error('cash_transaction_id') text-danger @enderror">Cash Transaction<sup class="text-danger">*</sup></label>
                                    <div wire:ignore>
                                        <select class="form-select select2" id="cash_transaction_id" wire:model="cash_transaction_id" data-placeholder="-- select cash transaction --">
                                            <option value="">-- select cash transaction --</option>
                                            @foreach ($cashTransactions as $c)
                                                <option value="{{ $c->id }}">{{ $c->number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('cash_transaction_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="date" class="form-label @error('date') text-danger @enderror">Allocation Date<sup class="text-danger">*</sup></label>
                                    <div wire:ignore>
                                        <input type="text" class="form-control datepicker" id="date" wire:model="date">
                                    </div>
                                    @error('date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="amount" class="form-label @error('amount') text-danger @enderror">Allocation Amount<sup class="text-danger">*</sup></label>
                                    <input type="text" class="form-control text-end rp2" id="amount" wire:model="amount">
                                    @error('amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label for="description" class="form-label @error('description') text-danger @enderror">Description</label>
                                    <textarea class="form-control" id="description" wire:model="description"></textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button wire:click.prevent="addRow" class="btn btn-sm btn-primary mb-2">Add Row</button>
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="60%">Billing</th>
                                            <th width="40%">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rowDetails as $index => $row)
                                        <tr>
                                            <td>
                                                <select wire:model="rowDetails.{{ $index }}.billing_id" class="form-select">
                                                    <option value="">-- pilih billing --</option>
                                                    @foreach($billings as $b)
                                                        <option value="{{ $b->id }}">{{ $b->number }}</option>
                                                    @endforeach
                                                </select>
                                                @error('rowDetails.' . $index . '.billing_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" wire:model="rowDetails.{{ $index }}.amount" class="form-control text-end amount" onchange="calculateAmount()" />
                                                @error('rowDetails.' . $index . '.amount')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a wire:navigate href="{{ route('transaction.payment-allocation.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#cash_transaction_id').on('change', function(e) {
                    @this.$set('cash_transaction_id', e.target.value);
                });

                $("#date").on("change", function() {
                    @this.set('date', $(this).val());
                });

                $("#amount").on("change", function() {
                    @this.set('amount', $(this).val());
                });
            });

            function calculateAmount() {
                // summary amount from element with class 'amount'
                let total = 0;
                $('.amount').each(function() {
                    total += parseInt($(this).val());
                });

                // set total amount to element with id 'amount'
                $('#amount').val(total);
                @this.set('amount', total);
            }

            document.addEventListener('livewire:init', () => {
                Livewire.on('addRow', (event) => {
                    
                });
            });
        </script>
    @endpush
</div>
