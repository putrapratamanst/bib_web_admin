@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Balance Sheet
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
            <form id="formReport" method="POST" autocomplete="off">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="mb-0">
                            <button class="btn-primary btn">Generate Report</button>
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

            $.ajax({
                url: '{{ route("api.report.balance-sheet.index") }}',
                method: 'GET',
                success: function(response) {
                    $("#tblReportData").html(response.data);
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        });
    });
</script>
@endpush