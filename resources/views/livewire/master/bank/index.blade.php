<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <span>List Bank</span>
                <a href="{{ route('bank.create') }}" class="btn btn-primary btn-sm">Create</a>
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
                    <input wire:model.live.debounce.250ms="search" type="text" class="form-control" placeholder="Search...">
                </div>
            </div>
            
            <table class="table table-bordered table-hover table-striped table-sm">
                <thead>
                    <tr>
                        <th width="15%">Code</th>
                        <th scope="col">Name</th>
                        <th width="20%">Created At</th>
                        <th width="10%">Option</th>
                  </tr>
                </thead>
                <tbody>
                    @forelse ($banks as $r)
                        <tr wire:key={{ $r->id }}>
                            <td>{{ $r->code }}</td>
                            <td>{{ $r->name }}</td>
                            @if ($r->created_at)
                                <td>{{ $r->created_at->format('d M Y H:i:s') }}</td>
                            @else
                                <td></td>
                            @endif
                            <td class="text-center">
                                <a href="{{ route('bank.edit', $r->id) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($banks->hasPages())
            <div class="card-footer">
                {{ $banks->links('vendor.livewire.bootstrap') }}
            </div>
        @endif
    </div>
</div>
