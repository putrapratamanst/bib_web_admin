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
                    <form method="POST" id="formAddContract" autocomplete="off">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="contract_type_id" class="form-label">Contract Type</label>
                                <select class="form-select select2" id="contract_type_id" name="contract_type_id" data-placeholder="-- choose contract type --" required>
                                    <option value=""></option>
                                    @foreach($contractTypies as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="client_id" class="form-label">Client</label>
                                        <select class="form-select select2" id="client_id" name="client_id" data-placeholder="-- choose client --" required>
                                            <option value=""></option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <?php /*<div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="insurance_id" class="form-label">Insurance</label>
                                        <select class="form-select select2" id="insurance_id" name="insurance_id" data-placeholder="-- choose insurance --">
                                            <option value=""></option>
                                            @foreach($insurances as $insurance)
                                                <option value="{{ $insurance->id }}">{{ $insurance->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>*/ ?>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="period_start" class="form-label">Period Start</label>
                                        <input type="text" class="form-control datepicker" id="period_start" name="period_start" readonly />
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="period_end" class="form-label">Period End</label>
                                        <input type="text" class="form-control datepicker" id="period_end" name="period_end" readonly />
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="count_of_item" class="form-label">Count Of Item</label>
                                        <input type="number" class="form-control" id="count_of_item" name="count_of_item" />
                                    </div>
                                </div>
                            </div>

                            

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="currency_id" class="form-label">Currency</label>
                                        <select class="form-select select2" id="currency_id" name="currency_id" data-placeholder="-- choose currency --">
                                            <option value=""></option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="currency_rate" class="form-label">Rate</label>
                                        <input type="text" class="form-control text-end rp2" id="currency_rate" name="currency_rate" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="discount" class="form-label">Discount</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-end rp2-discount" id="discount" name="discount" value="0">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="gross_amount" class="form-label">Gross Amount</label>
                                        <input type="text" class="form-control text-end rp2" id="gross_amount" name="gross_amount">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_amount" class="form-label">Discount Amount</label>
                                        <input type="text" class="form-control text-end rp2 readonly" id="discount_amount" readonly value="0">
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="nett_amount" class="form-label">Nett Amount</label>
                                        <input type="text" class="form-control text-end rp2 readonly" id="nett_amount" readonly value="0">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" id="btnSubmit" class="btn btn-primary">Save</button>
                            <a href="{{ route('transaction.contract.index') }}" class="btn btn-outline-secondary">Back to List</a>
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

            function changeGrossAmount() {
                var grossAmount = $("#gross_amount").autoNumeric('get');
                var discount = $("#discount").autoNumeric('get');
                var discountAmount = (grossAmount * discount) / 100;
                var nettAmount = grossAmount - discountAmount;

                $("#discount_amount").autoNumeric('set', discountAmount);
                $("#nett_amount").autoNumeric('set', nettAmount);
            }

            $(function() {
                $("#discount").on("change", function() {
                    changeGrossAmount();
                });

                $("#gross_amount").on("change", function() {
                    changeGrossAmount();
                });

                $("#formAddContract").submit(function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('api.transaction.contract.store') }}",
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
                                window.location.href = response.redirect;
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
            })
        </script>
    @endpush
</x-layouts.app>

