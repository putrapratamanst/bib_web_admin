@extends('layouts.app')
@section('title', 'A/R Aging Luminous')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-clock me-2"></i>A/R Aging Luminous</h5>
            <small class="text-muted">Piutang Basos per {{ $asOf->format('d-m-Y') }}</small>
            <div class="float-end">
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('luminous.invoices.index') }}">Kembali</a>
            </div>
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
            <form method="GET" autocomplete="off">
                <div class="row">
                    <div class="col-lg-2 col-md-3">
                        <div class="mb-3">
                            <label for="as_of_date" class="form-label">As Of Date</label>
                            <input type="text" name="as_of_date" id="as_of_date" class="form-control datepicker" value="{{ request('as_of_date') }}">
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 d-flex align-items-center justify-content-center">
                        <span class="fw-bold">|</span>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <div class="mb-3">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="text" name="from_date" id="from_date" class="form-control datepicker" value="{{ request('from_date') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <div class="mb-3">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="text" name="to_date" id="to_date" class="form-control datepicker" value="{{ request('to_date') }}">
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 d-flex align-items-center justify-content-center">
                        <span class="fw-bold">|</span>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="mb-3">
                            <label for="insurance_id" class="form-label">Clients / Insurances</label>
                            <select id="insurance_id" name="insurance_id" class="form-select select2" data-placeholder="-- select insurance --">
                                <option value="">-- select insurance --</option>
                                @foreach($insurances as $insurance)
                                    <option value="{{ $insurance->id }}" @selected($insurance->id === $insuranceId)>{{ $insurance->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <button class="btn-primary btn">Generate Report</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-new table-hover table-striped table-bordered">
                    <thead class="table-header">
                        <tr>
                            <th>Asuransi</th>
                            <th>Nomor Tagihan</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th class="text-end">Outstanding</th>
                            <th class="text-end">Current</th>
                            <th class="text-end">1-30</th>
                            <th class="text-end">31-60</th>
                            <th class="text-end">61-90</th>
                            <th class="text-end">&gt; 90</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ $row['invoice']->recipient }}</td>
                            <td>{{ $row['invoice']->number }}</td>
                            <td>{{ $row['invoice']->date->format('d-m-Y') }}</td>
                            <td>{{ $row['invoice']->due_date->format('d-m-Y') }}@if($row['days'] > 0) <small class="text-danger">({{ $row['days'] }} hari)</small>@endif</td>
                            <td class="text-end">{{ number_format($row['outstanding'], 2, ',', '.') }}</td>
                            @foreach(['current', 'd30', 'd60', 'd90', 'd90plus'] as $bucket)
                                <td class="text-end">{{ $row['bucket'] === $bucket ? number_format($row['outstanding'], 2, ',', '.') : '' }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center">Tidak ada piutang outstanding untuk filter ini. Piutang muncul setelah tagihan dibuat di menu Tagihan Luminous.</td></tr>
                    @endforelse
                    </tbody>
                    @if($rows->isNotEmpty())
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end">Total</td>
                            <td class="text-end">{{ number_format($rows->sum('outstanding'), 2, ',', '.') }}</td>
                            @foreach($totals as $total)
                                <td class="text-end">{{ number_format($total, 2, ',', '.') }}</td>
                            @endforeach
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
