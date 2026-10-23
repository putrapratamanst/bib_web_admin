<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->number }}</title>
    <style>
        @page { size: A4; margin: 0; }
        * { box-sizing: border-box; }
        body { font-family: Cambria, "Times New Roman", serif; font-size: 12pt; line-height: 1.45; color: #111; margin: 0; background: #e5e7eb; }
        .page { position: relative; width: 210mm; min-height: 297mm; margin: 8mm auto; padding: 18mm 24mm 38mm; background: #fff; }
        .logo { text-align: right; }
        .logo img { height: 18mm; }
        .logo-text { display: inline-block; text-align: right; line-height: 1; color: #2a5db0; }
        .logo-text b { display: block; font-family: Arial, sans-serif; font-size: 30pt; letter-spacing: -1px; }
        .logo-text small { font-family: Arial, sans-serif; font-size: 8pt; }
        .date { text-align: right; margin: 6mm 0 12mm; }
        table.info { border-collapse: collapse; margin-left: 4mm; }
        table.info td { padding: 0; vertical-align: top; }
        table.info td:first-child { width: 24mm; }
        .body p { margin: 0 0 5mm; }
        .just { text-align: justify; }
        table.bank { border-collapse: collapse; margin: 8mm 0 8mm; }
        table.bank td { padding: 0; vertical-align: top; }
        table.bank td:first-child { width: 36mm; }
        .signature { margin-top: 8mm; }
        .signature .space { height: 24mm; }
        .footer { position: absolute; left: 24mm; right: 24mm; bottom: 14mm; display: flex; justify-content: space-between; font-family: Arial, sans-serif; font-size: 7.5pt; line-height: 1.3; }
        .footer .phone { text-align: right; }
        .lampiran { padding-bottom: 18mm; }
        .lampiran h3 { text-align: center; margin: 0 0 6mm; }
        .lampiran table.items { border-collapse: collapse; width: 100%; font-size: 10pt; margin-top: 5mm; }
        .lampiran table.items th, .lampiran table.items td { border: 1px solid #333; padding: 4px 6px; }
        .lampiran table.items th { background: #eee; }
        .no-print { position: fixed; top: 8px; right: 12px; }
        @media print {
            body { background: #fff; }
            .page { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Print</button>

    <div class="page">
        <div class="logo">
            @if(file_exists(public_path('img/favicon/luminous-logo.png')))
                <img src="{{ asset('img/favicon/luminous-logo.png') }}" alt="Luminous Risk Management">
            @else
                <span class="logo-text"><b>Luminous</b><small>Risk Management</small></span>
            @endif
        </div>
        <div class="date">{{ $invoice->city }}, {{ $invoice->date->locale('id')->translatedFormat('d F Y') }}</div>

        <table class="info">
            <tr><td>Nomor</td><td>: {{ $invoice->number }}</td></tr>
            <tr><td>Perihal</td><td>: {{ $invoice->subject }}</td></tr>
        </table>

        <div class="body" style="margin-top: 10mm;">
            <p>Kepada Yth,<br>{{ $invoice->recipient }}</p>

            <p class="just">Kami ingin menginformasikan untuk tagihan {{ $invoice->description }}, sejumlah
                <b>IDR {{ number_format($invoice->amount, 2, ',', '.') }}</b>.
                Mohon untuk segera dibayarkan tagihan tersebut ke :</p>

            <table class="bank">
                <tr><td>Nomor Rekening</td><td>: {{ $invoice->bank_account }}</td></tr>
                <tr><td>Bank</td><td>: {{ $invoice->bank_name }}</td></tr>
                <tr><td>Atas Nama</td><td>: {{ $invoice->account_name }}</td></tr>
            </table>

            <p>Demikian surat ini kami sampaikan, Terimakasih.</p>

            <div class="signature">
                Hormat kami,
                <div class="space"></div>
                <b><u>{{ $invoice->signatory_name }}</u></b><br>
                {{ $invoice->signatory_title }}<br>
                PT. Luminous Risk Management
            </div>
        </div>

        <div class="footer">
            <div>
                <b>PT. Luminous Risk Management</b><br>
                Rukan Botanic Junction Blok i 10 no. 60,<br>
                Jl. Raya Joglo - Jakarta Barat 11640
            </div>
            <div class="phone">T: 021 – 5890 8403<br>021 – 2254 2676</div>
        </div>
    </div>

</body>
</html>
