<div class="container">
    @if(flash()->message)
        <div class="row">
            <div class="col">
                <div class="alert alert-{{ flash()->class ?? "success" }}" role="alert">
                    {{ flash()->message }}
                </div>
            </div>
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <span>List Contract</span>
                <a href="{{ route('contract.create') }}" class="btn btn-primary btn-sm">Create</a>
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
                <div class="col col-lg-2">
                    <input wire:model.live="search" type="text" class="form-control" placeholder="Search...">
                </div>
            </div>
            
            <table class="table table-bordered table-hover table-striped table-sm">
                <thead>
                    <tr>
                        <th width="15%">Number</th>
                        <th>Client</th>
                        <th>Period</th>
                        <th>Insurance</th>
                        <th>Item(s)</th>
                        <th>Nett Amount</th>
                        <th>Option</th>
                  </tr>
                </thead>
                <tbody>
                    @forelse ($contracts as $r)
                        <tr wire:key={{ $r->id }}>
                            <td>{{ $r->number }}</td>
                            <td>{{ $r->client->name ?? "" }}</td>
                            <td>
                                {{ \Carbon\Carbon::createFromFormat('Y-m-d', $r->period_start)->format('d-m-Y'); }} - {{ \Carbon\Carbon::createFromFormat('Y-m-d', $r->period_end)->format('d-m-Y'); }}
                            </td>
                            <td>{{ $r->insurance->name }}</td>
                            <td>{{ $r->count_of_item }}</td>
                            <td class="text-end">
                                {{ number_format($r->getNettAmountAttribute(), 0, ',', '.') }} ({{ $r->currency->code }})
                            </td>
                            <td class="text-center">
                                <a href="{{ route('contract.detail', $r->id) }}" class="btn btn-outline-primary btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($contracts->hasPages())
            <div class="card-footer">
                {{ $contracts->links('vendor.livewire.bootstrap') }}
            </div>
        @endif
    </div>
</div>
