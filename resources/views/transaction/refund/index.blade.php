@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Refund List</span>
            <a href="{{ route('transaction.refunds.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Add New Refund
            </a>
        </div>
        <div class="card-body">
            <table id="refundTable" class="table table-bordered table-hover">
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
        $('#refundTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.refunds.datatables') }}",
            columns: [
                { data: 'cash_bank_number', name: 'cash_bank_number', orderable: false },
                { data: 'contact_name', name: 'contact_name', orderable: false },
                { data: 'cash_bank_date', name: 'cash_bank_date' },
                { 
                    data: 'allocation', 
                    name: 'allocation',
                    render: function(data) {
                        // Refund disimpan negatif, tampilkan nilai absolut
                        return 'Rp ' + new Intl.NumberFormat('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(Math.abs(data));
                    }
                },
                { data: 'description', name: 'description', defaultContent: '-' },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        let badge = 'secondary';
                        if (data === 'posted') badge = 'success';
                        if (data === 'void') badge = 'danger';
                        return '<span class="badge bg-' + badge + '">' + data + '</span>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[2, 'desc']]
        });

        // Handle void button click
        $(document).on('click', '.btn-void', function() {
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This refund will be voided!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/refunds/${id}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Voided!', response.message, 'success');
                                $('#refundTable').DataTable().ajax.reload();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to void refund', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
