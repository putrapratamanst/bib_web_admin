@extends('layouts.app')

@section('title', 'Renewal Notice')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Renewal Notice
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom: 1px solid #cbd5e1;">
            <form id="renewalNoticeFilterForm" autocomplete="off">
                <div class="row">
                    <div class="col-lg-2 col-md-3">
                        <div class="mb-3">
                            <label for="filter_month" class="form-label">Month</label>
                            <select id="filter_month" name="filter_month" class="form-select" required>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ now()->month === $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <div class="mb-3">
                            <label for="filter_year" class="form-label">Year</label>
                            <select id="filter_year" name="filter_year" class="form-select" required>
                                @for ($y = now()->year - 2; $y <= now()->year + 5; $y++)
                                    <option value="{{ $y }}" {{ now()->year === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="mb-3">
                            <label for="contract_type_id" class="form-label">Type Insurance</label>
                            <select id="contract_type_id" name="contract_type_id" class="form-select">
                                <option value="">Semua</option>
                                @foreach($contractTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->code }} - {{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="btnGenerate">
                                    <i class="fas fa-search me-1"></i>Generate
                                </button>
                                <button type="button" class="btn btn-danger" id="btnDownloadPdf" disabled>
                                    <i class="fas fa-file-pdf me-1"></i>Download PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-new table-hover table-striped table-bordered" id="renewalNoticeTable">
                    <thead class="table-header">
                        <tr>
                            <th>Period End</th>
                            <th>Type Insurance</th>
                            <th>Nomor Polis</th>
                            <th>Nomor Placing</th>
                            <th>Nama Asuransi</th>
                            <th>Nama Tertanggung</th>
                            <th>Alamat Tertanggung</th>
                            <th>No DN</th>
                            <th>No CN</th>
                            <th>Mata Uang</th>
                            <th class="text-end">Total Sum Insured</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="text-center text-muted">No data loaded. Click Generate.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
    let renewalNoticeRows = [];
    let renewalNoticeFilters = {
        filtered_month: '',
        filtered_year: '',
        tipe_asuransi: 'Semua',
    };

    $(document).ready(function() {
        $('#renewalNoticeFilterForm').on('submit', function(e) {
            e.preventDefault();
            fetchRenewalNoticeData();
        });

        $('#btnDownloadPdf').on('click', function() {
            downloadRenewalNoticePdf();
        });

        fetchRenewalNoticeData();
    });

    function fetchRenewalNoticeData() {
        const month = $('#filter_month').val();
        const year = $('#filter_year').val();
        const contractTypeId = $('#contract_type_id').val();

        $('#btnGenerate').prop('disabled', true);

        $.ajax({
            url: "{{ route('api.reports.renewal-notice') }}",
            method: 'GET',
            data: {
                month: month,
                year: year,
                contract_type_id: contractTypeId,
            },
            success: function(response) {
                renewalNoticeRows = response.data || [];
                renewalNoticeFilters = response.filters || {
                    filtered_month: String(month).padStart(2, '0'),
                    filtered_year: String(year),
                    tipe_asuransi: 'Semua',
                };

                renderRenewalNoticeTable(renewalNoticeRows);
                $('#btnDownloadPdf').prop('disabled', renewalNoticeRows.length === 0);
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to load Renewal Notice data', 'error');
                renewalNoticeRows = [];
                renderRenewalNoticeTable([]);
                $('#btnDownloadPdf').prop('disabled', true);
            },
            complete: function() {
                $('#btnGenerate').prop('disabled', false);
            }
        });
    }

    function renderRenewalNoticeTable(rows) {
        const tbody = $('#renewalNoticeTable tbody');
        tbody.empty();

        if (!rows.length) {
            tbody.append('<tr><td colspan="11" class="text-center text-muted">No data found for selected filters.</td></tr>');
            return;
        }

        rows.forEach(function(row) {
            tbody.append(`
                <tr>
                    <td>${escapeHtml(row.period_end_formatted ?? '-')}</td>
                    <td>${escapeHtml(row.tipe_asuransi ?? '-')}</td>
                    <td>${escapeHtml(row.nomor_polis ?? '-')}</td>
                    <td>${escapeHtml(row.nomor_placing ?? '-')}</td>
                    <td>${escapeHtml(row.nama_asuransi ?? '-')}</td>
                    <td>${escapeHtml(row.nama_tertanggung ?? '-')}</td>
                    <td>${escapeHtml(row.alamat_tertanggung ?? '-')}</td>
                    <td>${escapeHtml(row.no_dn ?? '-')}</td>
                    <td>${escapeHtml(row.no_cn ?? '-')}</td>
                    <td>${escapeHtml(row.mata_uang ?? '-')}</td>
                    <td class="text-end">${escapeHtml(row.total_sum_insured_formatted ?? '0,00')}</td>
                </tr>
            `);
        });
    }

    function downloadRenewalNoticePdf() {
        if (!renewalNoticeRows.length) {
            Swal.fire('Info', 'No data to export', 'info');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

        doc.setFontSize(14);
        doc.text('Renewal Notice Report', 14, 12);

        doc.setFontSize(10);
        doc.text(`filtered_month: ${renewalNoticeFilters.filtered_month}`, 14, 19);
        doc.text(`filtered_year: ${renewalNoticeFilters.filtered_year}`, 60, 19);
        doc.text(`tipe_asuransi: ${renewalNoticeFilters.tipe_asuransi || 'Semua'}`, 100, 19);

        const head = [[
            'nomor_polis',
            'nomor_placing',
            'nama_asuransi',
            'nama_tertanggung',
            'alamat_tertanggung',
            'no_dn',
            'no_cn',
            'Mata Uang',
            'total_sum_insured'
        ]];

        const body = renewalNoticeRows.map(function(row) {
            return [
                row.nomor_polis || '-',
                row.nomor_placing || '-',
                row.nama_asuransi || '-',
                row.nama_tertanggung || '-',
                row.alamat_tertanggung || '-',
                row.no_dn || '-',
                row.no_cn || '-',
                row.mata_uang || '-',
                row.total_sum_insured_formatted || '0,00',
            ];
        });

        doc.autoTable({
            head: head,
            body: body,
            startY: 24,
            styles: { fontSize: 8, cellPadding: 1.5 },
            headStyles: { fillColor: [52, 58, 64] },
            margin: { left: 8, right: 8 },
        });

        const fileMonth = renewalNoticeFilters.filtered_month || String($('#filter_month').val()).padStart(2, '0');
        const fileYear = renewalNoticeFilters.filtered_year || $('#filter_year').val();
        doc.save(`renewal_notice_${fileYear}_${fileMonth}.pdf`);
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
@endpush
