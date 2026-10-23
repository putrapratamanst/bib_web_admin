@extends('layouts.app')
@section('title', 'Laporan Keuangan Luminous')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Laporan Keuangan Luminous</h5>
            <small class="text-muted">Periode {{ $from->format('d-m-Y') }} s/d {{ $to->format('d-m-Y') }}</small>
            <div class="float-end">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('luminous.invoices.index') }}">Kembali</a>
            </div>
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
            <form method="GET" class="row align-items-end">
                <div class="col-md-3"><label class="form-label">From Date</label><input type="date" name="from_date" class="form-control" value="{{ $from->toDateString() }}"></div>
                <div class="col-md-3"><label class="form-label">To Date</label><input type="date" name="to_date" class="form-control" value="{{ $to->toDateString() }}"></div>
                <div class="col-md-3"><button class="btn btn-primary">Generate Report</button></div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-new table-bordered mb-4" style="max-width: 520px;">
                <tbody>
                    <tr><th>Pendapatan Basos (ditagihkan)</th><td class="text-end">IDR {{ number_format($invoices->sum('amount'), 2, ',', '.') }}</td></tr>
                    <tr><th>Penerimaan Kas</th><td class="text-end">IDR {{ number_format($payments->sum('amount'), 2, ',', '.') }}</td></tr>
                    <tr><th>Piutang per {{ $to->format('d-m-Y') }}</th><td class="text-end">IDR {{ number_format($receivable, 2, ',', '.') }}</td></tr>
                </tbody>
            </table>

            <h6>Tagihan pada periode</h6>
            <table class="table table-new table-hover table-striped table-bordered mb-4">
                <thead class="table-header"><tr><th>Nomor</th><th>Asuransi</th><th>Date</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                @forelse($invoices as $invoice)
                    <tr><td>{{ $invoice->number }}</td><td>{{ $invoice->recipient }}</td><td>{{ $invoice->date->format('d-m-Y') }}</td><td class="text-end">{{ number_format($invoice->amount, 2, ',', '.') }}</td></tr>
                @empty
                    <tr><td colspan="4" class="text-center">Tidak ada tagihan.</td></tr>
                @endforelse
                </tbody>
            </table>

            <h6>Penerimaan pada periode</h6>
            <table class="table table-new table-hover table-striped table-bordered">
                <thead class="table-header"><tr><th>Date</th><th>Nomor Tagihan</th><th>Asuransi</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr><td>{{ $payment->date->format('d-m-Y') }}</td><td>{{ $payment->invoice?->number }}</td><td>{{ $payment->invoice?->recipient }}</td><td class="text-end">{{ number_format($payment->amount, 2, ',', '.') }}</td></tr>
                @empty
                    <tr><td colspan="4" class="text-center">Tidak ada penerimaan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
