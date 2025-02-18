<x-layouts.app>
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
                    <a href="{{ route('transaction.contract.create') }}" class="btn btn-outline-primary">Create</a>
                </div>
            </div>

            <div class="card-body">
            </div>
        </div>
    </div>
</x-layouts.app>
