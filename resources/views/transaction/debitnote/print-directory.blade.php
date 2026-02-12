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
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .label {
            width: 180px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .separator {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .value {
            flex: 1;
            padding-left: 10px;
        }

        .table-container {
            border: 1px solid #000;
            margin: 20px 0;
        }

        .table-header {
            background: #f0f0f0;
            display: flex;
            padding: 8px;
            border-bottom: 1px solid #000;
        }

        .table-header div {
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }

        .table-header div:first-child {
            flex: 1;
            border-right: 1px solid #000;
        }

        .table-body {
            display: flex;
            min-height: 180px;
        }

        .notes-column {
            flex: 1;
            padding: 12px;
            border-right: 1px solid #000;
            font-size: 11px;
            line-height: 1.4;
        }

        .details-column {
            width: 200px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .premium-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .premium-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            padding: 4px 0;
        }

        .premium-label {
            flex: 1;
            text-align: left;
        }

        .premium-value {
            flex: none;
            text-align: right;
            font-weight: bold;
            margin-left: 10px;
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
                /* border-top: 1px solid #ddd; */
                page-break-inside: avoid;
                display: flex;
                justify-content: space-between;
                padding-bottom: 75px;
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
                <div class="value">{{ \Carbon\Carbon::parse($debitNote->date)->format('d F Y') }}</div>
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
                <div>{{ $debitNote->contract->contact->name ?? $debitNote->contact->name ?? '' }}</div>
                <div style="margin-top: 5px;">
                    {{ $debitNote->contract->billingAddress ? $debitNote->contract->billingAddress->address : ($debitNote->contract->contact ? $debitNote->contract->contact->address : '') }}
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
                        IDR {{ number_format($debitNote->amount, 2, ',', '.') }} - {{ \Carbon\Carbon::parse($debitNote->due_date)->format('d M Y') }}
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
                            <span class="premium-value">{{ $debitNote->currency_code ?? 'IDR' }}<br>{{ number_format($grossPremium, 2, ',', '.') }}</span>
                        </div>

                        <div class="premium-row">
                            <span class="premium-label">Biaya Polis<br><i>Policy Cost</i></span>
                            <span class="premium-value">{{ $debitNote->currency_code ?? 'IDR' }}<br>{{ number_format(1, 2, ',', '.') }}</span>
                        </div>

                        <div class="premium-row">
                            <span class="premium-label">Biaya Materai<br><i>Stamp Duty</i></span>
                            <span class="premium-value">{{ $debitNote->currency_code ?? 'IDR' }}<br>{{ number_format($stampFee, 2, ',', '.') }}</span>
                        </div>

                        @if($discount > 0)
                        <div class="premium-row">
                            <span class="premium-label">Diskon ({{ number_format($discount, 0) }}%)<br><i>Discount</i></span>
                            <span class="premium-value">{{ $debitNote->currency_code ?? 'IDR' }}<br>{{ number_format($discountAmount, 2, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="premium-row" style="border-top: 1px solid #000; margin-top: 10px; padding-top: 10px;">
                            <span class="premium-label"><strong>Premi Neto<br><i>Nett Premium</i></strong></span>
                            <span class="premium-value"><strong>{{ $debitNote->currency_code ?? 'IDR' }}<br>{{ number_format($debitNote->amount, 2, ',', '.') }}</strong></span>
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