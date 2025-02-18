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
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <span>Create Cash In / Cash Out</span>
                </div>
            </div>
            <form method="POST" autocomplete="off" id="formCashTransaction">
                <div class="card-body">
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
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="number" class="form-label">Number</label>
                                <input type="text" class="form-control" placeholder="[Auto]" readonly />
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="text" class="form-control datepicker" placeholder="dd-mm-yyyy" name="date" id="date" readonly required />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select select2" id="type" name="type" required data-placeholder="-- choose type --">
                                    <option value=""></option>
                                    <option value="in">Cash In</option>
                                    <option value="out">Cash Out</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="bank_id" class="form-label">Bank</label>
                                <select class="form-select select2" id="bank_id" name="bank_id" required data-placeholder="-- choose bank --">
                                    <option value=""></option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="bank_account_name" class="form-label">Account Name</label>
                                <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" required />
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="bank_account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" required />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="currency_id" class="form-label">Currency</label>
                                <select class="form-select select2" id="currency_id" name="currency_id" required data-placeholder="-- choose currency --">
                                    <option value=""></option>
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="currency_rate" class="form-label">Currency Rate</label>
                                <input type="text" class="form-control rp2 text-end" id="currency_rate" name="currency_rate" required value="1" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="text" class="form-control rp2 text-end" id="amount" name="amount" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" id="btnSubmit" class="btn btn-primary">Save</button>
                    <a href="{{ route("transaction.cash-transaction.index") }}" class="btn btn-outline-secondary">Back to List</a>
                </div>
            </form>
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
                $("#formCashTransaction").submit(function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('api.transaction.cash-transaction.store') }}",
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
                            else {
                                Toast.fire({
                                    icon: "error",
                                    title: "An error occurred. Please try again later."
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