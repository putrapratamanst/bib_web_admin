@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            List of Billing
            <div class="float-end">
                <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="btn-print-selected" disabled>
                    <i class="fas fa-print me-1"></i> Print Selected
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-new table-hover table-striped table-bordered" id="billing-table">
                <thead class="table-header">
                    <tr>
                        <th class="text-center" style="width: 40px;">
                            <input type="checkbox" id="select-all" />
                        </th>
                        <th>Number</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Contact</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <!-- <th>Actions</th> -->
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
        var selectedBillingIds = new Set();

        var table = $('#billing-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('api.billings.datatables') }}",
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data) {
                        return '<input type="checkbox" class="billing-select" value="' + data + '">';
                    }
                },
                {
                    data: 'number',
                    name: 'number',
                    render: function(data, type, row) {
                        return '<a href="/transaction/billings/' + row.id + '">' + data + '</a>';
                    }
                },
                { data: 'type', name: 'type' },
                { data: 'date_formatted', name: 'date_formatted' },
                { data: 'due_date_formatted', name: 'due_date_formatted' },
                { data: 'contact', name: 'contact' },
                { data: 'amount_formatted', name: 'amount_formatted', className: 'text-end' },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        if (data === 'pending') {
                            return '<span class="badge bg-warning">Pending</span>';
                        }
                        if (data === 'posted') {
                            return '<span class="badge bg-success">Posted</span>';
                        }
                        return '<span class="badge bg-secondary">' + data + '</span>';
                    }
                },
                // {
                //     data: 'id',
                //     name: 'id',
                //     orderable: false,
                //     searchable: false,
                //     render: function(data, type, row) {
                //         let actions = '';
                //         if (row.status === 'pending') {
                //             actions += '<button class="btn btn-sm btn-success me-1 post-billing-btn" data-id="' + data + '" title="Post to Cashout">';
                //             actions += '<i class="fas fa-check-circle"></i> Post</button>';
                //         }
                //         actions += '<button type="button" class="btn btn-sm btn-info me-1 print-billing-btn" data-id="' + data + '" title="Print Billing">';
                //         actions += '<i class="fas fa-print"></i> Print</button>';
                //         actions += '<button type="button" class="btn btn-sm btn-success print-billing-directory-btn" data-id="' + data + '" title="Print Billing Directory">';
                //         actions += '<i class="fas fa-print"></i> Print Directory</button>';
                //         return actions;
                //     }
                // }
            ]
        });

        function updatePrintSelectedState() {
            const hasSelection = selectedBillingIds.size > 0;
            $('#btn-print-selected').prop('disabled', !hasSelection);
        }

        function syncSelectedFromPage() {
            $('.billing-select').each(function() {
                const id = $(this).val();
                if ($(this).is(':checked')) {
                    selectedBillingIds.add(id);
                } else {
                    selectedBillingIds.delete(id);
                }
            });
        }

        function restoreSelectionOnPage() {
            $('.billing-select').each(function() {
                const id = $(this).val();
                if (selectedBillingIds.has(id)) {
                    $(this).prop('checked', true);
                }
            });
        }

        $('#select-all').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.billing-select').prop('checked', isChecked);
            syncSelectedFromPage();
            updatePrintSelectedState();
        });

        $(document).on('change', '.billing-select', function() {
            const total = $('.billing-select').length;
            const checked = $('.billing-select:checked').length;
            $('#select-all').prop('checked', total > 0 && total === checked);
            syncSelectedFromPage();
            updatePrintSelectedState();
        });

        $('#btn-print-selected').on('click', function() {
            const ids = Array.from(selectedBillingIds);

            if (ids.length === 0) {
                alert('Please select at least one Billing to print.');
                return;
            }

            const url = "{{ route('transaction.billings.print-directory-bulk') }}";
            window.open(url + '?ids=' + ids.join(','), '_blank');
        });

        $(document).on('click', '.post-billing-btn', function() {
            postBilling($(this).data('id'));
        });

        $(document).on('click', '.print-billing-btn', function() {
            printBilling($(this).data('id'));
        });

        $(document).on('click', '.print-billing-directory-btn', function() {
            printBillingDirectory($(this).data('id'));
        });

        table.on('draw', function() {
            restoreSelectionOnPage();
            const total = $('.billing-select').length;
            const checked = $('.billing-select:checked').length;
            $('#select-all').prop('checked', total > 0 && total === checked);
            updatePrintSelectedState();
        });

        window.postBilling = function(billingId) {
            if (confirm('Are you sure you want to post this billing to cashout?')) {
                $.ajax({
                    url: '/api/debit-note-billing/' + billingId + '/post',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    data: {
                        id: billingId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        };

        window.printBilling = function(billingId) {
            window.open('/transaction/billings/print/' + billingId, '_blank');
        };

        window.printBillingDirectory = function(billingId) {
            window.open('/transaction/billings/print-directory/' + billingId, '_blank');
        };
    });
</script>
@endpush