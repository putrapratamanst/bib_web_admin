@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Report Piutang
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
            <form id="formReport" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-lg-2 col-md-3">
                        <div class="mb-3">
                            <label for="as_of_date" class="form-label">As Of Date</label>
                            <input type="text" name="as_of_date" id="as_of_date" class="form-control datepicker">
                        </div>
                    </div>
                    <!-- give a separator -->
                    <div class="col-lg-1 col-md-1 d-flex align-items-center justify-content-center">
                        <span class="fw-bold">|</span>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <div class="mb-3">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="text" name="from_date" id="from_date" class="form-control datepicker">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <div class="mb-3">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="text" name="to_date" id="to_date" class="form-control datepicker">
                        </div>
                    </div>
                    <!-- give a separator -->
                    <div class="col-lg-1 col-md-1 d-flex align-items-center justify-content-center">
                        <span class="fw-bold">|</span>
                    </div>

                    <div class="col-lg-3 col-md-4">
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Clients / Insurances</label>
                            <select id="insurance_id" name="insurance_id" class="form-select" data-placeholder="-- select insurance --"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-0">
                                <button class="btn-primary btn">Generate Report</button>
                                <button type="button" id="btnExport" class="btn-success btn" disabled>Export to Excel</button>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
        <div class="card-body">
            <div id="tblReportData"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#formReport').submit(function(e) {
            e.preventDefault();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();

            $.ajax({
                url: '{{ route("api.report.piutang.index") }}',
                method: 'GET',
                data: {
                    from_date: from_date,
                    to_date: to_date,
                },
                success: function(response) {
                    $("#tblReportData").html(response);
                    $("#btnExport").attr('disabled', false);
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        });

        // $("#btnExport").on('click', function() {
        //     var from_date = $('#from_date').val();
        //     var to_date = $('#to_date').val();
        //     var url = '{{ route("api.report.piutang.index") }}';

        //     url = url + "?format=excel&from_date=" + from_date + "&to_date=" + to_date;

        //     window.open(url, '_blank');
        // });
$("#btnExport").on('click', function() {
    var as_of_date = $('#as_of_date').val();
    var from_date  = $('#from_date').val();
    var to_date    = $('#to_date').val();
    var client_id  = $('#insurance_id').val();

    var url = '{{ route("api.report.piutang.index") }}';
    url += "?format=excel";
    if (as_of_date) url += "&as_of_date=" + as_of_date;
    if (from_date)  url += "&from_date=" + from_date;
    if (to_date)    url += "&to_date=" + to_date;
    if (client_id)  url += "&client_id=" + client_id;

    window.open(url, '_blank');
});

        $('#insurance_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- select insurance --',
            ajax: {
                url: "{{ route('api.contacts.select2') }}?type=insurance",
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
    });
</script>
@endpush