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
                    <span>Detail Transaction</span>
                </div>
            </div>
            <form method="POST" autocomplete="off">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="client_id" class="form-label">Client</label>
                                <input type="text" class="form-control" id="client_id" name="client_id" readonly value="{{ $ct->client->name }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="number" class="form-label">Number</label>
                                <input type="text" class="form-control" id="number" name="number" readonly value="{{ $ct->number }}" />
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="text" class="form-control" id="date" name="date" readonly value="{{ $ct->date_new }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <input type="text" class="form-control" id="type" name="type" readonly value="{{ $ct->type_new }}" />
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="bank_id" class="form-label">Bank</label>
                                <input type="text" class="form-control" id="bank_id" name="bank_id" readonly value="{{ $ct->bank->name }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="bank_account_name" class="form-label">Account Name</label>
                                <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" readonly value="{{ $ct->bank_account_name }}" />
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="bank_account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" readonly value="{{ $ct->bank_account_number }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="currency_id" class="form-label">Currency</label>
                                <input type="text" class="form-control" id="currency_id" name="currency_id" readonly value="{{ $ct->currency->code }}" />
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="currency_rate" class="form-label">Currency Rate</label>
                                <input type="text" class="form-control text-end" id="currency_rate" name="currency_rate" readonly value="{{ $ct->currency_rate }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="text" class="form-control text-end" id="amount" name="amount" readonly value="{{ $ct->amount }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{$ct->description}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route("transaction.cash-transaction.index") }}" class="btn btn-outline-secondary">Back to List</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>