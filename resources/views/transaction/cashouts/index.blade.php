@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-money-bill-wave me-2"></i>
                List of Cashouts
            </h5>
            <small class="text-muted">Manage payments to insurance companies</small>
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="cashout-table">
                <thead class="table-header">
                    <tr>
                        <th>Cashout Number</th>
                        <th>Date</th>
                        <th>Debit Note</th>
                        <th>Insurance Company</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#cashout-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.cashouts.datatables') }}",
            },
            columns: [
                { 
                    data: 'number', 
                    name: 'number',
                    render: function(data, type, row) {
                        return '<a href="{{ route('transaction.cashouts.index') }}/' + row.id + '">' + data + '</a>';
                    }
                },
                { 
                    data: 'date_display', 
                    name: 'date_display' 
                },
                { 
                    data: 'debit_note_number', 
                    name: 'debit_note_number' 
                },
                { 
                    data: 'insurance_name', 
                    name: 'insurance_name' 
                },
                { 
                    data: 'amount_display', 
                    name: 'amount_display', 
                    className: 'text-end' 
                },
                { 
                    data: 'status_badge', 
                    name: 'status_badge',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[1, 'desc']], // Order by date desc
            pageLength: 25
        });
    });

    function markAsPaid(id) {
        if (confirm('Are you sure you want to mark this cashout as paid?')) {
            $.ajax({
                url: "{{ url('/api/cashout') }}/" + id + "/mark-paid",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success !== false) {
                        Swal.fire('Success!', response.message, 'success');
                        $('#cashout-table').DataTable().ajax.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to update cashout status', 'error');
                }
            });
        }
    }

    function markAsCancelled(id) {
        if (confirm('Are you sure you want to cancel this cashout?')) {
            $.ajax({
                url: "{{ url('/api/cashout') }}/" + id + "/mark-cancelled",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success !== false) {
                        Swal.fire('Success!', response.message, 'success');
                        $('#cashout-table').DataTable().ajax.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to update cashout status', 'error');
                }
            });
        }
    }

    function viewCashout(id) {
        window.location.href = "{{ route('transaction.cashouts.index') }}/" + id;
    }
</script>
@endpush