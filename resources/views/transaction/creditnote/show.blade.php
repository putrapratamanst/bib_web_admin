@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Credit Note Details
            <div class="float-end">
                @if($creditNote->canBePrinted())
                    <button class="btn btn-success btn-sm me-2" onclick="printCreditNote('{{ $creditNote->id }}')">
                        <i class="fas fa-print"></i> Print Standard
                    </button>
                    <button class="btn btn-info btn-sm" onclick="printCreditNoteDirectory('{{ $creditNote->id }}')">
                        <i class="fas fa-map"></i> Print Directory
                    </button>
                @else
                    <span class="badge bg-warning">Approval Required</span>
                @endif
            </div>
        </div>        
        <form autocomplete="off" method="POST" id="formCreate">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contract_id" class="form-label">Contract<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="contract_id" id="contract_id" value="{{ $creditNote->contract->number }}">
                        </div>
                    </div>
                                        <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">CN Date<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="date" id="date" value="{{ $creditNote->date_formatted }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">CN Number<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="number" id="number" value="{{ $creditNote->number }}" style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="currency_code" class="form-label">Currency<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="currency_code" id="currency_code" value="{{ $creditNote->currency_code }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="exchange_rate" class="form-label">Exchange Rate<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">Rp</span>
                                <input type="text" class="form-control autonumeric" readonly name="exchange_rate" id="exchange_rate" value="{{ $creditNote->exchange_rate_formatted }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">Rp</span>
                                <input type="text" class="form-control autonumeric" readonly name="amount" id="amount" value="{{ $creditNote->amount_formatted }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-lg-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" readonly name="description" id="description" rows="3">{{ $creditNote->description }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <input type="text" class="form-control" readonly name="status" id="status" value="{{ ucfirst($creditNote->status) }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="approval_status" class="form-label">Approval Status</label>
                            <div class="form-control-plaintext">
                                {!! $creditNote->approval_status_badge !!}
                            </div>
                        </div>
                    </div>
                </div>

                @if($creditNote->approval_status !== 'pending')
                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="approved_by" class="form-label">{{ ucfirst($creditNote->approval_status) }} By</label>
                            <input type="text" class="form-control" readonly value="{{ $creditNote->approvedBy->name ?? 'N/A' }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="approved_at" class="form-label">{{ ucfirst($creditNote->approval_status) }} At</label>
                            <input type="text" class="form-control" readonly value="{{ $creditNote->approved_at_formatted ?? 'N/A' }}">
                        </div>
                    </div>
                </div>
                
                @if($creditNote->approval_notes)
                <div class="row">
                    <div class="col-md-8 col-lg-6">
                        <div class="mb-3">
                            <label for="approval_notes" class="form-label">Approval Notes</label>
                            <textarea class="form-control" readonly rows="3">{{ $creditNote->approval_notes }}</textarea>
                        </div>
                    </div>
                </div>
                @endif
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('transaction.credit-notes.index') }}" class="btn btn-secondary">Back</a>

                @if($creditNote->canBeEdited())
                    <a href="{{ route('transaction.credit-notes.edit', $creditNote->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
                
                @if($creditNote->canBeApproved() && auth()->user()->canApproveCreditNotes())
                    <button type="button" class="btn btn-success" onclick="approveCreditNote('{{ $creditNote->id }}')">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" onclick="rejectCreditNote('{{ $creditNote->id }}')">
                        <i class="fas fa-times"></i> Reject
                    </button>
                @elseif($creditNote->canBeApproved() && !auth()->user()->canApproveCreditNotes())
                    <span class="text-muted"><i class="fas fa-info-circle"></i> Only users with approver role can approve this Credit Note</span>
                @endif
                
                @if($creditNote->canBePrinted())
                    <button type="button" class="btn btn-primary" onclick="printCreditNote('{{ $creditNote->id }}')">
                        <i class="fas fa-print"></i> Print
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function approveCreditNote(creditNoteId) {
    const notes = prompt('Enter approval notes (optional):');
    
    if (confirm('Are you sure you want to approve this Credit Note?')) {
        $.ajax({
            url: `/api/credit-note/${creditNoteId}/approve`,
            type: 'POST',
            data: {
                notes: notes,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Credit Note approved successfully!');
                location.reload();
            },
            error: function(xhr) {
                let message = 'Error: ';
                if (xhr.status === 403) {
                    message += 'You are not authorized to perform this action. Only users with approver role can approve Credit Notes.';
                } else {
                    message += xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred';
                }
                alert(message);
            }
        });
    }
}

function rejectCreditNote(creditNoteId) {
    const notes = prompt('Enter rejection reason:');
    
    if (notes && confirm('Are you sure you want to reject this Credit Note?')) {
        $.ajax({
            url: `/api/credit-note/${creditNoteId}/reject`,
            type: 'POST',
            data: {
                notes: notes,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Credit Note rejected!');
                location.reload();
            },
            error: function(xhr) {
                let message = 'Error: ';
                if (xhr.status === 403) {
                    message += 'You are not authorized to perform this action. Only users with approver role can reject Credit Notes.';
                } else {
                    message += xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred';
                }
                alert(message);
            }
        });
    }
}

function printCreditNote(creditNoteId) {
    window.location.href = `/transaction/credit-notes/${creditNoteId}/print`;
}

function printCreditNoteDirectory(creditNoteId) {
    window.location.href = `/transaction/credit-notes/${creditNoteId}/print-directory`;
}
</script>
@endpush