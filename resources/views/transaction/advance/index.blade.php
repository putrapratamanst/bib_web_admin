@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Advance Payment List</span>
            <a href="{{ route('transaction.advances.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Add New Advance
            </a>
        </div>
        <div class="card-body">
            <table id="advanceTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Cash Bank Number</th>
                        <th>Contact</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
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
        $('#advanceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.advances.datatables') }}",
            columns: [
                { data: 'cash_bank_number', name: 'cash_bank_number' },
                { data: 'contact_name', name: 'contact_name' },
                { data: 'cash_bank_date', name: 'cash_bank.date' },
                { 
                    data: 'allocation', 
                    name: 'allocation',
                    render: function(data) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(data);
                    }
                },
                { data: 'description', name: 'description', defaultContent: '-' },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        let badge = 'secondary';
                        if (data === 'active') badge = 'success';
                        if (data === 'void') badge = 'danger';
                        return '<span class="badge bg-' + badge + '">' + data + '</span>';
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let actions = `
                            <a href="/transaction/advances/${data}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                        `;
                        if (row.status === 'active') {
                            actions += `
                                <button class="btn btn-danger btn-sm" onclick="voidAdvance('${data}')">
                                    <i class="bi bi-x-circle"></i> Void
                                </button>
                            `;
                        }
                        return actions;
                    }
                }
            ]
        });
    });

    function voidAdvance(id) {
        Swal.fire({
            text: 'Are you sure want to void this advance payment?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, void it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/advances/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            $('#advanceTable').DataTable().ajax.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            text: 'Failed to void advance: ' + xhr.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
