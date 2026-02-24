@extends('layouts.app')

@section('title', 'Detail Placing')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Detail Placing</span>
            @php
                $badgeClass = match($contract->approval_status) {
                    'approved' => 'bg-success',
                    'rejected' => 'bg-danger',
                    default => 'bg-warning'
                };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ ucfirst($contract->approval_status) }}</span>
        </div>
        <form autocomplete="off" method="POST" id="formCreate">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_status" class="form-label">Placing Status<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->contract_status }}" class="form-control" name="contract_status" id="contract_status" />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_type_id" class="form-label">Placing Type<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->contractType->name }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contact_id" class="form-label">Contact<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->contact->display_name }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label">Billing Address</label>
                            <input readonly type="text" value="{{ $contract->billingAddress ? $contract->billingAddress->name . ($contract->billingAddress->address ? ' - ' . $contract->billingAddress->address : '') : '-' }}" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">Number<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->number }}" class="form-control" name="number" id="number" />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="cover_note_number" class="form-label">Nomor Cover Note<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->cover_note_number ?? '-' }}" class="form-control" name="cover_note_number" id="cover_note_number" />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="policy_number" class="form-label">Policy Number<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->policy_number ?? '-' }}" class="form-control" name="policy_number" id="policy_number" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="insured_name" class="form-label">Insured Name</label>
                            <input readonly type="text" value="{{ $contract->billingAddress ? $contract->billingAddress->name : '-' }}" class="form-control" style="background-color: #e9ecef;">
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="correspondence_address" class="form-label">Correspondence Address</label>
                            <input readonly type="text" value="{{ $contract->billingAddress ? $contract->billingAddress->address : '-' }}" class="form-control" style="background-color: #e9ecef;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_start" class="form-label">Period Start<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->period_start_formatted }}" class="form-control" name="period_start" id="period_start" />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_end" class="form-label">Period End<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->period_end_formatted }}" class="form-control" name="period_end" id="period_end" />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="period_duration" class="form-label">Period Duration</label>
                            <input readonly type="text" value="{{ $contract->period_end ? $contract->period_start->diffInDays($contract->period_end) . ' days' : '0' }}" class="form-control" style="background-color: #e9ecef;">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="created_at" class="form-label">Created Date<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->created_at->format('d-m-Y') }}" class="form-control" name="created_at" id="created_at" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="currency_code" class="form-label">Currency<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->currency->code }}" class="form-control" name="currency_code" id="currency_code" />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="exchange_rate" class="form-label">Exchange Rate<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$contract->currency->code}}</span>
                                <input readonly type="text" value="{{ $contract->exchange_rate_formatted }}" class="form-control" name="exchange_rate" id="exchange_rate" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="coverage_amount" class="form-label">Total Sum Insured (TSI)<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$contract->currency->code}}</span>
                                <input readonly type="text" value="{{ $contract->coverage_amount_formatted }}" class="form-control" name="coverage_amount" id="coverage_amount" />
                            </div>
                        </div>
                    </div>
                                        <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="gross_premium" class="form-label">Gross Premium<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$contract->currency->code}}</span>
                                <input readonly type="text" value="{{ $contract->gross_premium_formatted }}" class="form-control" name="gross_premium" id="gross_premium" />
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                <div class="col-lg-3">
                    <div class="mb-3">
                        <label for="discount" class="form-label">Discount<sup class="text-danger">*</sup></label>
                        <div class="input-group">
                            <input type="text" name="discount" id="discount" class="form-control autonumeric" value="{{ $contract->discount_formatted }}" />
                            <span class="input-group-text" style="font-size: 14px;">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="mb-3">
                        <label for="discount_amount" class="form-label">Discount Amount<sup class="text-danger">*</sup></label>
                        <div class="input-group">
                            <span class="input-group-text curr-code" style="font-size: 14px;">{{$contract->currency->code}}</span>
                            <input type="text" id="discount_amount" class="form-control autonumeric" value="{{ $contract->discount_amount_formatted }}" readonly />
                        </div>
                    </div>
                </div>

                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Net Premium<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$contract->currency->code}}</span>
                                <input readonly type="text" value="{{ $contract->amount_formatted }}" class="form-control" name="amount" id="amount" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="policy_fee" class="form-label">Policy Fee</label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$contract->currency->code}}</span>
                                <input readonly type="text" value="{{ $contract->policy_fee ? number_format($contract->policy_fee, 2, ',', '.') : '-' }}" class="form-control" name="policy_fee" id="policy_fee" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="stamp_fee" class="form-label">Stamp Fee<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">{{$contract->currency->code}}</span>
                                <input readonly type="text" value="{{ $contract->stamp_fee_formatted }}" class="form-control" name="stamp_fee" id="stamp_fee" />
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-8 col-lg-6">
                        <div class="mb-3">
                            <label for="memo" class="form-label">Memo</label>
                            <textarea readonly name="memo" id="memo" class="form-control" rows="3">{{ $contract->memo }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="installment_count" class="form-label">Installment Count</label>
                        <div class="mb-3">
                            <input readonly type="text" value="{{ $contract->installment_count }}" class="form-control" name="installment_count" id="installment_count" />
                            {{-- <select name="installment_count" id="installment_count" class="form-select">
                                @for($i = 0; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $contract->installment_count == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                            </select> --}}
                        </div>
                    </div>
                </div>

                @if($contract->approval_status === 'approved')
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <strong>Approved by:</strong> {{ $contract->approvedBy->name ?? 'N/A' }}<br>
                            <strong>Approved at:</strong> {{ $contract->approved_at ? $contract->approved_at->format('d M Y H:i') : 'N/A' }}
                        </div>
                    </div>
                </div>
                @endif

                @if($contract->approval_status === 'rejected' && $contract->rejection_reason)
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-danger">
                            <strong>Rejected by:</strong> {{ $contract->approvedBy->name ?? 'N/A' }}<br>
                            <strong>Rejected at:</strong> {{ $contract->approved_at ? $contract->approved_at->format('d M Y H:i') : 'N/A' }}<br>
                            <strong>Reason:</strong> {{ $contract->rejection_reason }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Endorsements Section -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="mb-3">Endorsement / Placing Reference</h6>
                        @if($contract->endorsements->count() > 0)
                        <table class="table table-sm table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="40%">Placing Reference</th>
                                    <th width="30%">Endorsement No</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contract->endorsements as $index => $endorsement)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($endorsement->contractReference)
                                            {{ $endorsement->contractReference->number }} - {{ $endorsement->contractReference->contact->display_name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $endorsement->endorsement_number ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No endorsements found
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6 class="mb-3">Documents</h6>
                        <div id="documentsContainer">
                            <div class="alert alert-info">
                                <i class="bi bi-hourglass"></i> Loading documents...
                            </div>
                        </div>
                        
                        @if(auth()->user()->role === 'admin' && in_array($contract->approval_status, ['pending', 'rejected']))
                        <div class="mt-3 mb-3">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                <i class="bi bi-cloud-upload"></i> Add Documents
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <table id="tableDetails" class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="30%" rowspan="2">Insurance</th>
                            <th rowspan="2">Description</th>
                            <th colspan="3">%</th>
                        </tr>
                        <tr>
                            <th width="15%">Share</th>
                            <th width="15%">Brokerage Fee</th>
                            <th width="15%">Eng Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contract->details as $detail)
                        <tr>
                            <td>{{ $detail->insurance->display_name }}</td>
                            <td>{{ $detail->description }}</td>
                            <td>{{ $detail->percentage_formatted }}</td>
                            <td>{{ $detail->brokerage_fee_formatted }}</td>
                            <td>{{ $detail->eng_fee_formatted }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('transaction.contracts.index') }}" class="btn btn-outline-secondary">Back</a>
                
                @auth
                    @if(auth()->user()->role === 'admin' && in_array($contract->approval_status, ['pending', 'rejected']))
                    <div>
                        <a href="{{ route('transaction.contracts.edit', $contract->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    </div>
                    @endif

                    @if(auth()->user()->role === 'approver' && $contract->approval_status === 'pending')
                    <div>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                        <button type="button" class="btn btn-success" id="btnApprove">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                    </div>
                    @endif
                @endauth
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formReject">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason<sup class="text-danger">*</sup></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnRejectConfirm">Reject Contract</button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formUploadDocument">
                    <div class="mb-3">
                        <label for="documentFiles" class="form-label">Select Documents<sup class="text-danger">*</sup></label>
                        <input type="file" class="form-control" id="documentFiles" name="documents" multiple accept=".pdf,.xlsx,.xls,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png" required>
                        <small class="text-muted">Max file size: 10MB per file. Allowed formats: PDF, XLS, XLSX, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG</small>
                    </div>
                    <div id="uploadFilePreview"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnUploadDocument">Upload</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Load documents
        loadDocuments();

        // Document file preview
        $("#documentFiles").on("change", function() {
            let files = this.files;
            let preview = $("#uploadFilePreview");
            preview.empty();

            if (files.length > 0) {
                preview.append('<div class="mt-3"><strong>Selected files:</strong></div>');
                preview.append('<ul class="list-group mt-2" id="fileList"></ul>');
                
                let fileList = $("#fileList");
                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    let fileSize = (file.size / (1024 * 1024)).toFixed(2);
                    
                    if (fileSize > 10) {
                        fileList.append('<li class="list-group-item text-danger">' + file.name + ' (' + fileSize + 'MB) - <strong>Exceeds 10MB limit</strong></li>');
                    } else {
                        fileList.append('<li class="list-group-item">' + file.name + ' (' + fileSize + 'MB)</li>');
                    }
                }
            }
        });

        // Upload document
        $("#btnUploadDocument").on("click", function() {
            let files = document.getElementById('documentFiles').files;

            if (files.length === 0) {
                alert('Please select at least one file');
                return;
            }

            let contractId = '{{ $contract->id }}';
            console.log('Contract ID:', contractId);
            
            if (!contractId) {
                alert('Contract ID not found');
                return;
            }

            let formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('documents[]', files[i]);
            }

            $.ajax({
                url: '/api/contract/' + contractId + '/documents',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $("#btnUploadDocument").attr("disabled", true).text('Uploading...');
                },
                success: function(response) {
                    $("#btnUploadDocument").attr("disabled", false).text('Upload');
                    $('#uploadDocumentModal').modal('hide');
                    $('#documentFiles').val('');
                    $('#uploadFilePreview').empty();
                    
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then(() => {
                        loadDocuments();
                    });
                },
                error: function(xhr) {
                    $("#btnUploadDocument").attr("disabled", false).text('Upload');
                    
                    let errorMessage = 'Failed to upload documents';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        text: errorMessage,
                        icon: "error",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                },
            });
        });

        // Delete document
        $(document).on('click', '.btnDeleteDocument', function() {
            let documentId = $(this).data('id');
            let documentName = $(this).data('name');

            if (confirm('Are you sure you want to delete: ' + documentName + '?')) {
                $.ajax({
                    url: '/api/contract/{{ $contract->id }}/documents/' + documentId,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        }).then(() => {
                            loadDocuments();
                        });
                    },
                    error: function(xhr) {
                        alert('Failed to delete document');
                    }
                });
            }
        });

        // Approve Contract
        $('#btnApprove').on('click', function() {
            if (confirm('Are you sure you want to approve this contract?')) {
                $.ajax({
                    url: '/api/contracts/{{ $contract->id }}/approve',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Failed to approve contract');
                    }
                });
            }
        });

        // Reject Contract
        $('#btnRejectConfirm').on('click', function() {
            const reason = $('#rejection_reason').val().trim();
            
            if (!reason) {
                alert('Please provide a rejection reason');
                return;
            }

            $.ajax({
                url: '/api/contracts/{{ $contract->id }}/reject',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    rejection_reason: reason
                },
                success: function(response) {
                    alert(response.message);
                    $('#rejectModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Failed to reject contract');
                }
            });
        });
    });

    function loadDocuments() {
        $.ajax({
            url: '/api/contract/{{ $contract->id }}/documents',
            method: 'GET',
            success: function(response) {
                let documents = response.data;
                let container = $('#documentsContainer');
                container.empty();

                if (documents.length === 0) {
                    container.html('<div class="alert alert-info">No documents uploaded yet</div>');
                } else {
                    let html = '<div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Filename</th><th>Size</th><th>Uploaded</th><th>Action</th></tr></thead><tbody>';
                    
                    documents.forEach(function(doc) {
                        html += '<tr>' +
                                '<td><i class="bi bi-file"></i> ' + doc.filename + '</td>' +
                                '<td>' + doc.file_size_formatted + '</td>' +
                                '<td>' + doc.uploaded_at + '</td>' +
                                '<td>' +
                                '<a href="/api/contract/{{ $contract->id }}/documents/' + doc.id + '/download" class="btn btn-sm btn-info" title="Download"><i class="bi bi-download"></i></a> ';
                        
                        @if(auth()->user()->role === 'admin' && in_array($contract->approval_status, ['pending', 'rejected']))
                        html += '<button class="btn btn-sm btn-danger btnDeleteDocument" data-id="' + doc.id + '" data-name="' + doc.filename + '" title="Delete"><i class="bi bi-trash"></i></button>';
                        @endif
                        
                        html += '</td>' +
                                '</tr>';
                    });
                    
                    html += '</tbody></table></div>';
                    container.html(html);
                }
            },
            error: function(xhr) {
                $('#documentsContainer').html('<div class="alert alert-danger">Failed to load documents</div>');
            }
        });
    }
</script>
@endpush
@endsection