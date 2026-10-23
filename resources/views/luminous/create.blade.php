@extends('layouts.app')
@section('title', 'Tambah Tagihan Luminous')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-file-invoice me-2"></i>Tambah Tagihan Luminous (Basos)</h5>
            <small class="text-muted">Pilih Hutang Asuransi, total Basos dihitung otomatis</small>
        </div>
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <form method="POST" action="{{ route('luminous.invoices.store') }}">
                @csrf

                <div class="table-responsive mb-2">
                    <table class="table table-new table-hover table-striped table-bordered" id="cashout-table">
                        <thead class="table-header">
                            <tr>
                                <th class="text-center" style="width: 40px;"><input type="checkbox" id="select-all" title="Pilih semua asuransi yang sama dengan baris pertama yang dicentang"></th>
                                <th>Asuransi</th>
                                <th>Nomor Hutang</th>
                                <th>Debit Note</th>
                                <th>Nomor Polis</th>
                                <th>Tertanggung</th>
                                <th>Date</th>
                                <th class="text-end">Basos</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($cashouts as $cashout)
                            @php($contract = $cashout->debitNote?->contract)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="cashout-select" name="cashout_ids[]" value="{{ $cashout->id }}" data-insurance="{{ $cashout->insurance_id }}" data-basos="{{ $cashout->basos }}" @checked(in_array($cashout->id, old('cashout_ids', [])))>
                                </td>
                                <td>{{ $cashout->insurance?->display_name ?? '-' }}</td>
                                <td>{{ $cashout->number }}</td>
                                <td>{{ $cashout->debitNote?->number ?? '-' }}</td>
                                <td>{{ $contract?->policy_number ?: ($contract?->cover_note_number ?? '-') }}</td>
                                <td>{{ $cashout->debitNote?->billingAddress?->name ?: ($contract?->contact?->display_name ?? '-') }}</td>
                                <td>{{ $cashout->date?->format('d-m-Y') }}</td>
                                <td class="text-end">{{ number_format($cashout->basos, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">Tidak ada Hutang Asuransi dengan Basos yang belum ditagihkan.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="text-end fw-bold">Total Basos: IDR <span id="total">0,00</span></p>

                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Nomor Surat/Tagihan</label><input class="form-control" name="number" value="{{ old('number') }}" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Perihal</label><input class="form-control" name="subject" value="{{ old('subject', 'Tagihan Biaya Sosialisasi') }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Kota Surat</label><input class="form-control" name="city" value="{{ old('city', 'Jakarta') }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Tanggal</label><input class="form-control" type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Jatuh Tempo</label><input class="form-control" type="date" name="due_date" value="{{ old('due_date') }}" required></div>
                    <div class="col-12 mb-3"><label class="form-label">Keterangan Tagihan</label><input class="form-control" name="description" value="{{ old('description', 'Biaya Sosialisasi') }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Bank</label><input class="form-control" name="bank_name" value="{{ old('bank_name', 'Mandiri - Kcp Jakarta Botanical Garden') }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Nomor Rekening</label><input class="form-control" name="bank_account" value="{{ old('bank_account', '118-00-0992611-3') }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Atas Nama</label><input class="form-control" name="account_name" value="{{ old('account_name', 'PT. Luminous Risk Management') }}" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Nama Penandatangan</label><input class="form-control" name="signatory_name" value="{{ old('signatory_name') }}" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Jabatan</label><input class="form-control" name="signatory_title" value="{{ old('signatory_title', 'Collection Team') }}" required></div>
                </div>
                <button class="btn btn-primary">Simpan</button>
                <a class="btn btn-secondary" href="{{ route('luminous.invoices.index') }}">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const boxes = [...document.querySelectorAll('.cashout-select')];
    const totalEl = document.getElementById('total');
    const selectAll = document.getElementById('select-all');

    // Satu surat = satu asuransi: setelah ada yang dicentang, baris asuransi lain dikunci.
    function sync() {
        const checked = boxes.filter(b => b.checked);
        const insurance = checked[0]?.dataset.insurance;
        boxes.forEach(b => b.disabled = !!insurance && b.dataset.insurance !== insurance);
        const sum = checked.reduce((t, b) => t + parseFloat(b.dataset.basos), 0);
        totalEl.textContent = sum.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const enabled = boxes.filter(b => !b.disabled);
        selectAll.checked = enabled.length > 0 && enabled.every(b => b.checked);
    }

    boxes.forEach(b => b.addEventListener('change', sync));
    selectAll.addEventListener('change', e => {
        const insurance = boxes.find(b => b.checked)?.dataset.insurance ?? boxes[0]?.dataset.insurance;
        boxes.filter(b => b.dataset.insurance === insurance).forEach(b => b.checked = e.target.checked);
        sync();
    });
    sync();
</script>
@endpush
