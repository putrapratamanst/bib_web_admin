<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debit Note - {{ $billing->billing_number }}</title>
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
            max-width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 0;
            position: relative;
            overflow: hidden;
        }
        
        /* Header section */
        .header-section {
            position: absolute;
            top: 35mm;
            right: 15mm;
            width: 65mm;
        }
        
        .header-item {
            display: flex;
            margin-bottom: 2mm;
            font-size: 9px;
        }
        
        .header-label {
            width: 20mm;
            font-weight: bold;
        }
        
        .header-value {
            flex: 1;
            border-bottom: 0.5px solid #000;
            padding-bottom: 1px;
            margin-left: 2mm;
        }
        
        /* Description section (left) */
        .description-section {
            position: absolute;
            top: 60mm;
            left: 15mm;
            width: 95mm;
        }
        
        .description-title {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
            padding: 2px;
            margin-bottom: 2px;
        }
        
        .desc-row {
            display: flex;
            font-size: 8px;
            margin-bottom: 1mm;
        }
        
        .desc-label {
            width: 30mm;
            font-weight: bold;
            padding-right: 2mm;
        }
        
        .desc-value {
            flex: 1;
            border-bottom: 0.5px solid #000;
            padding-bottom: 1px;
        }
        
        .desc-value-multiline {
            flex: 1;
            border-bottom: 0.5px solid #000;
            padding-bottom: 1px;
            line-height: 1.2;
        }
        
        /* Premium Calculation section (right) */
        .premium-section {
            position: absolute;
            top: 60mm;
            right: 15mm;
            width: 65mm;
        }
        
        .premium-title {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
            padding: 2px;
            margin-bottom: 2px;
        }
        
        .premium-row {
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            margin-bottom: 1mm;
            padding-bottom: 1px;
            border-bottom: 0.5px solid #ccc;
        }
        
        .premium-row.total {
            font-weight: bold;
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
            padding: 1px 0;
            margin: 2mm 0;
        }
        
        .premium-label {
            flex: 1;
        }
        
        .premium-value {
            text-align: right;
            min-width: 25mm;
            padding-right: 1mm;
        }
        
        /* Table section */
        .table-section {
            position: absolute;
            top: 150mm;
            left: 15mm;
            right: 15mm;
            width: 170mm;
        }
        
        .table-label {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 2px;
            text-align: center;
        }
        
        .table-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        
        .table-items td {
            border: 0.5px solid #000;
            padding: 2px 3px;
            text-align: center;
        }
        
        .table-items td:first-child {
            text-align: left;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 15px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            z-index: 1000;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Print</button>

    <div class="container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-item">
                <div class="header-label">No :</div>
                <div class="header-value">{{ $billing->billing_number }}</div>
            </div>
            <div class="header-item">
                <div class="header-label">Tanggal :</div>
                <div class="header-value">{{ \Carbon\Carbon::parse($billing->date)->format('d-m-Y') }}</div>
            </div>
        </div>

        <!-- Description Section -->
        <div class="description-section">
            <div class="description-title">DESCRIPTION</div>
            
            <div class="desc-row">
                <div class="desc-label">Placing No</div>
                <div class="desc-value">{{ $billing->debitNote->contract->number ?? '-' }}</div>
            </div>
            
            <div class="desc-row">
                <div class="desc-label">Period</div>
                <div class="desc-value">
                    @php
                        $contract = $billing->debitNote->contract;
                        $startDate = $contract->effective_date ?? $billing->debitNote->date ?? $billing->date;
                        $endDate = $contract->expiry_date ?? \Carbon\Carbon::parse($startDate)->addYear()->toDateString();
                    @endphp
                    {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}
                </div>
            </div>
            
            <div class="desc-row">
                <div class="desc-label">Sum Insured</div>
                <div class="desc-value">IDR {{ number_format($billing->debitNote->coverage_amount ?? 0, 0, ',', '.') }},-</div>
            </div>
            
            <div class="desc-row">
                <div class="desc-label">REMARKS</div>
                <div class="desc-value">{{ $billing->debitNote->contract->contract_type->name ?? 'Insurance Coverage' }}</div>
            </div>
            
            <div class="desc-row">
                <div class="desc-label">Risk Location</div>
                <div class="desc-value-multiline">{{ $billing->debitNote->contract->contact->address ?? 'N/A' }}</div>
            </div>
            
            <div class="desc-row">
                <div class="desc-label">Stocks</div>
                <div class="desc-value">IDR {{ number_format($billing->debitNote->coverage_amount ?? 0, 0, ',', '.') }},00</div>
            </div>
        </div>

        <!-- Premium Calculation Section -->
        <div class="premium-section">
            <div class="premium-title">PREMIUM CALCULATION</div>
            
            <div class="premium-row">
                <div class="premium-label">Currency</div>
                <div class="premium-value">{{ $billing->debitNote->currency_code ?? 'IDR' }}</div>
            </div>
            
            @php
                $gross = $billing->debitNote->gross_premium ?? $billing->amount;
                $endorsementCost = $billing->debitNote->endorsement_cost ?? 0;
                $stampDuty = $billing->debitNote->stamp_duty ?? ($gross * 0.02);
                $discount = $billing->debitNote->discount ?? 0;
                $discountAmount = $gross * ($discount / 100);
                $net = $gross + $endorsementCost + $stampDuty - $discountAmount;
            @endphp
            
            <div class="premium-row">
                <div class="premium-label">Gross Premium</div>
                <div class="premium-value">{{ number_format($gross, 0, ',', '.') }},-</div>
            </div>
            
            <div class="premium-row">
                <div class="premium-label">Policy/Endorsement Cost</div>
                <div class="premium-value">{{ number_format($endorsementCost, 0, ',', '.') }},-</div>
            </div>
            
            <div class="premium-row">
                <div class="premium-label">Stamp duty</div>
                <div class="premium-value">{{ number_format($stampDuty, 0, ',', '.') }},-</div>
            </div>
            
            <div class="premium-row">
                <div class="premium-label">Discount {{ $discount }}%</div>
                <div class="premium-value">({{ number_format($discountAmount, 0, ',', '.') }}),-</div>
            </div>
            
            <div class="premium-row total">
                <div class="premium-label">Net Premium</div>
                <div class="premium-value">{{ number_format($net, 0, ',', '.') }},-</div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-section">
            <div class="table-label">INSTALLMENT DETAILS</div>
            <table class="table-items">
                <tbody>
                    <tr>
                        <td style="width: 40mm;">Installment</td>
                        <td style="width: 35mm;">Amount</td>
                        <td style="width: 35mm;">Due Date</td>
                        <td style="width: 60mm;">Description</td>
                    </tr>
                    <tr>
                        <td>
                            @php
                                $installmentNumber = $billing->debitNote->debitNoteBillings()
                                    ->where('date', '<=', $billing->date)
                                    ->orderBy('date')
                                    ->orderBy('id')
                                    ->get()
                                    ->search(function($item) use ($billing) {
                                        return $item->id === $billing->id;
                                    }) + 1;
                            @endphp
                            Installment {{ $installmentNumber }}
                        </td>
                        <td>{{ number_format($net, 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($billing->due_date)->format('d-m-Y') }}</td>
                        <td style="text-align: left;">Tagihan Premi Asuransi</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        window.addEventListener('afterprint', function() {
            window.history.back();
        });
    </script>
</body>
</html>
