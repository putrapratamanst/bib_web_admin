@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add New Advance Payment
        </div>
        <form autocomplete="off" id="formAdvance">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cash_bank_id" class="form-label">Cash Bank Number<sup class="text-danger">*</sup></label>
                            <select name="cash_bank_id" id="cash_bank_id" class="form-control" required>
                                <option value=""></option>
                            </select>
                            <small class="text-muted">Only showing Cash Bank with available allocation</small>
                        </div>
                    </div>
                </div>
                
                <!-- Cash Bank Details (will be shown after selection) -->
                <div id="cashBankDetails" style="display: none;">
                    <div class="row">
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
                                <label class="form-label">Total Amount</label>
                                <input type="text" id="cash_bank_amount" class="form-control" readonly />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Available for Allocation</label>
                                <input type="text" id="available_amount" class="form-control text-success fw-bold" readonly />
                                <input type="hidden" id="available_amount_raw" />
                            </div>
                        </div>
                    </div>
                    
                    <hr />
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Advance amount akan otomatis menggunakan seluruh available allocation: <strong><span id="advance_amount_display">Rp 0</span></strong>
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
                    <i class="bi bi-save"></i> Save Advance
                </button>
                <a href="{{ route('transaction.advances.index') }}" class="btn btn-secondary">
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
        // Initialize Select2 for Cash Bank
        $('#cash_bank_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Select Cash Bank --',
            ajax: {
                url: "{{ route('api.advances.cash-bank.select2') }}",
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

        // On Cash Bank selection change
        $('#cash_bank_id').on('change', function() {
            const cashBankId = $(this).val();
            
            if (!cashBankId) {
                $('#cashBankDetails').hide();
                return;
            }

            // Get cash bank details
            $.ajax({
                url: `/api/advances/cash-bank/${cashBankId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Fill in the details
                        $('#contact_name').val(data.contact_name);
                        $('#cash_bank_date').val(data.display_date);
                        $('#cash_bank_amount').val('Rp ' + data.display_amount);
                        $('#available_amount').val('Rp ' + data.available_for_allocation_formatted);
                        $('#available_amount_raw').val(data.available_for_allocation);
                        $('#advance_amount_display').text('Rp ' + data.available_for_allocation_formatted);
                        
                        // Show the details section
                        $('#cashBankDetails').show();
                    }
                },
                error: function(xhr) {
                    alert('Failed to get cash bank details');
                    console.error(xhr);
                }
            });
        });

        // Form submit
        $('#formAdvance').on('submit', function(e) {
            e.preventDefault();
            
            const cashBankId = $('#cash_bank_id').val();
            const allocation = parseFloat($('#available_amount_raw').val());
            const description = $('#description').val();
            
            // Validation
            if (!cashBankId) {
                alert('Please select Cash Bank');
                return;
            }
            
            if (!allocation || allocation <= 0) {
                alert('Available amount must be greater than 0');
                return;
            }
            
            // Disable submit button
            $('#btnSubmit').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Saving...');
            
            // Submit via AJAX
            $.ajax({
                url: "{{ route('api.advances.store') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    cash_bank_id: cashBankId,
                    allocation: allocation,
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
                                window.location.href = "{{ route('transaction.advances.index') }}";
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to save advance payment';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    $('#btnSubmit').prop('disabled', false).html('<i class="bi bi-save"></i> Save Advance');
                    
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
