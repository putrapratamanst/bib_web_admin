@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Report Console
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
            <form id="formReport" method="POST" autocomplete="off">
                <div class="row">
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
                url: '{{ route("api.report.console.index") }}',
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

        $("#btnExport").on('click', function() {
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var url = '{{ route("api.report.console.index") }}';

            url = url + "?format=excel&from_date=" + from_date + "&to_date=" + to_date;

            window.open(url, '_blank');
        });
    });
</script>
@endpush