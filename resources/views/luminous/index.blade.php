@extends('layouts.app')
@section('title', 'Tagihan Luminous')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-file-invoice me-2"></i>List of Tagihan Luminous</h5>
            <small class="text-muted">Tagihan engineering fee dibuat otomatis saat cashout BIB dibayar.</small>
            <div class="float-end">
                <a class="btn btn-outline-secondary btn-sm me-1" href="{{ route('luminous.aging') }}">A/R Aging</a>
                <a class="btn btn-outline-secondary btn-sm me-1" href="{{ route('luminous.report') }}">Laporan Keuangan</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
            <div class="table-responsive">
                <table class="table table-new table-hover table-striped table-bordered" id="luminous-invoices-table">
                    <thead class="table-header">
                        <tr>
                            <th>Nomor</th>
                            <th>Penerima</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Outstanding</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->number }}</td>
                            <td>{{ $invoice->recipient }}</td>
                            <td>{{ $invoice->date->format('d-m-Y') }}</td>
                            <td>{{ $invoice->due_date->format('d-m-Y') }}</td>
                            <td class="text-end">{{ number_format($invoice->amount, 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($invoice->outstanding_amount, 2, ',', '.') }}</td>
                            <td><span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'partial' ? 'warning' : 'danger') }}">{{ ucfirst($invoice->status) }}</span></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary mb-1" href="{{ route('luminous.invoices.print', $invoice) }}" target="_blank">
                                    <i class="fas fa-print me-1"></i>Surat
                                </a>
                                @if($invoice->outstanding_amount > 0)
                                    <form class="d-flex gap-1" method="POST" action="{{ route('luminous.invoices.pay', $invoice) }}">
                                        @csrf
                                        <input class="form-control form-control-sm" style="width:110px" type="date" name="date" value="{{ now()->toDateString() }}" required>
                                        <input class="form-control form-control-sm" style="width:120px" type="number" step="0.01" min="0.01" max="{{ $invoice->outstanding_amount }}" name="amount" placeholder="Bayar" required>
                                        <button class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Catat</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">Belum ada tagihan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $invoices->links() }}</div>
        </div>
    </div>
</div>
@endsection
