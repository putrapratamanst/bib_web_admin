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
                <span>List Payment Allocation</span>
                <a href="{{ route('transaction.payment-allocation.create') }}" class="btn btn-primary btn-sm" wire:navigate>Create</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover table-striped table-sm">
                <thead>
                    <tr>
                        <th width="15%">Number</th>
                        <th>Cash Transaction</th>
                        <th>Date</th>
                        <th>Amount Allocation</th>
                  </tr>
                </thead>
                <tbody>
                    @forelse ($paymentAllocations as $r)
                        <tr wire:key={{ $r->id }}>
                            <td>{{ $r->number }}</td>
                            <td>{{ $r->cashTransaction->number ?? "" }}</td>
                            <td>{{ $r->date }}</td>
                            <td class="text-end">
                                {{ number_format($r->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>