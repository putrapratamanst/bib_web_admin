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
            margin: 0 auto;
            background: white;
            padding: 15mm;
            box-sizing: border-box;
            min-height: calc(297mm - 40px);
        }

        .header {
            margin-bottom: 20px;
        }

        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .ref-text {
            font-size: 11px;
            font-style: italic;
            color: #333;
        }

        .logo {
            text-align: right;
        }

        .logo img {
            width: 180px;
            height: auto;
            display: block;
            margin-left: auto;
        }

        .title-section {
            text-align: center;
            margin: 30px 0 20px 0;
        }

        .title-line {
            border-top: 2px solid #000;
            margin-bottom: 15px;
        }

        .document-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 10px;
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

        .label i {
            font-weight: normal;
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
            border-bottom: 1px solid #000;
        }

        .table-header div {
            font-weight: bold;
            text-align: center;
            font-size: 12px;
            padding: 8px 12px;
        }

        .table-header div:first-child {
            flex: 1;
            border-right: 1px solid #000;
        }

        .table-header div:last-child {
            width: 200px;
            flex-shrink: 0;
        }

        .table-body {
            display: flex;
            min-height: 500px;
        }

        .notes-column {
            flex: 1;
            padding: 12px;
            border-right: 1px solid #000;
            font-size: 11px;
            line-height: 1.4;
            display: flex;
            flex-direction: column;
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
            align-items: flex-start;
            font-size: 10px;
            padding: 4px 0;
        }

        .premium-label {
            flex: 1;
            text-align: left;
        }

        .premium-currency {
            width: 30px;
            text-align: center;
            flex-shrink: 0;
        }

        .premium-value {
            width: 75px;
            text-align: right;
            font-weight: bold;
            flex-shrink: 0;
        }

        .signature-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            padding: 12px 12px 4px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            border-top: 1px solid #000;
        }

        .signature-text {
            display: inline-block;
            border-top: 1px solid #000;
            padding-top: 3px;
        }

        .content-wrap {
        }

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
                margin: 0;
                padding: 10mm;
                box-shadow: none;
                min-height: 297mm;
            }

            .content-wrap {
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
        <div class="content-wrap">
        <div class="header">
            <div class="top-row">
                <div class="ref-text">
                    Ref: {{ $debitNote->contract ? $debitNote->contract->number : '-' }}
                </div>
                <div class="logo">
                    <img src="{{ asset('logo.png') }}" alt="Brilliant Insurance Brokers Logo">
                </div>
            </div>
            
            <div class="title-section">
                <div class="document-title">DEBIT NOTE</div>
            </div>
        </div>
                <div class="title-line"></div>

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
                <div class="value">{{ \Carbon\Carbon::parse($debitNote->date)->format('d-M-Y') }}</div>
            </div>

            <div class="row">
                <div class="label">Ref</div>
                <div class="separator">:</div>
                <div class="value">{{ $debitNote->contract->policy_number ?? '-' }}</div>
            </div>
        </div>
                <div class="title-line"></div>

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

        <div class="row">
            <div class="label"><strong>Total Nilai Pertanggungan</strong><br><i>Total Sum Insured</i></div>
            <div class="separator">:</div>
            <div class="value">{{ $debitNote->contract?->currency_code ?? 'IDR' }}
                {{ number_format($debitNote->contract?->coverage_amount ?? 0, 2, ',', '.') }}
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <div class="table-header">
                <div>Catatan / <i>Notes</i></div>
                <div>Perincian / <i>Details</i></div>
            </div>

            <div class="table-body">
                <div class="notes-column">
                    <div>
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
                            @if($debitNote->debitNoteBillings->isNotEmpty())
                                @foreach($debitNote->debitNoteBillings as $index => $billing)
                                    @php
                                        // Display amount: remove policy_fee and stamp_fee from first installment (for existing data)
                                        $displayAmount = $billing->amount;
                                        if ($index === 0 && $debitNote->contract) {
                                            $policyFee = floatval($debitNote->contract->policy_fee ?? 0);
                                            $stampFee = floatval($debitNote->contract->stamp_fee ?? 0);
                                            $displayAmount = $billing->amount - $policyFee - $stampFee;
                                        }
                                    @endphp
                                    Installment {{ $index + 1 }}: {{ $debitNote->currency_code ?? 'IDR' }} {{ number_format($displayAmount, 2, ',', '.') }} - {{ \Carbon\Carbon::parse($billing->due_date)->format('d/m/Y') }}<br>
                                @endforeach
                            @else
                                {{ $debitNote->currency_code ?? 'IDR' }} {{ number_format($debitNote->amount, 2, ',', '.') }} - {{ \Carbon\Carbon::parse($debitNote->due_date)->format('d/m/Y') }}
                            @endif
                        </div>
                    </div>
                    
                    <div style="margin-top: auto;">
                        <strong>PT. Brilliant Insurance Brokers</strong><br>
                        Bank Mandiri KCP Botanica Garden a/c No. 070.0006.524123 (IDR)<br>
                        BNI 46 Cab. Senayan a/c No. 025.9960.691 (IDR)<br>
                        Bank Mandiri KCP Botanica Garden a/c No. 070.0006.524131 (USD)<br>
                        BCA KCP Puri Botanica a/c No. 6250.8855.88 (IDR)
                    </div>

                </div>

                <div class="details-column">
                    <div class="premium-section">
                        @php
                        $contract = $debitNote->contract;
                        $grossPremium = $contract ? $contract->gross_premium : $debitNote->amount;
                        $discount = $contract ? $contract->discount : 0;
                        $stampFee = $contract ? $contract->stamp_fee : 0;
                        $policyCost = $contract ? ($contract->policy_fee ?? 0) : 0;
                        $discountAmount = $contract ? $contract->discount_amount : 0;
                        $netPremium = $grossPremium - $discountAmount + $stampFee;
                        $currency = $debitNote->currency_code ?? 'IDR';
                        @endphp

                        <div class="premium-row">
                            <span class="premium-label">Premi<br><i>Premium</i></span>
                            <span class="premium-currency">{{ $currency }}</span>
                            <span class="premium-value">{{ number_format($grossPremium, 2, ',', '.') }}</span>
                        </div>

                        <div class="premium-row">
                            <span class="premium-label">Biaya Polis<br><i>Policy Cost</i></span>
                            <span class="premium-currency">{{ $currency }}</span>
                            <span class="premium-value">{{ number_format($policyCost, 2, ',', '.') }}</span>
                        </div>

                        <div class="premium-row">
                            <span class="premium-label">Biaya Materai<br><i>Stamp Duty</i></span>
                            <span class="premium-currency">{{ $currency }}</span>
                            <span class="premium-value">{{ number_format($stampFee, 2, ',', '.') }}</span>
                        </div>

                        @if($discount > 0)
                        <div class="premium-row">
                            <span class="premium-label">Diskon ({{ number_format($discount, 0) }}%)<br><i>Discount</i></span>
                            <span class="premium-currency">{{ $currency }}</span>
                            <span class="premium-value">{{ number_format($discountAmount, 2, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="premium-row" style="border-top: 1px solid #000; margin-top: 10px; padding-top: 10px;">
                            <span class="premium-label"><strong>Premi Neto<br><i>Nett Premium</i></strong></span>
                            <span class="premium-currency"><strong>{{ $currency }}</strong></span>
                            <span class="premium-value"><strong>{{ number_format($debitNote->amount, 2, ',', '.') }}</strong></span>
                        </div>
                    </div>

                    <div class="signature-section">
                        <span class="signature-text">Authorized Signatures</span>
                    </div>
                </div>
            </div>
        </div>
        </div><!-- end content-wrap -->

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