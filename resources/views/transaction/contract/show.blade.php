@extends('layouts.app')

@section('title', 'Detail Contract')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Detail Contract
        </div>        
        <form autocomplete="off" method="POST" id="formCreate">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_status" class="form-label">Contract Status<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->contract_status }}" class="form-control" name="contract_status" id="contract_status" />
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contract_type_id" class="form-label">Contract Type<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->contractType->name }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="contact_id" class="form-label">Contact<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->contact->display_name }}" class="form-control">
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
                            <label for="policy_number" class="form-label">Policy Number<sup class="text-danger">*</sup></label>
                            <input readonly type="text" value="{{ $contract->policy_number }}" class="form-control" name="policy_number" id="policy_number" />
                        </div>
                    </div>
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
                                <span class="input-group-text" style="font-size: 14px;">Rp</span>
                                <input readonly type="text" value="{{ $contract->exchange_rate_formatted }}" class="form-control" name="exchange_rate" id="exchange_rate" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="coverage_amount" class="form-label">Coverage Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">Rp</span>
                                <input readonly type="text" value="{{ $contract->coverage_amount_formatted }}" class="form-control" name="coverage_amount" id="coverage_amount" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="stamp_fee" class="form-label">Stamp Fee<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">Rp</span>
                                <input readonly type="text" value="{{ $contract->stamp_fee_formatted }}" class="form-control" name="stamp_fee" id="stamp_fee" />
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="gross_premium" class="form-label">Gross Premium<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">Rp</span>
                                <input readonly type="text" value="{{ $contract->gross_premium_formatted }}" class="form-control" name="gross_premium" id="gross_premium" />
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount<sup class="text-danger">*</sup></label>
                            <div class="input-group">
                                <span class="input-group-text" style="font-size: 14px;">Rp</span>
                                <input readonly type="text" value="{{ $contract->amount_formatted }}" class="form-control" name="amount" id="amount" />
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
            <div class="card-footer">
                <a href="{{ route('transaction.contracts.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection