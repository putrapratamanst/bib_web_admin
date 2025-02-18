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
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>Detail Contract</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="number" class="form-label">Number</label>
                                    <input type="text" class="form-control" id="number" value="{{ $contract->number }}" readonly />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="contract_type_id" class="form-label">Contract Type</label>
                                    <input type="text" class="form-control" id="contract_type_id" value="{{ $contract->contractType->name ?? "" }}" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="client" class="form-label">Client</label>
                                    <input type="text" class="form-control" id="client" value="{{ $contract->client->name }}" readonly />
                                </div>
                            </div>
                            <?php /*<div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="insurance" class="form-label">Insurance</label>
                                    <input type="text" class="form-control" id="insurance" value="{{ $contract->insurance->name }}" readonly />
                                </div>        
                            </div>*/ ?>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <label for="period_start" class="form-label">Period Start</label>
                                    <input type="text" class="form-control" id="period_start" value="{{ $contract->period_start }}" readonly />
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <label for="period_end" class="form-label">Period End</label>
                                    <input type="text" class="form-control" id="period_end" value="{{ $contract->period_end }}" readonly />
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <label for="count_of_item" class="form-label">Count Of Item</label>
                                    <input type="text" class="form-control" id="count_of_item" value="{{ $contract->count_of_item }}" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" readonly>{{ $contract->description }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="currency_id" class="form-label">Currency</label>
                                    <input type="text" class="form-control" id="currency_id" value="{{ $contract->currency->code }}" readonly>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="currency_rate" class="form-label">Rate</label>
                                    <input type="text" class="form-control text-end rp2" id="currency_rate" value="{{ $contract->currency_rate }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="discount" class="form-label">Discount</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-end rp2-discount" id="discount" readonly value="{{ $contract->discount }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="gross_amount" class="form-label">Gross Amount</label>
                                    <input type="text" class="form-control text-end rp2" id="gross_amount" readonly value="{{ $contract->gross_amount }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="discount_amount" class="form-label">Discount Amount</label>
                                    <input type="text" class="form-control text-end rp2 readonly" id="discount_amount" readonly value="{{ $contract->getDiscountAmountAttribute() }}">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="nett_amount" class="form-label">Nett Amount</label>
                                    <input type="text" class="form-control text-end rp2 readonly" id="nett_amount" readonly value="{{ $contract->getNettAmountAttribute() }}">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <a href="{{ route("transaction.contract.index") }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>List Billing</span>
                            <a href="{{ route("transaction.billing.create", ["contractId" => $contract->id]) }}" class="btn btn-primary">Create Billing</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Number</th>
                                    <th width="15%">Date</th>
                                    <th width="15%">Due Date</th>
                                    <th width="15%">Amount</th>
                                    <th width="15%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contract->billings as $b)
                                    <tr>
                                        <td>{{ $b->number }}</td>
                                        <td>{{ $b->date }}</td>
                                        <td>{{ $b->due_date }}</td>
                                        <td class="text-end">{{ $b->amount }}</td>
                                        <td>{{ $b->status }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No billing found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>