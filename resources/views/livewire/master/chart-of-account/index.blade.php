<div class="container">
    @if(flash()->message)
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-{{ flash()->class ?? "success" }}" role="alert">
                    {{ flash()->message }}
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <span>List Chart of Account</span>
                <a href="{{ route('coa.create') }}" class="btn btn-primary btn-sm">Create</a>
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
                        <th>Name</th>
                        <th width="10%">Option</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($chartOfAccounts as $r)
                        <tr wire:key="{{ $r->id }}">
                            <td>{{ $r->number }}</td>
                            <td>{{ $r->name }}</td>
                            <td class="text-center">
                                <a href="{{ route('coa.edit', $r->id) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($chartOfAccounts->hasPages())
            <div class="card-footer">
                {{ $chartOfAccounts->links('vendor.livewire.bootstrap') }}
            </div>
        @endif
    </div>
</div>
