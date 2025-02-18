<div>
    <div wire:ignore.self class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="createCreditNote">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Create Credit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="store" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="number" class="form-label">Number</label>
                                    <input type="text" class="form-control" id="number" value="" placeholder="[Auto by System]" readonly />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="billing_number" class="form-label">Billing Number</label>
                                    <div wire:ignore>
                                        <select id="billing_number" data-placeholder="-- choose billing number --">
                                            <option value=""></option>
                                            @foreach ($billings as $c)
                                                <option value="{{ $c->id }}">{{ $c->number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('billing_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Billing Date</label>
                                    <div wire:ignore>
                                        <input type="text" class="form-control datepicker" id="date" placeholder="dd-mm-yyyy" readonly required />
                                    </div>
                                    @error('date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <div wire:ignore>
                                        <input type="text" id="amount" class="form-control rp2 text-end" required />
                                    </div>
                                    @error('amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" wire:model="description" id="description" name="description" rows="3"></textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" wire:click="saveData">Save</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" wire:click="closeModal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        $("#date").on('change', function (e) {
            var data = $('#date').val();
            @this.set('date', data);
        });

        // $("#due_date").on('change', function (e) {
        //     var data = $('#due_date').val();
        //     @this.set('due_date', data);
        // });

        $("#amount").on('change', function (e) {
            var data = $('#amount').val();
            @this.set('amount', data);
        });

        $("#billing_number").select2({
            theme: "bootstrap-5",
            dropdownParent: $('#createCreditNote')
        });

        $('#billing_number').on('change', function (e) {
            var data = $('#billing_number').val();
            @this.set('billing_number', data);
        });
    });

    Livewire.on('creditNote.created', (event) => {
        $("#billing_number").val('').trigger('change');
        $('#date').val('');
        $('#amount').val('');
        $('#description').val('');
        $("#createCreditNote").modal('hide');
    });
</script>
@endpush
