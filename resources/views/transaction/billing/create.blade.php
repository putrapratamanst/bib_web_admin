<x-layouts.app>
    <div class="container">
        @if(flash()->message)
            <div class="row">
                <div class="col">
                    <div class="alert alert-{{ flash()->class ?? "success" }}" role="alert">
                        {{ flash()->message }}
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>Create New Contract</span>
                        </div>
                    </div>
                    <form method="POST" id="formAddBilling" autocomplete="off">
                        <input type="hidden" name="contract_id" value="{{ $contract->id }}" />
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="contract_number" class="form-label">Contract Number</label>
                                <input type="text" class="form-control" id="contract_number" value="{{ $contract->number }}" readonly />      
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="number" class="form-label">Number</label>
                                        <input type="text" class="form-control" id="number" value="" placeholder="[Auto by System]" readonly />
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">Billing Date</label>
                                        <input type="text" class="form-control datepicker" id="date" name="date" placeholder="dd-mm-yyyy" readonly required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="text" class="form-control datepicker" id="due_date" name="due_date" value="" placeholder="dd-mm-yyyy" readonly required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Amount</label>
                                        <input type="text" class="form-control rp2 text-end" id="amount" name="amount" required />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" id="btnSubmit" class="btn btn-primary">Save</button>
                            <a href="{{ $urlBack }}" class="btn btn-outline-secondary">{{ $textBack }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(function() {
                $("#formAddBilling").submit(function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('api.transaction.billing.store') }}",
                        method: "POST",
                        data: $(this).serialize(),
                        beforeSend: function() {
                            $("#btnSubmit").attr("disabled", true);
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: response.message,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then(function() {
                                window.location.href = '{{ $urlBack }}';
                            });
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON.message) {
                                Toast.fire({
                                    icon: "error",
                                    title: xhr.responseJSON.message
                                });
                            }
                        },
                        complete: function() {
                            $("#btnSubmit").attr("disabled", false);
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layouts.app>