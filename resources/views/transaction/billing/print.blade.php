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

        /*
         * Data-only overlay untuk pre-printed form.
         * Koordinat mengikuti Inisialisasi Batas dari dn_print_ok_popup.php (FPDF mm)
         * Asumsi bxhead=5, byhead=5 (dari cfg_marginprint)
         *
         * bxdes   = bxhead + 30  = 35mm   (value description)
         * bxket   = bxhead + 5   = 10mm   (remarks / keterangan / bank)
         * bxttg   = bxhead + 113 = 118mm  (nama tertanggung)
         * bxnodn  = bxhead + 122 = 127mm  (no debit note)
         * bxcalc  = bxhead + 157 = 162mm  (premium calc currency code)
         * bxcalcv = bxcalc + 7   = 169mm  (premium calc values, right-aligned 28mm)
         *
         * byttg   = byhead + 10  = 15mm
         * bynodn  = byhead + 34  = 39mm
         * bydes1  = byhead + 58  = 63mm   (Policy No)
         * bydes2  = byhead + 65  = 70mm   (Period)
         * bydes3  = byhead + 72  = 77mm   (Sum Insured)
         * bydes4  = byhead + 79  = 84mm   (Coverage/type)
         * bydes5  = byhead + 87  = 92mm   (Remarks/keterangan)
         * bydes7  = byhead + 105 = 110mm  (Nett Premium)
         * bykaki  = byhead + 138 = 143mm  (Footer/bank)
         */

        .page {
            width: 210mm;
            height: 155mm;
            margin: 0 auto;
            background: white;
            position: relative;
            overflow: hidden;
        }

        /* All data elements are absolutely positioned */
        .data {
            position: absolute;
            font-size: 9px;
            font-family: Arial, sans-serif;
        }

        /* Reference text - top left (small) */
        .ref-text {
            top: 6mm;
            left: 10mm;
            font-size: 8px;
        }

        /* Client name - bxttg, byttg-3 = 118, 12 */
        .client-nama1 {
            top: 12mm;
            left: 118mm;
            width: 80mm;
        }
        .client-nama2 {
            top: 15mm;
            left: 118mm;
            width: 80mm;
        }
        .client-alamat1 {
            top: 24mm;
            left: 118mm;
            width: 80mm;
        }
        .client-alamat2 {
            top: 27mm;
            left: 118mm;
            width: 80mm;
        }

        /* No DN - bxnodn=127, bynodn=39 */
        .dn-number {
            top: 39mm;
            left: 127mm;
            width: 73mm;
        }

        /* Description values - bxdes=35mm */
        .desc-val1 { top: 63mm; left: 35mm; }  /* Policy No */
        .desc-val2 { top: 70mm; left: 35mm; }  /* Period */
        .desc-val3 { top: 77mm; left: 35mm; }  /* Sum Insured */
        .desc-val4 { top: 84mm; left: 35mm; }  /* Coverage type */

        /* Remarks - bxket=10mm, bydes5=92mm */
        .remarks {
            top: 92mm;
            left: 10mm;
            width: 95mm;
            line-height: 1.3;
        }

        /* Sub-keterangan - bydes5-2+(4*7) = 92-2+28 = 118mm */
        .sub-keterangan {
            top: 118mm;
            left: 10mm;
            width: 95mm;
            font-size: 8px;
            font-style: italic;
            font-weight: bold;
            text-align: center;
            line-height: 1.4;
        }

        /* Bank info - bxket=10mm, bykaki=143mm */
        .bank-title {
            top: 141mm;
            left: 10mm;
            font-weight: bold;
        }
        .bank-line1 { top: 144mm; left: 10mm; font-size: 8px; font-weight: bold; }
        .bank-line2 { top: 147mm; left: 10mm; font-size: 8px; font-weight: bold; }
        .bank-line3 { top: 150mm; left: 10mm; font-size: 8px; font-weight: bold; }
        .bank-line4 { top: 153mm; left: 10mm; font-size: 8px; font-weight: bold; }

        /* Premium calc - currency name centered at bxcalc-3=159mm, width 41mm */
        .calc-currency-name {
            top: 62mm;
            left: 159mm;
            width: 41mm;
            text-align: center;
        }

        /* Premium calc rows: currency code at bxcalc=162mm, value at bxcalc+7=169mm right-aligned 28mm */
        .calc-row {
            left: 162mm;
        }
        .calc-val {
            left: 169mm;
            width: 28mm;
            text-align: right;
        }

        .calc-gross-cur   { top: 70.5mm; left: 162mm; }
        .calc-gross-val   { top: 69.5mm; left: 169mm; width: 28mm; text-align: right; }
        .calc-polis-cur   { top: 77.5mm; left: 162mm; }
        .calc-polis-val   { top: 76.5mm; left: 169mm; width: 28mm; text-align: right; }
        .calc-materai-cur { top: 84.5mm; left: 162mm; }
        .calc-materai-val { top: 83.5mm; left: 169mm; width: 28mm; text-align: right; }
        .calc-disc-label  { top: 91mm;   left: 129mm; }
        .calc-disc-cur    { top: 91mm;   left: 162mm; }
        .calc-disc-val    { top: 90mm;   left: 169mm; width: 28mm; text-align: right; }

        /* Nett premium - bydes7-3=107mm */
        .calc-nett-cur    { top: 107mm;  left: 162mm; }
        .calc-nett-val    { top: 106.5mm; left: 169mm; width: 28mm; text-align: right; }

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
            body { background: white; padding: 0; margin: 0; }
            .print-button { display: none; }
            .page { box-shadow: none; margin: 0; }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Print</button>

    @php
        $contract = $billing->debitNote->contract ?? null;
        $contact = $contract?->contact ?? $billing->debitNote->contact;
        $billingAddress = $billing->debitNote->billingAddress;

        $policyNumber = $contract?->policy_number ?? '-';
        $endorsementNumber = $contract?->endorsement_number ?? '0';
        $startDate = $contract?->period_start ?? $billing->debitNote?->date ?? $billing->date;
        $endDate = $contract?->period_end;
        $coverageName = $contract?->contractType?->name ?? 'Insurance Coverage';
        $installmentNum = $billing->debitNote->installment ?? 0;

        $currencyName = $contract?->currency?->name ?? 'Rupiah Indonesia';
        $currencyCode = $contract?->currency_code ?? 'IDR';

        $grosspremi = $billing->amount ?? 0;
        $bpolis = $contract?->policy_fee ?? 0;
        $bmaterai = $contract?->stamp_fee ?? 0;
        $disc = $contract?->discount ?? 0;
        $jmldisc = ($grosspremi * $disc) / 100;

        $billingNumber = $billing->number ?? $billing->billing_number ?? '';
        $installmentNumber = 0;
        if (preg_match('/-INST(\d+)/i', $billingNumber, $matches)) {
            $installmentNumber = (int)$matches[1];
        }

        // Nett premium (sesuai dn_print_ok_popup.php)
        $nettpremi = $grosspremi + $bpolis + $bmaterai - $jmldisc;
        if ($installmentNumber > 1) {
            $nettpremi = $grosspremi;
            $bpolis = 0;
            $bmaterai = 0;
        }

        // Installment display
        $showInstallment = ($installmentNum > 0);
        $persentase = $billing->percentage ?? null;
        if ($persentase == 100.00) {
            $showInstallment = false;
        }

        // Contact fields
        $nama1 = $contact?->display_name ?? $contact?->name ?? '-';
        $nama2 = $contact?->name2 ?? '';
        $alamat1 = $billingAddress?->address ?? ($contact?->address ?? '');
        $alamat2 = $contact?->address2 ?? '';

        // Coverage code for reference
        $coverageCode = $contract?->contractType?->code ?? '';
        $kdmarketing = $contact?->marketing_code ?? '';
        $noend = $endorsementNumber;

        // Tanggal terima polis
        $tglterimapol = $billing->debitNote?->date_formatted ?? \Carbon\Carbon::parse($billing->date)->format('d-m-Y');
    @endphp

    <div class="page">

        {{-- Reference text (kecil, pojok kiri atas) --}}
        <div class="data ref-text">
            {{ $coverageCode }}&nbsp;&nbsp;&nbsp;{{ now()->format('d/m/Y') }}--{{ $kdmarketing }}&nbsp;&nbsp;&nbsp;Noend #{{ $noend }}
        </div>

        {{-- Nama Tertanggung (bxttg=118, byttg-3=12) --}}
        <div class="data client-nama1">{{ $nama1 }}</div>
        @if($nama2)
        <div class="data client-nama2">{{ $nama2 }}</div>
        @endif
        <div class="data client-alamat1">{{ $alamat1 }}</div>
        @if($alamat2)
        <div class="data client-alamat2">{{ $alamat2 }}</div>
        @endif

        {{-- No Debit Note (bxnodn=127, bynodn=39) --}}
        <div class="data dn-number">
            {{ $billing->number ?? $billing->billing_number }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tanggal : {{ $tglterimapol }}
        </div>

        {{-- Description values (bxdes=35mm) --}}
        {{-- bydes1=63: Policy No --}}
        <div class="data desc-val1">{{ $policyNumber }}&nbsp;&nbsp;&nbsp;(No End. {{ $endorsementNumber }})</div>

        {{-- bydes2=70: Period --}}
        <div class="data desc-val2">
            @if($endDate)
                {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}
            @else
                {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}
            @endif
        </div>

        {{-- bydes3=77: Sum Insured --}}
        <div class="data desc-val3">{{ $currencyCode }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($contract?->coverage_amount ?? 0, 2, ',', '.') }}</div>

        {{-- bydes4=84: Coverage type --}}
        <div class="data desc-val4">
            @if($showInstallment)
                {{ $coverageName }} --- Installment {{ $installmentNum }}
            @else
                {{ $coverageName }}
            @endif
        </div>

        {{-- Remarks/Keterangan (bxket=10, bydes5=92) --}}
        <div class="data remarks">{!! nl2br(e($contract?->memo ?? '')) !!}</div>

        {{-- Sub-keterangan (bxket=10, bydes5-2+28=118) --}}
        <div class="data sub-keterangan">
            "Jatuh tempo pembayaran Premi adalah 7 hari setelah Polis diterima"<br>
            "Klaim dapat ditolak jika pembayaran premi melebihi jatuh tempo"<br>
            "Pembayaran Premi dianggap sah apabila disertakan nomer Debit Note & Bukti Bayar"
        </div>

        {{-- Bank info (bxket=10, bykaki=143) --}}
        <div class="data bank-title">PT. Brilliant Insurance Brokers</div>
        <div class="data bank-line1">Bank Mandiri KCP Botanical Garden a/c No. 070.0006.524123 (IDR)</div>
        <div class="data bank-line2">BNI 46 Cab. Senayan a/c No. 025.9960.691 (IDR)</div>
        <div class="data bank-line3">Bank Mandiri KCP Botanical Garden a/c No. 070.0006.524131 (USD)</div>
        <div class="data bank-line4">BCA KCP Puri Botanical a/c No. 6250.8855.88 (IDR)</div>

        {{-- Premium Calculation: Currency name (centered bxcalc-3=159, bydes1-0.8=62) --}}
        <div class="data calc-currency-name">{{ $currencyName }}</div>

        {{-- Gross Premium (bydes2=70) --}}
        <div class="data calc-gross-cur">{{ $currencyCode }}</div>
        <div class="data calc-gross-val">{{ number_format($grosspremi, 2, ',', '.') }}</div>

        {{-- Policy fee (bydes3=77) --}}
        <div class="data calc-polis-cur">{{ $currencyCode }}</div>
        <div class="data calc-polis-val">{{ number_format($bpolis, 2, ',', '.') }}</div>

        {{-- Stamp duty (bydes4=84) --}}
        <div class="data calc-materai-cur">{{ $currencyCode }}</div>
        <div class="data calc-materai-val">{{ number_format($bmaterai, 2, ',', '.') }}</div>

        {{-- Discount (bydes5=92, if applicable) --}}
        @if($disc > 0)
        <div class="data calc-disc-label">Discount {{ number_format($disc, 2) }}%</div>
        <div class="data calc-disc-cur">{{ $currencyCode }}</div>
        <div class="data calc-disc-val">({{ number_format($jmldisc, 2, ',', '.') }})</div>
        @endif

        {{-- Nett Premium (bydes7-3=107) --}}
        <div class="data calc-nett-cur">{{ $currencyCode }}</div>
        <div class="data calc-nett-val">{{ number_format($nettpremi, 2, ',', '.') }}</div>

    </div>

    <script>
        window.addEventListener('afterprint', function() {
            window.history.back();
        });
    </script>
</body>
</html>
