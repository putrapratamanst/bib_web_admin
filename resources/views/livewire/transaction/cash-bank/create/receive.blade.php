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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Create Receive Money
                </div>
                <form wire:submit="store" autocomplete="off">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="client" class="form-label @error('chart_of_account_id') text-danger @enderror">
                                        Deposit To<sup class="text-danger">*</sup>
                                    </label>
                                    <div wire:ignore>
                                        <select class="form-select select2" id="chart_of_account_id" wire:model="chart_of_account_id" data-placeholder="-- select cash account --">
                                            <option value="">-- select cash account --</option>
                                            @foreach ($listChartOfAccountsCashBank as $c)
                                                <option value="{{ $c->id }}">{{ $c->display_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('chart_of_account_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="client" class="form-label @error('contact_id') text-danger @enderror">
                                        Payer <sup class="text-danger">*</sup>
                                    </label>
                                    <div wire:ignore>
                                        <select class="form-select select2" id="contact_id" wire:model="contact_id" data-placeholder="-- select contact --">
                                            <option value="">-- select contact --</option>
                                            @foreach ($listContacts as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('contact_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="date" class="form-label @error('date') text-danger @enderror">
                                        Transaction Date<sup class="text-danger">*</sup>
                                    </label>
                                    <div wire:ignore>
                                        <input type="text" class="form-control datepicker" id="date" wire:model="date" onkeydown="return false">
                                    </div>
                                    @error('date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="number" class="form-label @error('number') text-danger @enderror">
                                        Transaction No<sup class="text-danger">*</sup>
                                    </label>
                                    <div>
                                        <input type="text" class="form-control" id="number" wire:model="number" placeholder="[Auto]">
                                    </div>
                                    @error('number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="reference" class="form-label @error('reference') text-danger @enderror">
                                        Reference
                                    </label>
                                    <div>
                                        <input type="text" class="form-control" id="reference" wire:model="reference">
                                    </div>
                                    @error('reference')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="client" class="form-label @error('currency_id') text-danger @enderror">
                                        Currency <sup class="text-danger">*</sup>
                                    </label>
                                    <div wire:ignore>
                                        <select class="form-select select2" id="currency_id" wire:model="currency_id" data-placeholder="-- select currency --">
                                            <option value="">-- select currency --</option>
                                            @foreach ($listCurrencies as $c)
                                                <option value="{{ $c->id }}">{{ $c->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('currency_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <label for="exchange_rate" class="form-label @error('exchange_rate') text-danger @enderror">
                                        Exchange Rate <sup class="text-danger">*</sup>
                                    </label>
                                    <input type="text" class="form-control text-end rp2" id="exchange_rate" wire:model="exchange_rate">
                                    @error('exchange_rate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <button wire:click.prevent="addCashBankDetail" class="btn btn-sm btn-primary mb-2">Add Row</button>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Receive From</th>
                                    <th>Description</th>
                                    <th width="20%">Amount</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cash_bank_details as $row)
                                <tr>
                                    <td>
                                        <div wire:ignore>
                                            <select id="chart_of_account_id{{ $row['row_number'] }}" wire:model="cash_bank_details.{{ $row['row_number'] }}.chart_of_account_id" class="form-select" data-placeholder="-- select chart of account --" onchange="onChangeChartOfAccountDetail({{ $row['row_number'] }})">
                                                <option value="">-- select chart of account --</option>
                                                @foreach ($listChartOfAccounts as $c)
                                                    <option value="{{ $c->id }}">{{ $c->display_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @error('cash_bank_details.' . $row['row_number'] . '.chart_of_account_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input id="description{{ $row['row_number'] }}" type="text" wire:model="cash_bank_details.{{ $row['row_number'] }}.description" class="form-control" />
                                        @error('cash_bank_details.' . $row['row_number'] . '.description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text" wire:model="cash_bank_details.{{ $row['row_number'] }}.amount" class="form-control text-end amount" />
                                        @error('cash_bank_details.' . $row['row_number'] . '.amount')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="text-center">
                                        <button type="button" wire:click="removeDetail({{ $row['row_number'] }})" class="btn btn-outline-danger btn-sm">Remove{{ $row['row_number'] }}</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-success">Create Receive Money</button>
                        <a href="{{ route('transaction.cash-bank.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            var addRow = false;
            var addRowId = "";

            document.addEventListener('DOMContentLoaded', function () {
                $('#chart_of_account_id1').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                });
            });

            Livewire.on('addRow', (event) => {
                addRow = true;
                addRowId = event.id;
            });

            Livewire.on('removeRow', (event) => {
                alert(event.id);
            });

            // setTimeout to check addRow is true or false
            // setInterval(() => {
            //     if (addRow) {
            //         $('#chart_of_account_id' + addRowId).select2({
            //             theme: 'bootstrap-5',
            //             width: '100%',
            //         });
            //         addRow = false;
            //         addRowId = "";
            //     }
            // }, 250);

            function onChangeChartOfAccountDetail(row_number) {
                alert(row_number);
            }


            $('#chart_of_account_id').on('change', function(e) {
                @this.$set('chart_of_account_id', e.target.value);
            });

            $('#contact_id').on('change', function(e) {
                @this.$set('contact_id', e.target.value);
            });

            $('#currency_id').on('change', function(e) {
                @this.$set('currency_id', e.target.value);
            });

            $("#date").on("change", function() {
                @this.set('date', $(this).val());
            });

            $("#exchange_rate").on("change", function() {
                @this.set('exchange_rate', $(this).val());
            });
        </script>
    @endpush
</div>
