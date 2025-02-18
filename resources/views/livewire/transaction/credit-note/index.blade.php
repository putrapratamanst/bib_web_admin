<div>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <span>List Credit Note</span>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCreditNote">Create Credit Note</button>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-between mb-4 gap-2">
                    <div class="col">
                        <select wire:model.live="perPage" class="form-select w-auto">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                    </div>
                    <div class="col col-md-4 col-lg-3">
                        <input wire:model.live="search" type="text" class="form-control" placeholder="Search...">
                    </div>
                </div>

                <table class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                        <tr>
                            <th width="15%">Number</th>
                            <th>Billing</th>
                            <th width="12%">Date</th>
                            <th width="15%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($creditNotes as $b)
                        <tr wire:key={{ $b->id }}>
                            <td>{{ $b->number }}</td>
                            <td>{{ $b->billing->number }}</td>
                            <td>{{ $b->date }}</td>
                            <td class="text-end">{{ $b->amount }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($creditNotes->hasPages())
                <div class="card-footer">
                    {{ $creditNotes->links('vendor.livewire.bootstrap') }}
                </div>
            @endif
        </div>
    </div>

    @livewire('transaction.credit-note.create')
</div>
