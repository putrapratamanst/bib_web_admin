<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debit Note</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
            margin: 0;
        }

        .container {
            max-width: 210mm;
            /* A4 width */
            width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 15mm;
            border: 2px solid #000;
            box-sizing: border-box;
        }

        .logo {
            text-align: right;
            margin-bottom: 10px;
        }

        .logo img {
            width: 80px;
            height: auto;
            display: block;
            margin-left: auto;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .header-section {
            margin-bottom: 10px;
        }

        .row {
            display: flex;
            margin-bottom: 6px;
            padding: 4px 0;
        }

        .label {
            width: 180px;
            font-weight: bold;
        }

        .label i {
            font-weight: normal !important;
            font-style: italic;
        }

        .header-section .row:last-child {
            border-bottom: 1px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .separator {
            margin: 0 10px;
        }

        .value {
            flex: 1;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .table-container {
            border: 2px solid #000;
            margin: 15px 0 5px 0;
        }

        .table-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 2px solid #000;
        }

        .table-header div {
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        .table-header div:first-child {
            border-right: 2px solid #000;
        }

        .table-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            height: 300px;
        }

        .notes-column {
            border-right: 2px solid #000;
            padding: 12px;
            font-size: 12px;
        }

        .details-column {
            display: flex;
            flex-direction: column;
        }

        .premium-section {
            flex: 0 0 auto;
            padding: 12px;
            border-bottom: 2px solid #000;
            font-size: 12px;
        }

        .premium-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .premium-label {
            font-weight: bold;
        }

        .premium-value {
            text-align: right;
        }

        .signature-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 12px;
            text-align: center;
            font-size: 12px;
        }

        .footer {
            margin-top: 5px;
            /* padding: 10mm 15mm; */
            font-size: 9px;
            color: #666;
            line-height: 1.2;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
        }

        .footer-left {
            flex: none;
            width: auto;
        }

        .footer-right {
            text-align: right;
            flex: none;
            width: auto;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .container {
                border: 2px solid #000;
                max-width: 100%;
                width: 210mm;
                margin: 0;
                padding: 15mm;
                box-shadow: none;
            }

            .footer {
                margin-top: 5px;
                /* padding: 10mm 15mm; */
                /* border-top: 1px solid #ddd; */
                page-break-inside: avoid;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                width: 100%;
            }

            /* Hide browser print margins */
            @page {
                size: A4;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('logo.png') }}" alt="Brilliant Insurance Brokers Logo">
        </div>

        <!-- Title -->
        <div class="title">DEBIT NOTE</div>

        <!-- Header Information -->
        <div class="header-section">
            <div class="row">
                <div class="label">No</div>
                <div class="separator">:</div>
                <div class="value">{{ $debitNote->number }}</div>
            </div>

            <div class="row">
                <div class="label">Tanggal<br><i>Date</i></div>
                <div class="separator">:</div>
                <div class="value">{{ $debitNote->date_formatted }}</div>
            </div>

            <div class="row">
                <div class="label">Ref</div>
                <div class="separator">:</div>
                <div class="value">{{ $debitNote->contract ? $debitNote->contract->number : '-' }}</div>
            </div>
        </div>

        <!-- Policy Information -->
        <div class="row">
            <div class="label">Nomor Polis<br><i>Policy Number</i></div>
            <div class="separator">:</div>
            <div class="value">{{ $debitNote->contract->policy_number ?? '-' }}</div>
        </div>

        <div class="row">
            <div class="label">Nama & Alamat Tertanggung<br><i>Name & Address of Insured</i></div>
            <div class="separator">:</div>
            <div class="value">
                <div>{{ $debitNote->contract->contact->name ?? $debitNote->contact->name }}</div>
                <div style="margin-top: 5px;">
                    {{ $debitNote->billingAddress ? $debitNote->billingAddress->address : ($debitNote->contract->contact ? $debitNote->contract->contact->address : '') }}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="label">Periode Asuransi<br><i>Period of Insurance</i></div>
            <div class="separator">:</div>
            <div class="value">{{ $debitNote->contract ? $debitNote->contract->period : '-' }}</div>
        </div>

        <div class="row">
            <div class="label">Jenis Asuransi<br><i>Type of Insurance</i></div>
            <div class="separator">:</div>
            <div class="value">{{ $debitNote->contract->contractType->name ?? 'General Insurance' }}</div>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <div class="table-header">
                <div>Catatan / <i>Notes</i></div>
                <div>Perincian / <i>Details</i></div>
            </div>

            <div class="table-body">
                <div class="notes-column">
                    <div style="margin-bottom: 12px;">
                        <strong>Risk Location: </strong>{{ $debitNote->contract->contact->address ?? '-' }}
                    </div>
                    <div style="margin-bottom: 12px;">
                        <strong>Subject: </strong>{{ $debitNote->contract->covered_item ?? 'Property All Risk' }}
                    </div>
                    <div style="margin-bottom: 12px;">
                        Adalah benar bahwa 7 hari setelah jatuh tempo dari tanggal dikeluarkannya premi maupun jatuh tempo pembayaran Premi dianggap dan dianggap sebagai Debit Note & Polis terlambat.
                    </div>
                    <div style="margin-top: 30px;margin-bottom: 15px;">
                        <strong>Tanggal Pembayaran<br><i>Date of Payment</i></strong><br>
                        IDR {{ $debitNote->amount_formatted }} - {{ $debitNote->due_date ? $debitNote->due_date->format('d M Y') : '' }}
                    </div>
                    <div style="margin-bottom: 15px;">
                        <strong>PT. Brilliant Insurance Brokers</strong><br>
                        Bank Mandiri KCP Botanica Garden a/c No. 070.0006.524123 (IDR)<br>
                        BNI 46 Cab. Senayan a/c No. 025.9060.691 (IDR)<br>
                        Bank Mandiri KCP Botanica Garden a/c No. 070.0006.524131 (USD)<br>
                        BCA KCP Puri Botanica a/c No. 6260.5866.88 (IDR)
                    </div>

                </div>

                <div class="details-column">
                    <div class="premium-section">
                        @php
                        $contract = $debitNote->contract;
                        $grossPremium = $contract ? $contract->gross_premium : $debitNote->amount;
                        $discount = $contract ? $contract->discount : 0;
                        $stampFee = $contract ? $contract->stamp_fee : 0;
                        $discountAmount = $grossPremium * ($discount / 100);
                        $netPremium = $grossPremium - $discountAmount + $stampFee;
                        @endphp

                        <div class="premium-row">
                            <span class="premium-label">Premi<br><i>Premium</i></span>
                            <span class="premium-value">{{ $debitNote->currency_code }}<br>{{ number_format($grossPremium, 2, ',', '.') }}</span>
                        </div>

                        <div class="premium-row">
                            <span class="premium-label">Biaya Polis<br><i>Policy Cost</i></span>
                            <span class="premium-value">{{ $debitNote->currency_code }}<br>{{ number_format(1, 2, ',', '.') }}</span>
                        </div>

                        <div class="premium-row">
                            <span class="premium-label">Biaya Materai<br><i>Stamp Duty</i></span>
                            <span class="premium-value">{{ $debitNote->currency_code }}<br>{{ number_format($stampFee, 2, ',', '.') }}</span>
                        </div>

                        @if($discount > 0)
                        <div class="premium-row">
                            <span class="premium-label">Diskon ({{ number_format($discount, 0) }}%)<br><i>Discount</i></span>
                            <span class="premium-value">{{ $debitNote->currency_code }}<br>{{ number_format($discountAmount, 2, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="premium-row" style="border-top: 1px solid #000; margin-top: 10px; padding-top: 10px;">
                            <span class="premium-label"><strong>Premi Neto<br><i>Nett Premium</i></strong></span>
                            <span class="premium-value"><strong>{{ $debitNote->currency_code }}<br>{{ $debitNote->amount_formatted }}</strong></span>
                        </div>
                    </div>

                    <div class="signature-section">
                        <div>Authorized</div>
                        <div style="margin-top: 40px;">Signature</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">
                <strong>PT. Brilliant Insurance Brokers</strong><br>
                Rukan Botanica Junction Blok J.12 no. 60,<br>
                Jl. Raya Joglo - Jakarta Barat 11640<br>
                SIUP NO : KEP.230/KM.10/2012 & Member of APPARINDO no. 189-2012
            </div>
            <div class="footer-right">
                T: 021 – 5890 8403<br>
                021 – 2254 2676<br>
                021 – 2568 0394
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>