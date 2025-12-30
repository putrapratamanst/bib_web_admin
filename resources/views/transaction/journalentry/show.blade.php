@extends('layouts.app')

@section('title', 'Detail Journal Entry')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Detail Journal Entry
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 col-lg-3">
                    <div class="mb-3">
                        <label for="number" class="form-label">Number</label>
                        <input type="text" class="form-control" readonly name="number" id="number" value="{{ $journalEntry->number }}">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="mb-3">
                        <label for="entry_date" class="form-label">Entry Date</label>
                        <input type="text" class="form-control" readonly name="entry_date" id="entry_date" value="{{ $journalEntry->date_formatted }}">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="mb-3">
                        <label for="reference" class="form-label">Reference</label>
                        <input type="text" class="form-control" readonly name="reference" id="reference" value="{{ $journalEntry->reference }}">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control" readonly name="status" id="status" value="{{ ucfirst($journalEntry->status) }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-lg-6">
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" readonly>{{ $journalEntry->description }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <h5 class="mb-3">Journal Entry Details</h5>
                    <table class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="30%">Account</th>
                                <th>Description</th>
                                <th width="15%" class="text-end">Debit</th>
                                <th width="15%" class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($journalEntry->details as $detail)
                            <tr>
                                <td>{{ $detail->chartOfAccount->display_name }}</td>
                                <td>{{ $detail->description }}</td>
                                <td class="text-end">{{ $detail->debit_formatted }}</td>
                                <td class="text-end">{{ $detail->credit_formatted }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="2" class="text-end">Total:</td>
                                <td class="text-end">{{ $journalEntry->total_debit_formatted }}</td>
                                <td class="text-end">{{ $journalEntry->total_credit_formatted }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('transaction.journal-entries.index') }}" class="btn btn-secondary">Back</a>
            <a href="{{ route('transaction.journal-entries.print', $journalEntry->id) }}" target="_blank" class="btn btn-info">
                <i class="fas fa-print"></i> Print
            </a>
        </div>
    </div>
</div>
@endsection
