<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Jurnal - {{ $journalEntry->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 5px;
            background-color: #f5f5f5;
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
        
        .header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        
        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            font-weight: normal;
        }
        
        .top-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .left-box {
            border: 2px solid #000;
        }
        
        .right-box {
            border: 2px solid #000;
        }
        
        .field-row {
            border-bottom: 2px solid #000;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            min-height: 32px;
        }
        
        .field-row:last-child {
            border-bottom: none;
        }
        
        .field-label {
            font-size: 11px;
            color: #333;
            font-weight: normal;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-table td {
            border: 2px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-size: 10px;
            color: #666;
            height: 35px;
        }
        
        .main-tables {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .table-section {
            border: 2px solid #000;
        }
        
        .table-header {
            background-color: transparent;
            padding: 6px 8px;
            text-align: center;
            font-size: 12px;
            letter-spacing: 4px;
            color: #666;
            border-bottom: 2px solid #000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table td {
            border: 2px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-size: 10px;
            color: #333;
            height: 32px;
        }
        
        .col-header {
            font-weight: normal;
            color: #666;
        }
        
        .checkmark-col {
            width: 30px;
            position: relative;
        }
        
        .checkmark {
            font-size: 16px;
            color: #999;
        }
        
        .rupiah-section {
            border: 2px solid #000;
            padding: 6px 10px;
            margin-bottom: 12px;
            min-height: 32px;
        }
        
        .rupiah-label {
            font-size: 11px;
            color: #666;
        }
        
        .keterangan-section {
            border: 2px solid #000;
            padding: 8px 10px;
            min-height: 60px;
            margin-bottom: 8px;
        }
        
        .keterangan-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .keterangan-content {
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        
        .footer {
            font-size: 9px;
            color: #333;
            text-align: left;
            line-height: 1.4;
            margin-top: 8px;
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
        
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .container {
                padding: 20px;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Print</button>

    <div class="container">
        <div class="header">PT. BRILLIANT INSURANCE BROKERS</div>
        <div class="subtitle">TRANSFER JURNAL</div>
        
        <div class="top-section">
            <div class="left-box">
                <div class="field-row">
                    <div class="field-label">Tanggal : {{ $journalEntry->date_formatted }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">No. : {{ $journalEntry->number }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Kpd/dari prk : {{ $journalEntry->reference }}</div>
                </div>
            </div>
            
            <div class="right-box">
                <table class="signature-table">
                    <tr>
                        <td>Pimpinan</td>
                        <td>Kasir</td>
                        <td>Pemeriksa</td>
                    </tr>
                    <tr>
                        <td style="height: 60px;"></td>
                        <td style="height: 60px;"></td>
                        <td style="height: 60px;"></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="main-tables">
            <div class="table-section">
                <div class="table-header">DEBIT</div>
                <table>
                    <tr>
                        <td class="col-header">Perkiraan</td>
                        <td class="checkmark-col"><span class="checkmark">✓</span></td>
                        <td class="col-header">Nilai (Rp.)</td>
                    </tr>
                    @php
                        $debitDetails = $journalEntry->details->filter(fn($d) => $d->debit > 0);
                        $debitRows = max(3, $debitDetails->count());
                    @endphp
                    
                    @for($i = 0; $i < $debitRows; $i++)
                        @php
                            $detail = $debitDetails->get($i);
                        @endphp
                        <tr>
                            <td style="text-align: left;">{{ $detail?->chartOfAccount->display_name ?? '' }}</td>
                            <td>{{ $detail ? '✓' : '' }}</td>
                            <td style="text-align: right;">{{ $detail ? $detail->debit_formatted : '' }}</td>
                        </tr>
                    @endfor
                    
                    <tr>
                        <td class="col-header">Jumlah</td>
                        <td></td>
                        <td style="text-align: right; font-weight: bold;">{{ $journalEntry->total_debit_formatted }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="table-section">
                <div class="table-header">KREDIT</div>
                <table>
                    <tr>
                        <td class="col-header">Perkiraan</td>
                        <td class="checkmark-col"><span class="checkmark">✓</span></td>
                        <td class="col-header">Nilai (Rp.)</td>
                    </tr>
                    @php
                        $creditDetails = $journalEntry->details->filter(fn($d) => $d->credit > 0)->values();
                        $creditRows = max(3, $creditDetails->count());
                    @endphp
                    @for($i = 0; $i < $creditRows; $i++)
                        @php
                            $detail = $creditDetails->get($i);
                        @endphp
                        <tr>
                            <td style="text-align: left;">{{ $detail?->chartOfAccount->display_name ?? '' }}</td>
                            <td>{{ $detail ? '✓' : '' }}</td>
                            <td style="text-align: right;">{{ $detail ? $detail->credit_formatted : '' }}</td>
                        </tr>
                    @endfor
                    
                    <tr>
                        <td class="col-header">Jumlah</td>
                        <td></td>
                        <td style="text-align: right; font-weight: bold;">{{ $journalEntry->total_credit_formatted }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="rupiah-section">
            <span class="rupiah-label">Rupiah : {{ \App\Helpers\TerbilangHelper::terbilang($journalEntry->total_debit) }}</span>
        </div>
        
        <div class="keterangan-section">
            <div class="keterangan-label">Keterangan :</div>
            <div class="keterangan-content">{{ $journalEntry->description ?? '' }}</div>
        </div>
        
        <div class="footer">
            Rukan Botanic Junction, Mega Kebon Jeruk, Blok H 7 No.26, Joglo- Jakarta Barat 11540  Tel.: 0828-1706-7000 / 8000  Fax.: 021-58906387  Email : info@brilliantinsbrokers.com
        </div>
    </div>
</body>
</html>
