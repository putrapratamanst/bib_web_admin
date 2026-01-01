@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add New Refund
        </div>
        <form autocomplete="off" id="formRefund">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="advance_id" class="form-label">Select Advance<sup class="text-danger">*</sup></label>
                            <select name="advance_id" id="advance_id" class="form-control" required>
                                <option value=""></option>
                            </select>
                            <small class="text-muted">Only showing Advances with available balance for refund</small>
                        </div>
                    </div>
                </div>
                
                <!-- Advance Details (will be shown after selection) -->
                <div id="advanceDetails" style="display: none;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Cash Bank Number</label>
                                <input type="text" id="cash_bank_number" class="form-control" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Contact</label>
                                <input type="text" id="contact_name" class="form-control" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="text" id="cash_bank_date" class="form-control" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Chart of Account</label>
                                <input type="text" id="chart_of_account" class="form-control" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Advance Amount</label>
                                <input type="text" id="advance_amount" class="form-control" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Total Refunded</label>
                                <input type="text" id="total_refunded" class="form-control text-warning" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Available for Refund</label>
                                <input type="text" id="available_for_refund" class="form-control text-success fw-bold" readonly />
                                <input type="hidden" id="available_for_refund_raw" />
                            </div>
                        </div>
                    </div>
                    
                    <hr />
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Refund amount akan otomatis menggunakan seluruh available balance: <strong><span id="refund_amount_display">Rp 0</span></strong>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="3" class="form-control" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Refund
                </button>
                <a href="{{ route('transaction.refunds.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for Advance
        $('#advance_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Select Advance --',
            ajax: {
                url: "{{ route('api.refunds.advance.select2') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                },
                cache: true
            }
        });

        // On Advance selection change
        $('#advance_id').on('change', function() {
            const advanceId = $(this).val();
            
            if (!advanceId) {
                $('#advanceDetails').hide();
                return;
            }

            // Get advance details
            $.ajax({
                url: `/api/refunds/advance/${advanceId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Fill in the details
                        $('#cash_bank_number').val(data.cash_bank_number);
                        $('#contact_name').val(data.contact_name);
                        $('#cash_bank_date').val(data.display_date);
                        $('#chart_of_account').val(data.chart_of_account);
                        $('#advance_amount').val('Rp ' + data.advance_amount_formatted);
                        $('#total_refunded').val('Rp ' + data.total_refunded_formatted);
                        $('#available_for_refund').val('Rp ' + data.available_for_refund_formatted);
                        $('#available_for_refund_raw').val(data.available_for_refund);
                        $('#refund_amount_display').text('Rp ' + data.available_for_refund_formatted);
                        
                        // Show the details section
                        $('#advanceDetails').show();
                    }
                },
                error: function(xhr) {
                    alert('Failed to get advance details');
                    console.error(xhr);
                }
            });
        });

        // Form submit
        $('#formRefund').on('submit', function(e) {
            e.preventDefault();
            
            const advanceId = $('#advance_id').val();
            const refundAmount = parseFloat($('#available_for_refund_raw').val());
            const description = $('#description').val();
            
            // Validation
            if (!advanceId) {
                alert('Please select Advance');
                return;
            }
            
            if (!refundAmount || refundAmount <= 0) {
                alert('Available amount must be greater than 0');
                return;
            }
            
            // Disable submit button
            $('#btnSubmit').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Saving...');
            
            // Submit via AJAX
            $.ajax({
                url: "{{ route('api.refunds.store') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    advance_id: advanceId,
                    refund_amount: refundAmount,
                    description: description
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('transaction.refunds.index') }}";
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to save refund';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    $('#btnSubmit').prop('disabled', false).html('<i class="bi bi-save"></i> Save Refund');
                    
                    Swal.fire({
                        text: errorMsg,
                        icon: "error",
                    });
                }
            });
        });
    });
</script>
@endpush
