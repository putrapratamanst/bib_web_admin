<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debit Note - {{ $billing->number ?? $billing->billing_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 5px;
        }
        
        .container {
            max-width: 850px;
            height: 650px;
            margin: 0 auto;
            background: white;
            padding: 15mm;
            position: relative;
            overflow: hidden;
        }
        
        /* Top left header */
        .company-header {
            position: absolute;
            top: 10mm;
            left: 15mm;
            width: 80mm;
        }
        
        .company-logo {
            font-size: 18px;
            font-weight: bold;
            color: #0066cc;
        }
        
        .company-name {
            font-size: 11px;
            font-weight: bold;
            margin-top: 2px;
        }

        /* Top right - Client info */
        .client-header {
            position: absolute;
            top: 10mm;
            right: 15mm;
            width: 90mm;
            font-size: 9px;
        }
        
        .client-name {
            font-weight: bold;
            margin-bottom: 2mm;
        }
        
        .client-address {
            margin-bottom: 3mm;
            line-height: 1.3;
        }
        
        .dn-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        
        .dn-label {
            font-weight: bold;
            width: 15mm;
        }
        
        .dn-value {
            flex: 1;
        }
        
        /* Main title */
        .main-title {
            position: absolute;
            top: 45mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        /* Description section (left) */
        .description-section {
            position: absolute;
            top: 55mm;
            left: 15mm;
            width: 95mm;
        }
        
        .description-title {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
            padding: 3px;
            margin-bottom: 2mm;
            background-color: #f9f9f9;
        }
        
        .desc-row {
            display: flex;
            font-size: 8.5px;
            margin-bottom: 1.5mm;
            align-items: flex-start;
        }
        
        .desc-label {
            width: 28mm;
            padding-right: 2mm;
        }
        
        .desc-colon {
            width: 3mm;
        }
        
        .desc-value {
            flex: 1;
            line-height: 1.3;
        }
        
        .desc-remarks {
            margin-top: 2mm;
            font-size: 8.5px;
            color: #0066cc;
            line-height: 1.4;
        }
        
        .desc-footer-note {
            margin-top: 5mm;
            font-size: 7.5px;
            font-style: italic;
            color: #0066cc;
            line-height: 1.4;
        }
        
        /* Premium Calculation section (right) */
        .premium-section {
            position: absolute;
            top: 55mm;
            right: 15mm;
            width: 85mm;
        }
        
        .premium-title {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
            padding: 3px;
            margin-bottom: 2mm;
            background-color: #f9f9f9;
        }
        
        .premium-row {
            display: flex;
            justify-content: space-between;
            font-size: 8.5px;
            margin-bottom: 1.5mm;
            padding: 2px 3px;
        }
        
        .premium-row.total {
            font-weight: bold;
            padding: 3px;
            margin-top: 3mm;
            margin-bottom: 3mm;
        }
        
        .premium-label {
            flex: 1;
        }
        
        .premium-currency {
            width: 15mm;
            text-align: center;
        }
        
        .premium-value {
            text-align: right;
            min-width: 30mm;
            padding-right: 2mm;
        }
        
        .eoe-text {
            text-align: right;
            font-size: 8px;
            font-style: italic;
            margin-top: 2mm;
        }
        
        /* Signature section */
        .signature-section {
            position: absolute;
            top: 180mm;
            right: 15mm;
            width: 70mm;
            text-align: center;
        }
        
        .paid-stamp {
            margin-bottom: 10mm;
            font-size: 24px;
            font-weight: bold;
            color: #666;
            border: 3px solid #666;
            padding: 5px 15px;
            display: inline-block;
            transform: rotate(-10deg);
        }
        
        .signature-label {
            font-size: 9px;
            margin-top: 15mm;
            padding-top: 5mm;
            border-top: 1px solid #000;
        }
        
        /* Footer Section */
        .footer-section {
            position: absolute;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
        }
        
        .bank-info {
            font-size: 8px;
            line-height: 1.5;
            margin-bottom: 3mm;
        }
        
        .bank-title {
            font-weight: bold;
        }
        
        .company-footer {
            font-size: 7px;
            text-align: center;
            line-height: 1.4;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 2mm;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        
        .print-button:hover {
            background-color: #1976D2;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .print-button {
                display: none;
            }
            
            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Print</button>

    <div class="container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-logo">Brilliant 🏢</div>
            <div style="font-size: 9px; font-style: italic;">Insurance Brokers</div>
            <div class="company-name">PT. BRILLIANT INSURANCE BROKERS</div>
        </div>

        <!-- Client Header -->
        <div class="client-header">
            @php
                $contract = $billing->debitNote->contract ?? null;
                $contact = $contract?->contact ?? $billing->debitNote->contact;
                $billingAddress = $billing->debitNote->billingAddress;
            @endphp
            <div class="client-name">{{ $contact?->display_name ?? $contact?->name ?? '-' }}</div>
            <div class="client-address">{{ $billingAddress?->address ?? ($contact?->address ?? 'N/A') }}</div>
            <div class="dn-info">
                <div class="dn-label">No :</div>
                <div class="dn-value">{{ $billing->number ?? $billing->billing_number }}</div>
                <div class="dn-label" style="margin-left: 5mm;">Tanggal :</div>
                <div class="dn-value">{{ $billing->debitNote?->date_formatted ?? \Carbon\Carbon::parse($billing->date)->format('d-m-Y') }}</div>
            </div>
        </div>

        <!-- Main Title -->
        <div class="main-title">DEBIT NOTE</div>

        <!-- Description Section -->
        <div class="description-section">
            <div class="description-title">D E S C R I P T I O N</div>
            
            @php
                $policyNumber = $contract?->policy_number ?? '-';
                $endorsementNumber = $contract?->endorsement_number ?? '0';
                $startDate = $contract?->period_start ?? $billing->debitNote?->date ?? $billing->date;
                $endDate = $contract?->period_end ?? \Carbon\Carbon::parse($startDate)->addYear()->toDateString();
                $coverageName = $contract?->contractType?->name ?? 'Insurance Coverage';
                $installmentNum = $billing->debitNote->installment ?? 0;
            @endphp
            
            <div class="desc-row">
                <div class="desc-label">{{-- Policy No --}}</div>
                <div class="desc-colon">{{-- : --}}</div>
                <div class="desc-value">{{ $policyNumber }} (No End. {{ $endorsementNumber }})</div>
            </div>
            
            <div class="desc-row">
                <div class="desc-label">{{-- Period --}}</div>
                <div class="desc-colon">{{-- : --}}</div>
                <div class="desc-value">{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</div>
            </div>
            
            <div class="desc-row">
                <div class="desc-label">{{-- Sum Insured --}}</div>
                <div class="desc-colon">{{-- : --}}</div>
                <div class="desc-value">{{ $contract?->currency_code ?? 'IDR' }}  {{ number_format($contract?->coverage_amount ?? 0, 0, '.', ',') }},-</div>
            </div>
            
            <div class="desc-row">
                <div class="desc-label">{{-- REMARKS --}}</div>
                <div class="desc-colon">{{-- : --}}</div>
                <div class="desc-value">{{ $coverageName }}</div>
            </div>
            
            <div class="desc-remarks">
                {!! nl2br(e($contract?->memo ?? '-')) !!}
            </div>

            <div class="desc-footer-note">
                @php
                    $dueDateText = $billing->due_date ? \Carbon\Carbon::parse($billing->due_date)->format('d-m-Y') : '';
                    $dueClause = $dueDateText ? ", yaitu tanggal {$dueDateText}" : ".";
                @endphp
                "Jatuh Tempo pembayaran Premi adalah 7 hari setelah Polis diterima"<br/>
                "Klaim dapat ditolak jika pembayaran premi melebihi jatuh tempo"<br/>
                "Pembayaran Premi ditujukan atas atas nama dituangkan nomor Debit Note & Polis tersebut"
            </div>
        </div>

        <!-- Premium Calculation Section -->
        <div class="premium-section">
            <div class="premium-title">PREMIUM CALCULATION</div>
            
            @php
                $currencyName = $contract?->currency?->name ?? 'Rupiah Indonesia';
                $currencyCode = $contract?->currency_code ?? 'IDR';
                
                // Billing amount (gross premium untuk billing ini)
                $billingAmount = $billing->amount ?? 0;
                
                $gross = $billingAmount;
                $policyFee = $contract?->policy_fee ?? 0;
                
                // Extract installment number from billing number (e.g., BIB/D25/0001-INST1)
                $billingNumber = $billing->number ?? $billing->billing_number ?? '';
                $installmentNumber = 0;
                if (preg_match('/-INST(\d+)/i', $billingNumber, $matches)) {
                    $installmentNumber = (int)$matches[1];
                }
                
                // Stamp duty
                $stampDuty = $contract?->stamp_fee ?? 0;
                
                // Calculate net premium
                if ($installmentNumber == 1) {
                    // Installment 1: amount + policy_fee + stamp_fee
                    $net = $billingAmount + $policyFee + $stampDuty;
                } else {
                    // Other installments: amount only
                    $net = $billingAmount;
                }
                
                $discountPercent = $contract?->discount ?? 0;
                $discountAmount = ($gross * $discountPercent) / 100;
            @endphp
            
            <div class="premium-row">
                <div class="premium-label">{{-- Currency --}}</div>
                <div class="premium-value" style="text-align: center;">{{ $currencyName }}</div>
            </div>
            
            <div class="premium-row">
                <div class="premium-label">{{-- Gross Premium --}}</div>
                <div class="premium-currency">{{ $currencyCode }}</div>
                <div class="premium-value">{{ number_format($gross, 2, ',', '.') }}</div>
            </div>
            
            <div class="premium-row">
                <div class="premium-label">{{-- Policy/Endorsement Cost --}}</div>
                <div class="premium-currency">{{  $currencyCode  }}</div>
                <div class="premium-value">{{ $installmentNumber == 1 ? number_format($policyFee, 2, ',', '.') : '0,-' }}</div>
            </div>
            
            <div class="premium-row">
                <div class="premium-label">{{-- Stamp duty --}}</div>
                <div class="premium-currency">{{  $currencyCode  }}</div>
                <div class="premium-value">{{ $installmentNumber == 1 ? number_format($stampDuty, 2, ',', '.') : '0,-' }}</div>
            </div>
            
            @if($discountPercent > 0)
            <div class="premium-row">
                <div class="premium-label">{{-- Discount {{ number_format($discountPercent, 2, ',', '.') }}% --}}</div>
                <div class="premium-currency">{{ $currencyCode }}</div>
                <div class="premium-value">({{ number_format($discountAmount, 2, ',', '.') }})</div>
            </div>
            @endif
            
            <div class="premium-row total">
                <div class="premium-label">{{-- Net Premium --}}</div>
                <div class="premium-currency">{{ $currencyCode }}</div>
                <div class="premium-value">{{ number_format($net, 2, ',', '.') }}</div>
            </div>
            
            <div class="eoe-text">E. & O.E.</div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="paid-stamp">PAID<br><span style="font-size: 14px;">{{ now()->format('d M Y') }}</span></div>
            <div class="signature-label">Authorized Signature</div>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
            <div class="bank-info">
                <div class="bank-title">PT. Brilliant Insurance Brokers</div>
                <div>Bank Mandiri KCP Botanical Garden a/c No. 070.0006.524123 (IDR)</div>
                <div>BNI 46 Cab. Senayan a/c No. 025.9060.691 (IDR)</div>
                <div>Bank Mandiri KCP Botanical Garden a/c No. 070.0006.524131 (USD)</div>
                <div>BCA KCP Puri Botanical a/c No. 6260.5866.88 (IDR)</div>
            </div>
            <div class="company-footer">
                Rukan Botanic Junction, Blok i 10 No. 60, Joglo - Jakarta Barat 11640<br/>
                Telp. : 021- 55589803, 22542676, 25680394<br/>
                Email : admin@brilliantinsbrokers.com
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('afterprint', function() {
            window.history.back();
        });
    </script>
</body>
</html>
