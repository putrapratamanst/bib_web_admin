@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Refund Detail</span>
            <a href="{{ route('transaction.refunds.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Cash Bank Number</th>
                            <td>{{ $refund->cashBank->number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Contact</th>
                            <td>{{ $refund->cashBank->contact->display_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $refund->cashBank->display_date ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Chart of Account</th>
                            <td>{{ $refund->cashBank->chartOfAccount->display_name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Refund Amount</th>
                            <td class="text-danger fw-bold">Rp {{ number_format($refund->allocation, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($refund->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($refund->status === 'void')
                                    <span class="badge bg-danger">Void</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($refund->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $refund->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $refund->created_at->format('d-m-Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($refund->status === 'active')
            <div class="mt-3">
                <button type="button" class="btn btn-danger" id="btnVoid">
                    <i class="bi bi-x-circle"></i> Void Refund
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#btnVoid').on('click', function() {
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
                        url: `/api/refunds/{{ $refund->id }}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    text: response.message,
                                    icon: 'success',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                }).then(() => {
                                    window.location.href = "{{ route('transaction.refunds.index') }}";
                                });
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
