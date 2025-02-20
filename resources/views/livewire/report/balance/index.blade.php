<div>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <span>List Balance</span>
                    <a class="btn btn-success btn-sm" href="{{ route('report.balance.download') }}">Download Balance</a>
                </div>
            </div>
            <div class="card-body">
                <!-- <div class="d-flex flex-column flex-md-row justify-between mb-4 gap-2">
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
                </div> -->

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
