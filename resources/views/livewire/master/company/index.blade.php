<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <span>List Company</span>
                <button class="btn btn-primary btn-sm" wire:click="$dispatch('openModal', { component: 'master.company.create' })">Create</button>
            </div>
        </div>
        
        <div class="card-body">
            @if(flash()->message)
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-{{ flash()->class ?? "success" }}" role="alert">
                            {{ flash()->message }}
                        </div>
                    </div>
                </div>
            @endif
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
                        <th scope="col">Email</th>
                        <th width="20%">Created At</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $r)
                        <tr wire:key={{ $r->id }}>
                            <td>{{ $r->code }}</td>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->email }}</td>
                            @if ($r->created_at)
                                <td>{{ $r->created_at->format('d M Y H:i:s') }}</td>
                            @else
                                <td></td>
                            @endif
                            <td class="text-center">
                                <a href="{{ route('company.edit', $r->id) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($companies->hasPages())
            <div class="card-footer">
                {{ $companies->links('vendor.livewire.bootstrap') }}
            </div>
        @endif
    </div>
</div>
