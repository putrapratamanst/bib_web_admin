@extends('layouts.app')

@section('title', 'Review Approval')

@section('content')
<div class="container py-4" data-approval-index-url="{{ route('approval.index') }}">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><a href="{{ route('approval.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Approval</a><h1 class="h3 mt-2 mb-0">Review {{ $contract->number }}</h1></div>
        <span class="badge text-bg-warning">Pending</span>
    </div>

    <div class="accordion" id="approvalSections">
        <div class="accordion-item mb-3 shadow-sm">
            <h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#contractSection">Contract</button></h2>
            <div id="contractSection" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <div class="row g-3">
                        @foreach([
                            'Contract Number' => $contract->number,
                            'Contract Status' => $contract->contract_status,
                            'Type' => $contract->contractType?->name ?? '-',
                            'Contact' => $contract->contact?->display_name ?? '-',
                            'Billing Address' => $contract->billingAddress?->name ?? '-',
                            'Period' => $contract->period,
                            'Currency' => $contract->currency_code,
                            'Coverage Amount' => $contract->coverage_amount_formatted,
                            'Gross Premium' => $contract->gross_premium_formatted,
                            'Discount' => $contract->discount_formatted,
                            'Stamp Fee' => $contract->stamp_fee_formatted,
                            'Net Premium' => $contract->amount_formatted,
                            'Installments' => $contract->installment_count,
                        ] as $label => $value)
                            <div class="col-md-3"><small class="text-muted d-block">{{ $label }}</small><strong>{{ $value ?: '-' }}</strong></div>
                        @endforeach
                        <div class="col-12"><small class="text-muted d-block">Memo</small><div class="border rounded p-2 bg-light">{{ $contract->memo ?: '-' }}</div></div>
                    </div>
                    <hr>
                    <h6>Contract Details</h6>
                    <div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Insurance</th><th>Description</th><th>Share</th><th>Brokerage</th><th>Engineering</th></tr></thead><tbody>
                        @forelse($contract->details as $detail)<tr><td>{{ $detail->insurance?->display_name ?? '-' }}</td><td>{{ $detail->description }}</td><td>{{ $detail->percentage_formatted }}</td><td>{{ $detail->brokerage_fee_formatted }}</td><td>{{ $detail->eng_fee_formatted }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted">No contract details.</td></tr>@endforelse
                    </tbody></table></div>
                </div>
            </div>
        </div>

        <div class="accordion-item shadow-sm">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#billingSection">Debit Note &amp; Debit Note Billing</button></h2>
            <div id="billingSection" class="accordion-collapse collapse">
                <div class="accordion-body">
                    @forelse($contract->debitNotes as $debitNote)
                        <div class="border rounded p-3 mb-3"><div class="d-flex justify-content-between"><h5 class="mb-3">Debit Note: {{ $debitNote->number }}</h5><span class="badge text-bg-warning">{{ ucfirst($debitNote->approval_status) }}</span></div>
                            <div class="row g-3 mb-3"><div class="col-md-3"><small class="text-muted d-block">Date</small>{{ $debitNote->date_formatted }}</div><div class="col-md-3"><small class="text-muted d-block">Due Date</small>{{ $debitNote->due_date_formatted ?: '-' }}</div><div class="col-md-3"><small class="text-muted d-block">Amount</small>{{ $debitNote->amount_formatted }}</div><div class="col-md-3"><small class="text-muted d-block">Operational Status</small>{{ ucfirst($debitNote->status) }}</div></div>
                            <div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead><tr><th>Billing Number</th><th>Date</th><th>Due Date</th><th>Amount</th><th>Operational Status</th><th>Approval</th></tr></thead><tbody>
                                @forelse($debitNote->debitNoteBillings as $billing)<tr><td>{{ $billing->billing_number }}</td><td>{{ $billing->date_formatted }}</td><td>{{ $billing->due_date_formatted ?: '-' }}</td><td>{{ $billing->amount_formatted }}</td><td colspan="2" class="text-center"><span class="badge {{ $billing->status === 'posted' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($billing->status) }}</span></td></tr>@empty<tr><td colspan="6" class="text-center text-muted">No billing.</td></tr>@endforelse
                            </tbody></table></div>
                        </div>
                    @empty
                        <div class="alert alert-info mb-0">Belum ada Debit Note atau Billing untuk Contract ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div id="approvalFeedback" class="mt-3"></div>
    <div class="d-flex justify-content-end gap-2 mt-3"><button class="btn btn-primary px-4" id="submitApproval"><i class="fas fa-paper-plane me-1"></i> Submit</button></div>
</div>

<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="approvalModalLabel">Submit Approval</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><label class="form-label" for="decision">Decision</label><select class="form-select" id="decision"><option value="approve">Approve</option><option value="reject">Reject</option></select><div class="mt-3 d-none" id="reasonWrap"><label class="form-label" for="reason">Reason</label><textarea class="form-control" id="reason" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="confirmApproval">Confirm</button></div></div></div></div>
@endsection

@push('scripts')
<script>
$(function () {
    const approvalIndexUrl = $('.container[data-approval-index-url]').data('approval-index-url');
    const modal = new bootstrap.Modal('#approvalModal');
    const decision = $('#decision');
    const reasonWrap = $('#reasonWrap');
    const submitButton = $('#submitApproval');
    const confirmButton = $('#confirmApproval');
    const feedback = $('#approvalFeedback');

    decision.on('change', function () { reasonWrap.toggleClass('d-none', this.value !== 'reject'); });
    submitButton.on('click', () => modal.show());
    confirmButton.on('click', async function () {
        const selectedDecision = decision.val();
        const reason = $('#reason').val().trim();
        if (selectedDecision === 'reject' && !reason) { $('#reason').addClass('is-invalid').trigger('focus'); return; }
        submitButton.prop('disabled', true); confirmButton.prop('disabled', true).text('Processing...');
        try {
            const response = await fetch('{{ url('/api/approval/contracts/' . $contract->id) }}', { method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, body: JSON.stringify({decision: selectedDecision, reason}) });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Approval gagal diproses.');
            modal.hide(); feedback.html('<div class="alert alert-success">Approval berhasil diproses. Mengalihkan...</div>');
            window.setTimeout(() => window.location.href = approvalIndexUrl, 800);
        } catch (error) {
            feedback.html('<div class="alert alert-danger">' + error.message + '</div>');
            submitButton.prop('disabled', false); confirmButton.prop('disabled', false).text('Confirm');
        }
    });
});
</script>
@endpush
