@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Detail Contact
        </div>
        <form autocomplete="off" method="POST" id="formShow">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="contact_group_id" class="form-label">Contact Group<sup class="text-danger">*</sup></label>
                            <select class="form-select" id="contact_group_id" name="contact_group_id">
                                <option value="">-- select contact group --</option>
                                @foreach($contactGroups as $cg)
                                <option value="{{ $cg->id }}" {{ $contact->contact_group_id == $cg->id ? 'selected' : '' }}>
                                    {{ $cg->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="display_name" class="form-label">Display Name<sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" id="display_name" name="display_name" value="{{ $contact->display_name }}" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $contact->name }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type<sup class="text-danger">*</sup></label>
                            <select class="form-select" required id="type" name="type">
                                <option value="client" {{ $contact->type == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="agent" {{ $contact->type == 'agent' ? 'selected' : '' }}>Agent</option>
                                <option value="insurance" {{ $contact->type == 'insurance' ? 'selected' : '' }}>Insurance</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="{{ $contact->email }}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $contact->phone }}" />
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">Save Contact</button>
                <a href="{{ route('master.contacts.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Billing Addresses -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Billing Addresses</h5>
            <button type="button" class="btn btn-sm btn-primary" id="btnAddBilling">
                <i class="fas fa-plus"></i> Add New
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tableBillingAddresses">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th width="10%">Primary</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="billingAddressList">
                        <tr>
                            <td colspan="7" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Billing Address -->
<div class="modal fade" id="modalBillingAddress" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBillingAddressTitle">Add Billing Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formBillingAddress">
                <input type="hidden" id="billing_id" name="billing_id">
                <input type="hidden" id="contact_id_billing" name="contact_id" value="{{ $contact->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="billing_name_modal" class="form-label">Name<sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="billing_name_modal" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="billing_address_modal" class="form-label">Address<sup class="text-danger">*</sup></label>
                        <textarea class="form-control" id="billing_address_modal" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="billing_email_modal" class="form-label">Email</label>
                        <input type="email" class="form-control" id="billing_email_modal" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="billing_phone_modal" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="billing_phone_modal" name="phone">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1">
                        <label class="form-check-label" for="is_primary">
                            Set as Primary Address
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveBilling">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        $('#type').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select type --',
        });

        $('#contact_group_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select contact group --',
        });

        $("#formShow").submit(function(e) {
            e.preventDefault();

            var urlUpdate = "{{ route('api.contacts.update', $contact->id) }}";

            $.ajax({
                url: urlUpdate,
                method: "PUT",
                data: $(this).serialize(),
                beforeSend: function() {
                    $("#btnSubmit").attr("disabled", true);
                },
                success: function(response) {
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var firstItem = Object.keys(errors)[0];
                    var firstErrorMessage = errors[firstItem][0];
                    $("#btnSubmit").attr("disabled", false);

                    Swal.fire({
                        text: firstErrorMessage,
                        icon: "error",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                },
            });
        });

        // Load billing addresses
        loadBillingAddresses();

        // Add billing address button
        $('#btnAddBilling').click(function() {
            $('#modalBillingAddressTitle').text('Add Billing Address');
            $('#formBillingAddress')[0].reset();
            $('#billing_id').val('');
            $('#modalBillingAddress').modal('show');
        });

        // Save billing address
        $('#formBillingAddress').submit(function(e) {
            e.preventDefault();
            
            var billingId = $('#billing_id').val();
            var url = billingId ? 
                "{{ url('api/billing-address') }}/" + billingId : 
                "{{ route('api.billing-addresses.store') }}";
            var method = billingId ? 'PUT' : 'POST';

            var formData = {
                contact_id: $('#contact_id_billing').val(),
                name: $('#billing_name_modal').val(),
                address: $('#billing_address_modal').val(),
                email: $('#billing_email_modal').val(),
                phone: $('#billing_phone_modal').val(),
                is_primary: $('#is_primary').is(':checked') ? 1 : 0
            };

            $.ajax({
                url: url,
                method: method,
                data: formData,
                beforeSend: function() {
                    $('#btnSaveBilling').attr('disabled', true);
                },
                success: function(response) {
                    $('#modalBillingAddress').modal('hide');
                    Swal.fire({
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadBillingAddresses();
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var firstItem = Object.keys(errors)[0];
                    var firstErrorMessage = errors[firstItem][0];
                    
                    Swal.fire({
                        text: firstErrorMessage,
                        icon: 'error'
                    });
                },
                complete: function() {
                    $('#btnSaveBilling').attr('disabled', false);
                }
            });
        });
    });

    function loadBillingAddresses() {
        $.ajax({
            url: "{{ url('api/contact/' . $contact->id . '/billing-address') }}",
            method: 'GET',
            success: function(response) {
                var html = '';
                if (response.data.length > 0) {
                    response.data.forEach(function(item, index) {
                        var primaryBadge = item.is_primary ? 
                            '<span class="badge bg-success">Primary</span>' : 
                            '<button class="btn btn-sm btn-outline-secondary" onclick="setPrimary(\'' + item.id + '\')">Set Primary</button>';
                        
                        html += '<tr>';
                        html += '<td>' + (index + 1) + '</td>';
                        html += '<td>' + item.name + '</td>';
                        html += '<td>' + item.address + '</td>';
                        html += '<td>' + (item.email || '-') + '</td>';
                        html += '<td>' + (item.phone || '-') + '</td>';
                        html += '<td>' + primaryBadge + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-warning me-1" onclick="editBilling(\'' + item.id + '\')"><i class="fas fa-edit"></i></button>';
                        html += '<button class="btn btn-sm btn-danger" onclick="deleteBilling(\'' + item.id + '\')"><i class="fas fa-trash"></i></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center">No billing addresses found</td></tr>';
                }
                $('#billingAddressList').html(html);
            }
        });
    }

    function editBilling(id) {
        $.ajax({
            url: "{{ url('api/contact/' . $contact->id . '/billing-address') }}",
            method: 'GET',
            success: function(response) {
                var billing = response.data.find(b => b.id == id);
                if (billing) {
                    $('#modalBillingAddressTitle').text('Edit Billing Address');
                    $('#billing_id').val(billing.id);
                    $('#billing_name_modal').val(billing.name);
                    $('#billing_address_modal').val(billing.address);
                    $('#billing_email_modal').val(billing.email);
                    $('#billing_phone_modal').val(billing.phone);
                    $('#is_primary').prop('checked', billing.is_primary);
                    $('#modalBillingAddress').modal('show');
                }
            }
        });
    }

    function deleteBilling(id) {
        Swal.fire({
            text: 'Are you sure you want to delete this billing address?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('api/billing-address') }}/" + id,
                    method: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadBillingAddresses();
                    }
                });
            }
        });
    }

    function setPrimary(id) {
        $.ajax({
            url: "{{ url('api/billing-address') }}/" + id + "/set-primary",
            method: 'POST',
            success: function(response) {
                Swal.fire({
                    text: response.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                loadBillingAddresses();
            }
        });
    }
</script>
@endpush