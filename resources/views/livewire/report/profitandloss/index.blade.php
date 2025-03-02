<div>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <span>List Profit & Loss</span>
                    <a class="btn btn-success btn-sm" href="{{ route('report.profitandloss.download') }}">Download Profit & Loss</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover table-striped table-sm">
                    <thead>
                        <tr>
                            <th width="10px">Urutan</th>
                            <th>Uraian</th>
                            <th>Kode</th>
                            <th width="12%">Rincian</th>
                            <th width="12%">Tipe</th>
                            <th width="15%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $d)
                        <tr wire:key={{ $loop->iteration }}>
                            <td class="text-center">{{ $loop->iteration }}</td> {{-- Menampilkan nomor urut --}}
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->code }}</td>
                            <td>    </td>
                            <td>{{ $d->balance_type }}</td>
                            <td>{{ $d->total_debit }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>