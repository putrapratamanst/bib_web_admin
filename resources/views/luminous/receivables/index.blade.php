@extends('layouts.app')
@section('title', 'Tagihan Luminous')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0"><i class="fas fa-file-invoice me-2"></i>Tagihan Luminous</h5>
                <small class="text-muted">Engineering Fee otomatis dan tagihan manual Luminous.</small>
            </div>
            <div>
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('report.piutang.index') }}">A/R Report</a>
                {{-- <a class="btn btn-primary btn-sm" href="{{ route('luminous.receivables.create') }}">Tambah Tagihan</a> --}}
            </div>
        </div>
        <div class="card-body">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            <div class="table-responsive">
                <table class="table table-new table-hover table-striped table-bordered">
                    <thead class="table-header">
                        <tr>
                            <th>Nomor Billing</th>
                            <th>Debit Note</th>
                            <th>Ditagihkan Kepada</th>
                            <th>Tanggal</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Nominal</th>
                            <th>Sumber</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($billings as $billing)
                        <tr>
                            <td>{{ $billing->billing_number }}</td>
                            <td>{{ $billing->debitNote?->number ?? '-' }}</td>
                            <td>{{ $billing->debitNote?->contact?->display_name ?? $billing->debitNote?->contract?->contact?->display_name ?? '-' }}</td>
                            <td>{{ $billing->date?->format('d-m-Y') }}</td>
                            <td>{{ $billing->due_date?->format('d-m-Y') }}</td>
                            <td class="text-end">{{ $billing->debitNote?->currency_code }} {{ number_format((float) $billing->amount, 2, ',', '.') }}</td>
                            <td>{{ $billing->debitNote?->source_cashout_id ? 'Otomatis - Hutang Asuransi' : 'Manual' }}</td>
                            <td>{{ ucfirst($billing->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">Belum ada tagihan Luminous.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $billings->links() }}
        </div>
    </div>
</div>
@endsection
