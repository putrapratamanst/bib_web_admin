@extends('layouts.app')

@section('title', 'Approval')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Approval
            <div class="float-end">
                <span class="badge bg-primary">{{ $contracts->total() }} Contracts</span>
            </div>
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
            <form method="GET" action="{{ route('approval.index') }}" id="approval-filter-form">
                <div class="row align-items-end">
                    <div class="col-lg-3 col-md-4">
                        <label for="contract_type_id" class="form-label">Placing Type</label>
                        <select name="contract_type_id" id="contract_type_id" class="form-control select2" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach($contractTypes as $contractType)
                                <option value="{{ $contractType->id }}" {{ request('contract_type_id') == $contractType->id ? 'selected' : '' }}>{{ $contractType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if($contracts->isEmpty())
                <div class="alert alert-success mb-0"><i class="fas fa-check-circle me-2"></i>Tidak ada Contract yang membutuhkan approval.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-new table-hover table-striped table-bordered" id="approval-table">
                        <thead class="table-header">
                            <tr>
                                <th>Contract Number</th>
                                <th>Contact</th>
                                <th>Placing Type</th>
                                <th>Debit Note Status</th>
                                <th>Credit Note Status</th>
                                <th>Contract Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($contracts as $contract)
                            <tr>
                                <td><a href="{{ route('approval.show', $contract) }}">{{ $contract->number }}</a></td>
                                <td>{{ $contract->contact?->display_name ?? '-' }}</td>
                                <td>{{ $contract->contractType?->name ?? '-' }}</td>
                                <td>
                                    @forelse($contract->debitNotes as $debitNote)
                                        @php
                                            $debitNoteBadge = match($debitNote->approval_status) {
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <div class="mb-1">
                                            <span class="badge bg-{{ $debitNoteBadge }}">{{ ucfirst($debitNote->approval_status ?? 'unknown') }}</span>
                                        </div>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>
                                    @php
                                        $creditNoteStatus = $contract->creditNotes->contains('approval_status', 'rejected')
                                            ? 'rejected'
                                            : ($contract->creditNotes->contains('approval_status', 'pending')
                                                ? 'pending'
                                                : ($contract->creditNotes->contains('approval_status', 'approved') ? 'approved' : null));
                                        $creditNoteBadge = match($creditNoteStatus) {
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    @if($creditNoteStatus)
                                        <span class="badge bg-{{ $creditNoteBadge }}">{{ ucfirst($creditNoteStatus) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                @php
                                    $contractBadge = match($contract->approval_status) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <td class="text-center"><span class="badge bg-{{ $contractBadge }}">{{ ucfirst($contract->approval_status ?? 'unknown') }}</span></td>
                                <td class="text-center"><a href="{{ route('approval.show', $contract) }}" class="btn btn-primary btn-sm">Review</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $contracts->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
