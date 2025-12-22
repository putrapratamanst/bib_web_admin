@extends('layouts.app')

@section('title', 'Detail Debit Note')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Detail Debit Note
        </div>
        <form autocomplete="off" method="POST" id="formCreate">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">DN Number<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="number" id="number" value="{{ $debitNote->number }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contract_id" class="form-label">Placing Number<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="contract_id" id="contract_id" value="{{ $debitNote->contract->number }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly id="contact" value="{{ $debitNote->contract->contact->display_name }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="billing_address_id" class="form-label">Billing Address<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly id="billing_address_id" value="{{ $debitNote->billingAddress?->name ?? '-' }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="date" id="date" value="{{ $debitNote->date_formatted }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="due_date" id="due_date" value="{{ $debitNote->due_date_formatted }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="installment" class="form-label">Installment<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="installment" id="installment" value="{{ $debitNote->installment }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="currency_code" class="form-label">Currency<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" readonly name="currency_code" id="currency_code" value="{{ $debitNote->currency_code }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="exchange_rate" class="form-label">Exchange Rate<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$debitNote->currency_code}}</span>
                                <input type="text" class="form-control autonumeric text-end" readonly name="exchange_rate" id="exchange_rate" value="{{ $debitNote->exchange_rate_formatted }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$debitNote->currency_code}}</span>
                                <input type="text" class="form-control text-end autonumeric" readonly name="amount" id="amount" value="{{ $debitNote->amount_formatted }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('transaction.debit-notes.index') }}" class="btn btn-secondary">Back</a>

                @if(!$debitNote->is_posted && $debitNote->status === 'active')
                @if ($debitNote->installment == 0)
                <button type="button" class="btn btn-primary" onclick="postDebitNote()" id="btnPost">
                    <i class="fas fa-paper-plane me-1"></i> Post Debit Note
                </button>
                @endif
                @endif
            </div>
        </form>
    </div>
    @if ($debitNote->installment > 0)
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>List Billing</span>
            @if ($debitNote->debitNoteBillings->count() < $debitNote->installment)
                <a href="{{ route('transaction.debit-notes-billing.create', $debitNote->id) }}" class="btn btn-primary btn-sm">Create Billing</a>
                @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-new table-hover table-striped table-bordered">
                    <thead class="table-header">
                        <tr>
                            <th>Number</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($debitNote->debitNoteBillings as $billing)
                        <tr>
                            <td>{{ $billing->billing_number }}</td>
                            <td>{{ $billing->date_formatted }}</td>
                            <td>{{ $billing->due_date_formatted }}</td>
                            <!-- Amount sudah otomatis dikurangi credit notes melalui accessor di model -->
                            <td class="text-end">{{ $debitNote->currency_code }} {{ $billing->amount_formatted }}</td>
                            <td>
                                @if ($billing->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif ($billing->status === 'posted')
                                    <span class="badge bg-success">Posted</span>
                                @else
                                    <span class="badge bg-secondary">{{ $billing->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($billing->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-success me-1" onclick="postBillingToPosted('{{ $billing->id }}')" title="Post Billing">
                                        <i class="fas fa-check-circle"></i> Post
                                    </button>
                                @endif
                                <a href="javascript:void(0);" class="btn btn-sm btn-info" onclick="printBilling('{{ $billing->id }}')" title="Print Billing">
                                    <i class="fas fa-print"></i> Print
                                </a>
                            </td>

                            @empty
                        <tr>
                            <td colspan="6" class="text-center">No billing found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function postDebitNote() {
        if (confirm('Are you sure you want to post this Debit Note? This will automatically create cashouts to insurance companies.')) {
            $('#btnPost').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Posting...');

            $.ajax({
                url: "{{ route('api.debit-notes.post', $debitNote->id) }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success !== false) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                        $('#btnPost').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Post Debit Note');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to post Debit Note';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                    $('#btnPost').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Post Debit Note');
                }
            });
        }
    }

    function postBillingToCashout(billingId) {
        Swal.fire({
            title: 'Post Billing to Cashout',
            text: 'Are you sure you want to post this billing to cashout? This will create a cashout entry.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Post it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable button
                $(`button[onclick="postBillingToCashout('${billingId}')"]`)
                    .prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Posting...');

                $.ajax({
                    url: `/api/debit-note-billing/${billingId}/post-to-cashout`,
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Posted Successfully!',
                                html: `
                                    <div class="text-start">
                                        <p><strong>Message:</strong> ${response.message}</p>
                                        ${response.data && response.data.cashout ? 
                                            `<p><strong>Cashout Number:</strong> ${response.data.cashout.number}</p>
                                             <p><strong>Amount:</strong> ${response.data.cashout.currency_code} ${parseFloat(response.data.cashout.amount).toLocaleString('id-ID', {minimumFractionDigits: 2})}</p>` 
                                            : ''
                                        }
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                            // Re-enable button
                            $(`button[onclick="postBillingToCashout('${billingId}')"]`)
                                .prop('disabled', false)
                                .html('<i class="fas fa-paper-plane"></i> Post to Cashout');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to post billing to cashout';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                        // Re-enable button
                        $(`button[onclick="postBillingToCashout('${billingId}')"]`)
                            .prop('disabled', false)
                            .html('<i class="fas fa-paper-plane"></i> Post to Cashout');
                    }
                });
            }
        });
    }

    function postBillingToPosted(billingId) {
        if (confirm('Are you sure you want to post this billing? This will change status from Pending to Posted.')) {
            $.ajax({
                url: `/api/debit-note-billing/${billingId}/post`,
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMsg, 'error');
                }
            });
        }
    }

    function printBilling(billingId) {
        window.open(`/transaction/billings/print/${billingId}`, '_blank');
    }

</script>
@endpush