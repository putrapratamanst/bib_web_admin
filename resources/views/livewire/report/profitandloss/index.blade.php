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
                        <tr wire:key={{ $d[0] }}>
                            <td class="text-center">{{ $d[0] }}</td>
                            <td>{{ $d[1] }}</td>
                            <td>{{ $d[2] }}</td>
                            <td>{{ $d[3] }}</td>
                            <td>{{ $d[4] }}</td>
                            <td>{{ $d[5] }}</td>
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
