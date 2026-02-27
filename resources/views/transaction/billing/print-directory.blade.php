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
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 15mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Top bar: ref kiri, logo kanan */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .ref-top {
            font-size: 9px;
            font-style: italic;
            color: #333;
        }

        .logo img {
            width: 200px;
            height: auto;
            display: block;
        }

        /* Title */
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1.5px solid #000;
            margin-bottom: 10px;
        }

        .header-divider {
            border: none;
            border-top: 1.5px solid #000;
            margin: 8px 0;
        }

        /* Header rows */
        .header-section {
            margin-bottom: 2px;
        }

        .row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .label {
            width: 175px;
            font-weight: bold;
            flex-shrink: 0;
            line-height: 1.4;
        }

        .label i {
            font-weight: normal;
            font-style: italic;
        }

        .separator {
            width: 15px;
            flex-shrink: 0;
        }

        .value {
            flex: 1;
            line-height: 1.4;
        }

        /* Table */
        .table-container {
            border: 1px solid #000;
            margin: 12px 0 5px 0;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .table-header {
            display: grid;
            grid-template-columns: 1fr 220px;
            border-bottom: 1px solid #000;
        }

        .table-header div {
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }

        .table-header div:first-child {
            border-right: 1px solid #000;
        }

        .table-body {
            display: grid;
            grid-template-columns: 1fr 220px;
            flex: 1;
        }

        /* Notes column */
        .notes-column {
            border-right: 1px solid #000;
            padding: 10px;
            font-size: 10px;
            line-height: 1.5;
            display: flex;
            flex-direction: column;
        }

        .notes-column > div {
            margin-bottom: 8px;
        }

        .notes-bottom {
            margin-top: auto;
        }

        /* Details column */
        .details-column {
            display: flex;
            flex-direction: column;
        }

        .premium-section {
            flex: 0 0 auto;
            padding: 10px;
            font-size: 10px;
        }

        .premium-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 7px;
        }

        .premium-label {
            flex: 1;
            font-weight: bold;
            line-height: 1.3;
        }

        .premium-label i {
            font-weight: normal;
            font-style: italic;
            display: block;
        }

        .premium-currency {
            width: 30px;
            text-align: left;
            font-size: 10px;
            padding-top: 1px;
        }

        .premium-value {
            width: 80px;
            text-align: right;
            font-size: 10px;
            padding-top: 1px;
        }

        .premium-row.total {
            border-top: 1px solid #000;
            padding-top: 6px;
            margin-top: 4px;
        }

        .premium-row.total .premium-label,
        .premium-row.total .premium-value,
        .premium-row.total .premium-currency {
            font-weight: bold;
        }

        /* Signature section */
        .signature-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 12px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            border-top: 1px solid #000;
        }

        /* Footer */
        .footer {
            margin-top: 8px;
            border-top: 1px solid #000;
            padding-top: 6px;
            font-size: 8.5px;
            color: #000;
            line-height: 1.4;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .footer-left {
            flex: 1;
        }

        .footer-right {
            text-align: right;
            white-space: nowrap;
            margin-left: 20px;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .container {
                max-width: 100%;
                width: 210mm;
                min-height: 297mm;
                height: 297mm;
                margin: 0;
                padding: 15mm;
                box-shadow: none;
                page-break-after: always;
            }

            .content-wrapper {
                height: 100%;
            }

            .table-container {
                flex: 1;
            }

            @page {
                size: A4;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="content-wrapper">

            <!-- Top bar: ref kiri, logo kanan -->
            <div class="top-bar">
                <div class="ref-top">Ref: {{ $billing->debitNote->contract ? $billing->debitNote->contract->number : '-' }}</div>
                <div class="logo">
                    <img src="{{ asset('logo.png') }}" alt="Brilliant Insurance Brokers Logo">
                </div>
            </div>

            <!-- Title -->
            <div class="title">DEBIT NOTE</div>

            <!-- Divider bawah title -->
            <hr class="divider">

            <!-- Header: No, Tanggal, Ref -->
            <div class="header-section">
                <div class="row">
                    <div class="label"><strong>No</strong></div>
                    <div class="separator">:</div>
                    <div class="value">{{ $billing->billing_number }}</div>
                </div>
                <div class="row">
                    <div class="label"><strong>Tanggal</strong><br><i>Date</i></div>
                    <div class="separator">:</div>
                    <div class="value">{{ \Carbon\Carbon::parse($billing->date)->format('d-M-Y') }}</div>
                </div>
                <div class="row">
                    <div class="label"><strong>Ref</strong></div>
                    <div class="separator">:</div>
                    <div class="value">{{ $billing->debitNote->contract ? $billing->debitNote->contract->number : '-' }}</div>
                </div>
            </div>

            <!-- Divider setelah No/Tanggal/Ref -->
            <hr class="header-divider">

            <!-- Policy Information -->
            <div class="row" style="margin-top: 6px;">
                <div class="label"><strong>Nomor Polis</strong><br><i>Policy Number</i></div>
                <div class="separator">:</div>
                <div class="value">{{ $billing->debitNote->contract->policy_number ?? '-' }}</div>
            </div>

            <div class="row">
                <div class="label"><strong>Nama Tertanggung</strong><br><i>Name & Address of Insured</i></div>
                <div class="separator">:</div>
                <div class="value">
                    <div>{{ $billing->debitNote->contract->contact->name ?? $billing->debitNote->contact->name ?? '' }}</div>
                    <div style="margin-top: 3px;">
                        {{ $billing->debitNote->contract->billingAddress ? $billing->debitNote->contract->billingAddress->address : ($billing->debitNote->contract->contact ? $billing->debitNote->contract->contact->address : '') }}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="label"><strong>Periode Asuransi</strong><br><i>Period of Insurance</i></div>
                <div class="separator">:</div>
                <div class="value">{{ $billing->debitNote->contract ? $billing->debitNote->contract->period : '-' }}</div>
            </div>

            <div class="row">
                <div class="label"><strong>Jenis Asuransi</strong><br><i>Type of Insurance</i></div>
                <div class="separator">:</div>
                <div class="value">{{ $billing->debitNote->contract->contractType->name ?? 'Property All Risk' }}</div>
            </div>

            <!-- Table Section -->
            <div class="table-container">
                <div class="table-header">
                    <div><strong>Catatan / <em>Notes</em></strong></div>
                    <div><strong>Perincian / <em>Details</em></strong></div>
                </div>

                <div class="table-body">
                    <!-- Notes Column -->
                    <div class="notes-column">
                        <div>
                            <strong>Risk Location: </strong>{{ $billing->debitNote->contract->riskLocation ?? '-' }}
                        </div>

                        <div>
                            <strong>Stock: </strong>{{ $billing->debitNote->contract->stock ?? '-' }}<br>
                            <strong>Content: </strong>{{ $billing->debitNote->contract->content ?? '-' }}
                        </div>

                        <div><i>Jatuh tempo pembayaran premi adalah 7 hari setelah polis diterima.</i></div>
                        <div><i>Klaim dapat ditolak jika pembayaran premi melebihi jatuh tempo</i></div>
                        <div><i>Pembayaran Premi ditujukan atas atas nama ditujukan nomor Debit Note &amp; Polis tersebut</i></div>

                        <div class="notes-bottom">
                            <div style="margin-bottom: 10px;">
                                <strong>Tanggal Pembayaran<br><i>Date of Payment(s)</i></strong><br>
                                {{ \Carbon\Carbon::parse($billing->due_date)->format('d-M-Y') }} – {{ $billing->debitNote->currency_code ?? 'IDR' }} {{ number_format($billing->amount, 2, '.', ',') }}
                            </div>

                            <div>
                                <strong>PT. Brilliant Insurance Brokers</strong><br>
                                Bank Mandiri KCP Botanical Garden<br>
                                a/c No. 070.0006.524123 (IDR)<br>
                                Bank Mandiri KCP Botanical Garden<br>
                                a/c No. 070.0006.524131 (USD)<br>
                                BNI 46 Cab. Senayan<br>
                                a/c No. 025.9060.691 (IDR)<br>
                                BCA KCP Puri Botanical<br>
                                a/c No. 6260.5866.88 (IDR)
                            </div>
                        </div>
                    </div>

                    <!-- Details Column -->
                    <div class="details-column">
                        <div class="premium-section">
                            @php
                            $contract = $billing->debitNote->contract;
                            $grossPremium = $contract ? $contract->gross_premium : $billing->amount;
                            $discount = $contract ? $contract->discount : 0;
                            $stampFee = $contract ? $contract->stamp_fee : 0;
                            $policyCost = $contract ? ($contract->policy_cost ?? 0) : 0;
                            $discountAmount = $grossPremium * ($discount / 100);
                            $currency = $billing->debitNote->currency_code ?? 'IDR';
                            @endphp

                            <!-- Premi -->
                            <div class="premium-row">
                                <div class="premium-label">Premi<i>Premium</i></div>
                                <div class="premium-currency">{{ $currency }}</div>
                                <div class="premium-value">{{ number_format($grossPremium, 2, '.', ',') }}</div>
                            </div>

                            <!-- Biaya Polis -->
                            <div class="premium-row">
                                <div class="premium-label">Biaya Polis<i>Policy Cost</i></div>
                                <div class="premium-currency">{{ $currency }}</div>
                                <div class="premium-value">{{ number_format($policyCost, 2, '.', ',') }}</div>
                            </div>

                            <!-- Biaya Materai -->
                            <div class="premium-row">
                                <div class="premium-label">Biaya Materai<i>Stamp Duty</i></div>
                                <div class="premium-currency">{{ $currency }}</div>
                                <div class="premium-value">{{ number_format($stampFee, 2, '.', ',') }}</div>
                            </div>

                            <!-- Diskon -->
                            @if($discount > 0)
                            <div class="premium-row">
                                <div class="premium-label">Diskon<i>Discount</i></div>
                                <div class="premium-currency">{{ $currency }}</div>
                                <div class="premium-value">({{ number_format($discountAmount, 2, '.', ',') }})</div>
                            </div>
                            @endif

                            <!-- Premi Neto -->
                            <div class="premium-row total">
                                <div class="premium-label">Premi Neto<i>Nett Premium</i></div>
                                <div class="premium-currency">{{ $currency }}</div>
                                <div class="premium-value">{{ number_format($billing->amount, 2, '.', ',') }}</div>
                            </div>
                        </div>

                        <!-- Signature -->
                        <div class="signature-section">
                            Authorized Signatures
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">
                <strong>PT. Brilliant Insurance Brokers</strong><br>
                Rukan Botanic Junction Blok I 10 no. 60,<br>
                Jl. Raya Joglo - Jakarta Barat 11640<br>
                <em>SIUP No : KEP-230/KM.10/2012 &amp; Member of APPARINDO No. 189-2012</em>
            </div>
            <div class="footer-right">
                T: 021 – 5890 8403<br>
                021 – 2254 2676<br>
                021 – 2568 0394
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>