@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add New Journal Entry
        </div>        
        <form autocomplete="off" method="POST" id="formCreate">
            <input type="hidden" name="status" value="posted" />
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="number" class="form-label">Number<sup class="text-danger">*</sup></label>
                            <input type="text" name="number" id="number" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="entry_date" class="form-label">Entry Date<sup class="text-danger">*</sup></label>
                            <input type="text" name="entry_date" id="entry_date" class="form-control datepicker" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="mb-3">
                            <label for="reference" class="form-label">Reference<sup class="text-danger">*</sup></label>
                            <input type="text" name="reference" id="reference" class="form-control" required />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-lg-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <table id="tableDetails" class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="30%">Account</th>
                            <th>Description</th>
                            <th width="15%">Debit</th>
                            <th width="15%">Credit</th>
                            <th width="10%">Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select id="chart_of_account_id_0" name="chart_of_account_id[]" class="form-select" data-placeholder="-- select account --"></select>
                            </td>
                            <td>
                                <input id="description_0" type="text" class="form-control" name="description[]">
                            </td>
                            <td>
                                <input id="debit_0" type="text" class="form-control text-end" name="debit[]" value="0">
                            </td>
                            <td>
                                <input id="credit_0" type="text" class="form-control text-end" name="credit[]" value="0">
                            </td>
                            <td class="text-center" style="vertical-align: middle;">
                                <button type="button" class="removeRow btn btn-outline-danger btn-sm">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddRow">Add Row</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <div class="card-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary">Save</button>
                <a href="{{ route('transaction.journal-entries.index') }}" class="btn btn-secondary">Cancel</a>
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

    var rowNumber = 1;

    $(document).ready(function() {
        $("#formCreate").submit(function(e) {
            e.preventDefault();

            var details = [];
            $('#tableDetails tbody tr').each(function() {
                var chartOfAccountId = $(this).find('select[name="chart_of_account_id[]"]').val();
                var description = $(this).find('input[name="description[]"]').val();
                var debit = $(this).find('input[name="debit[]"]').val();
                var credit = $(this).find('input[name="credit[]"]').val();

                details.push({
                    chart_of_account_id: chartOfAccountId,
                    description: description,
                    debit: debit,
                    credit: credit,
                });
            });

            var formData = {
                number: $("#number").val(),
                entry_date: $("#entry_date").val(),
                reference: $("#reference").val(),
                description: $("#description").val(),
                status: "posted",
                details: details,
            };

            $.ajax({
                url: "{{ route('api.journal-entries.store') }}",
                method: "POST",
                data: formData,
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
                            window.location.href = "{{ route('transaction.journal-entries.index') }}";
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

        $('#btnAddRow').click(function () {
            $('#tableDetails tbody').append(`
                <tr>
                    <td>
                        <select id="chart_of_account_id_` + rowNumber + `" name="chart_of_account_id[]" class="form-select" data-placeholder="-- select account --"></select>
                    </td>
                    <td>
                        <input id="description_` + rowNumber + `" type="text" class="form-control" name="description[]">
                    </td>
                    <td>
                        <input id="debit_` + rowNumber + `" type="text" class="form-control text-end" name="debit[]" value="0">
                    </td>
                    <td>
                        <input id="credit_` + rowNumber + `" type="text" class="form-control text-end" name="credit[]" value="0">
                    </td>
                    <td class="text-center" style="vertical-align: middle;">
                        <button type="button" class="removeRow btn btn-outline-danger btn-sm">Remove</button>
                    </td>
                </tr>
            `);

            assignAccount(rowNumber.toString());
            rowNumber++;
        });

        assignAccount("0");
    });

    $(document).on('click', '.removeRow', function () {
        if ($('#tableDetails tbody tr').length === 1) {
            return;
        }
        $(this).closest('tr').remove();
    });

    function assignAccount(number) {
        $('#chart_of_account_id_' + number).select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select account --',
            ajax: {
                url: "{{ route('api.chart-of-accounts.select2') }}",
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data) {
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
    }
</script>
@endpush