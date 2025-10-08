@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Debit Note Billings</h3>
                </div>
                <div class="card-body">
                    <table id="debitNoteBillingsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Contract</th>
                                <th>Debit Note</th>
                                <th>Billing Number</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Posted to Cashout</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#debitNoteBillingsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('api.debit-note-billings.datatables') }}",
            type: 'GET'
        },
        columns: [
            { data: 'contract_number', name: 'contract_number' },
            { data: 'debit_note_number', name: 'debit_note_number' },
            { data: 'billing_number', name: 'billing_number' },
            { data: 'date_formatted', name: 'date' },
            { data: 'due_date_formatted', name: 'due_date' },
            { data: 'amount_formatted', name: 'amount' },
            { data: 'status', name: 'status' },
            { data: 'is_posted_to_cashout', name: 'is_posted_to_cashout' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']], // Order by billing_number desc
        responsive: true,
        lengthChange: false,
        autoWidth: false,
    });
});

function postBillingToCashout(billingId) {
    if (confirm('Are you sure you want to post this billing to cashout?')) {
        $.ajax({
            url: `/api/debit-note-billing/${billingId}/post-to-cashout`,
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
                            $('#debitNoteBillingsTable').DataTable().ajax.reload();
                        }
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire('Error!', message, 'error');
            }
        });
    }
}
</script>
@endpush