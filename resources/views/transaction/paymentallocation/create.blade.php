@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add Debit Note Allocation
            <div class="float-end">
                <a href="{{ route('transaction.payment-allocations.show', $dataCashBank->id) }}" class="btn btn-secondary btn-sm">
                    Back
                </a>
            </div>
        </div>
        <form autocomplete="off" method="POST" id="formCreate">
            <div class="card-body">
                <h5>Cash Bank Amount: {{$dataCashBank->currency_code}}{{ number_format($dataCashBank->amount, 2, ',', '.') }}</h5>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Debit Note Number</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Allocation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cashBank as $detail)
                                @php
                                $existingAllocation = $detail->debitNote->paymentAllocations
                                ->where('cash_bank_id', $detail->cash_bank_id) // optional, kalau mau per cash bank
                                ->first();
                                $allocationValue = $existingAllocation->allocation ?? $detail->amount ?? 0;
                                @endphp <tr>
                                    <td>{{ $detail->debitNote->number ?? '-' }}</td>
                                    <td>{{ $detail->debitNote->date ?? '-' }}</td>
                                    <td>{{ $detail->debitNote->due_date ?? '-' }}</td>
                                    <td>
                                        @php
                                            $totalCreditNotes = $detail->debitNote->creditNotes->sum('amount') ?? 0;
                                            $remainingAmount = $detail->debitNote->amount - $totalCreditNotes;
                                        @endphp
                                        {{ $detail->debitNote->currency_code }}
                                        {{ number_format($remainingAmount, 2, ',', '.') }}
                                        @if($totalCreditNotes > 0)
                                            <br>
                                            <small class="text-muted">
                                                (Original: {{ number_format($detail->debitNote->amount, 2, ',', '.') }},
                                                Credit Notes: {{ number_format($totalCreditNotes, 2, ',', '.') }})
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="hidden" name="debit_note_id[]" value="{{ $detail->debitNote->id }}">
                                        <input type="hidden" name="cash_bank_id[]" value="{{ $detail->cash_bank_id }}">
                                        <input type="number" name="allocation[]" class="form-control" step="0.01"
                                            value="{{ old('allocation.' . $loop->index, $allocationValue) }}">
                                        <input type="hidden" name="status[]" class="form-control" value="draft">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No Debit Notes available</td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">Save</button>
                <a href="{{ route('transaction.payment-allocations.index', $dataCashBank->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

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

        $('#contact_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select contact --',
            ajax: {
                url: "{{ route('api.contacts.select2') }}",
                dataType: 'json',
                delay: 500,
                data: function(params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        })
                    };
                },
                minimumInputLength: 2,
            },
        });

        $('#chart_of_account_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select chart of account --',
            ajax: {
                url: "{{ route('api.chart-of-accounts.select2') }}?c=3",
                dataType: 'json',
                delay: 500,
                data: function(params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        })
                    };
                },
                minimumInputLength: 2,
            },
        });

        $('#type').on('change', function() {
            var type = $(this).val();
            var labelContact = $('#labelContact');
            var labelChartOfAccount = $('#labelChartOfAccount');

            if (type == 'receive') {
                labelContact.html('From<sup class="text-danger">*</sup>');
                labelChartOfAccount.html('Deposit To<sup class="text-danger">*</sup>');
            } else if (type == 'pay') {
                labelContact.html('To<sup class="text-danger">*</sup>');
                labelChartOfAccount.html('Pay From<sup class="text-danger">*</sup>');
            }
        });

        $("#formCreate").submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('api.payment-allocations.store') }}",
                method: "POST",
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
                            window.location.href = "{{ route('transaction.payment-allocations.show', $dataCashBank->id) }}";
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
    });
</script>
@endpush