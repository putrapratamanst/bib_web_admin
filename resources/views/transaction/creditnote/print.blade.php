<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Note - PT. Brilliant Agensi Indonesia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .credit-note {
            max-width: 210mm;
            width: 210mm;
            margin: 0 auto;
            background-color: #fffacd;
            padding: 15mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
            min-height: auto;
        }

        .header {
            margin-bottom: 15px;
        }

        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .company-info {
            flex: 1;
        }

        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 5px;
        }

        .logo img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .company-name {
            font-size: 10px;
            color: #333;
            font-weight: normal;
            letter-spacing: 0.5px;
        }

        .no-box {
            border: 2px solid #333;
            padding: 8px 15px;
            min-width: 280px;
            height: 80px;
        }

        .no-label {
            font-size: 13px;
            margin-bottom: 10px;
        }

        .no-input {
            border-bottom: 1px solid #333;
            height: 25px;
            width: 100%;
            font-size: 13px;
            padding-top: 5px;
        }

        .title-row {
            margin-top: 20px;
        }

        .document-title {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #333;
        }

        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }

        .left-section {
            border: 2px solid #333;
            padding: 12px;
        }

        .right-section {
            border: 2px solid #333;
            padding: 12px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .field {
            margin-bottom: 8px;
            display: flex;
            align-items: baseline;
        }

        .field-label {
            font-style: italic;
            min-width: 120px;
            font-size: 13px;
        }

        .field-colon {
            margin: 0 10px;
        }

        .field-value {
            flex: 1;
            border-bottom: 1px solid #333;
            min-height: 20px;
            font-size: 13px;
        }

        .remarks-section {
            margin-top: 15px;
        }

        .remarks-label {
            font-style: italic;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .remarks-area {
            border: 1px solid #333;
            min-height: 60px;
            padding: 8px;
        }

        .premium-title {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .premium-table {
            width: 100%;
        }

        .premium-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #333;
            min-height: 30px;
        }

        .premium-row:last-child {
            border-bottom: none;
        }

        .premium-label {
            padding: 8px;
            border-right: 1px solid #333;
            font-size: 12px;
        }

        .premium-value {
            padding: 8px;
            font-size: 12px;
        }

        .spacer {
            height: 30px;
            border-right: 1px solid #333;
        }

        .net-premium-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 50px;
        }

        .eoe-section {
            text-align: right;
            padding: 8px;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .bottom-section {
            margin-top: 10px;
            border: 2px solid #333;
            min-height: 40px;
            grid-column: 1 / -1;
        }

        .signature-section {
            text-align: right;
            padding: 10px;
            margin-top: 10px;
        }

        .signature-line {
            display: inline-block;
            border-bottom: 2px solid #333;
            min-width: 200px;
            margin-top: 30px;
        }

        .signature-label {
            font-size: 12px;
            margin-top: 5px;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
                margin: 0;
            }
            .credit-note {
                box-shadow: none;
                max-width: 100%;
                width: 210mm;
                padding: 10mm;
                margin: 0;
            }
            @page {
                size: A4;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="credit-note">
        <div class="header">
            <div class="top-row">
                <div class="company-info">
                    <div class="logo">
                        <img src="{{ asset('logo.png') }}" alt="Brilliant Logo">
                    </div>
                    <div class="company-name">PT. BRILLIANT AGENSI INDONESIA</div>
                </div>
                <div class="no-box">
                    <div class="no-label">No :</div>
                    <div class="no-input">{{ $creditNote->number }}</div>
                </div>
            </div>
            
            <div class="title-row">
                <div class="document-title">CREDIT NOTE</div>
            </div>
        </div>

        <div class="content">
            <div class="left-section">
                <div class="section-title">DESCRIPTION</div>
                
                <div class="field">
                    <span class="field-label">Policy No</span>
                    <span class="field-colon">:</span>
                    <div class="field-value">{{ $creditNote->contract->policy_number ?? '-' }}</div>
                </div>

                <div class="field">
                    <span class="field-label">Period</span>
                    <span class="field-colon">:</span>
                    <div class="field-value">{{ $creditNote->contract->period_start ? $creditNote->contract->period_start->format('d M Y') : '' }} - {{ $creditNote->contract->period_end ? $creditNote->contract->period_end->format('d M Y') : '' }}</div>
                </div>

                <div class="field">
                    <span class="field-label">Sum Insured</span>
                    <span class="field-colon">:</span>
                    <div class="field-value">{{ $creditNote->currency_code }} {{ number_format($creditNote->contract->coverage_amount ?? 0, 2, ',', '.') }}</div>
                </div>

                <div class="remarks-section">
                    <div class="remarks-label">REMARKS :</div>
                    <div class="remarks-area">{{ $creditNote->description ?? '-' }}</div>
                </div>
            </div>

            <div class="right-section">
                <div class="premium-title">PREMIUM CALCULATION</div>
                
                <div class="premium-table">
                    <div class="premium-row">
                        <div class="premium-label">Currency</div>
                        <div class="premium-value">{{ $creditNote->currency_code }}</div>
                    </div>
                    <div class="premium-row">
                        <div class="premium-label">Gross Premium</div>
                        <div class="premium-value">{{ number_format($creditNote->contract->gross_premium ?? 0, 2, ',', '.') }}</div>
                    </div>
                    <div class="premium-row">
                        <div class="premium-label">Policy/Endorsement Cost</div>
                        <div class="premium-value">
                            @php
                                $discount = $creditNote->contract->discount ?? 0;
                                $discountAmount = ($creditNote->contract->gross_premium ?? 0) * ($discount / 100);
                            @endphp
                            {{ number_format($discountAmount, 2, ',', '.') }}
                            @if($discount > 0)
                                ({{ number_format($discount, 2, ',', '.') }}%)
                            @endif
                        </div>
                    </div>
                    <div class="premium-row">
                        <div class="premium-label">Stamp duty</div>
                        <div class="premium-value">{{ number_format($creditNote->contract->stamp_fee ?? 0, 2, ',', '.') }}</div>
                    </div>
                    <div class="premium-row">
                        <div class="spacer"></div>
                        <div class="premium-value"></div>
                    </div>
                    <div class="net-premium-row">
                        <div class="premium-label" style="border-right: 1px solid #333;">Net Premium</div>
                        <div class="premium-value">{{ number_format($creditNote->amount, 2, ',', '.') }}</div>
                    </div>
                    <div class="premium-row">
                        <div class="premium-label" style="border:none;"></div>
                        <div class="eoe-section">E. & O.E.</div>
                    </div>
                </div>
            </div>

            <div class="bottom-section"></div>
        </div>

        <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-label">Authorized Signature</div>
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