@php
use App\Helpers\TerbilangHelper;
$total = $cashBank->amount;

// Calculate Loss/Gain on Collection from allocations (yang sudah di-tick)
$lossOnCollection = 0;
$gainOnCollection = 0;
$totalArWriteOff = 0;

// Calculate Kekurangan Bayar Premi (yang belum di-tick tapi ada selisih)
$kekuranganBayarPremi = 0;

foreach ($allocations as $allocation) {
    if ($allocation->write_off_type === 'loss') {
        $lossOnCollection += $allocation->write_off_amount;
        $totalArWriteOff += $allocation->write_off_amount;
    } elseif ($allocation->write_off_type === 'gain') {
        $gainOnCollection += $allocation->write_off_amount;
        $totalArWriteOff -= $allocation->write_off_amount;
    } else {
        // write_off_type = 'none' - belum di-tick, hitung kekurangan
        // Get billing amount for this allocation
        $billing = $allocation->debitNoteBilling;
        if ($billing) {
            // Check if this is first installment for policy/stamp fee
            preg_match('/-INST(\d+)/i', $billing->billing_number, $matches);
            $installmentNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            
            $billingAmount = floatval($billing->amount);
            
            // Add policy fee and stamp fee for first installment only
            //if ($installmentNumber == 1 && $billing->debitNote && $billing->debitNote->contract) {
              //  $policyFee = $billing->debitNote->contract->policy_fee ?? 0;
                //$stampFee = $billing->debitNote->contract->stamp_fee ?? 0;
                //$billingAmount += $policyFee + $stampFee;
            //} 
            
            // Calculate difference (billing - allocated)
            $difference = $billingAmount - floatval($allocation->allocation);
            if ($difference > 0) {
                $kekuranganBayarPremi += $difference;
            }
        }
    }
}

// Total AR to credit = allocations + loss on collection - gain on collection
$totalArCredit = $allocations->sum('allocation') + $totalArWriteOff;

// Total Debit = Bank + Loss on Collection + Kekurangan Bayar Premi
$totalDebit = $total + $lossOnCollection + $kekuranganBayarPremi;

// Total Credit = AR + Gain on Collection
$totalCredit = $totalArCredit + $gainOnCollection + $kekuranganBayarPremi;
@endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Jurnal Penerimaan - {{ $cashBank->number }}</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, Helvetica, sans-serif;
      background: #f5f5f5;
      padding: 5px;
      color: #111;
    }

    .container {
      max-width: 850px;
      height: 650px;
      margin: 0 auto;
      background: white;
      padding: 15mm;
      position: relative;
    }

    /* ===== HEADER ===== */
    .header {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 8px;
      margin-bottom: 15px;
    }

    .header .company {
      font-weight: 700;
      font-size: 18px;
      letter-spacing: 0.5px;
    }

    .header .title {
      margin-top: 6px;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 1px;
    }

    /* ===== CONTENT ===== */
    .content {
      min-height: 230mm;
    }

    .box {
      border: 2px solid #000;
      padding: 10px;
      margin-bottom: 12px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    td, th {
      border: 1px solid #000;
      padding: 6px;
      font-size: 13px;
    }

    th {
      text-align: center;
      font-weight: 700;
    }

    .right { text-align: right; }
    .center { text-align: center; }
    .bold { font-weight: 700; }

    /* ===== FOOTER ===== */
    .footer {
      position: fixed;
      bottom: 10mm;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 11px;
      color: #333;
    }

    .no-print {
        position: fixed;
        top: 10px;
        right: 10px;
    }
    @media print {
        .no-print {
            display: none;
        }
    }
  </style>
</head>

<body>
  <div class="no-print">
      <button onclick="window.print()">Print</button>
  </div>

  <div class="container">
  <!-- ===== HEADER ===== -->
  <div class="header">
    <div class="company">PT. BRILLIANT INSURANCE BROKERS</div>
    <div class="title">JURNAL PENERIMAAN</div>
  </div>

  <!-- ===== CONTENT ===== -->
  <div class="content">

    <div class="box">
      <table>
        <tr>
          <td class="bold" width="25%">Tanggal</td>
          <td width="25%">{{ \Carbon\Carbon::parse($cashBank->date)->format('d/m/Y') }}</td>
          <td class="bold" width="15%">No</td>
          <td width="35%">{{ $cashBank->number }}</td>
        </tr>
        <tr>
          <td class="bold">Diterima dari</td>
          <td colspan="3">{{ $cashBank->contact->display_name }}</td>
        </tr>
      </table>
    </div>

    <table>
      <tr>
        <th colspan="3">DEBIT</th>
        <th colspan="3">KREDIT</th>
      </tr>
      <tr>
        <th>Perkiraan</th>
        <th>✓</th>
        <th>Nilai</th>
        <th>Perkiraan</th>
        <th>✓</th>
        <th>Nilai</th>
      </tr>
      {{-- Row 1: Bank (Debit) dan AR/Piutang (Kredit) --}}
      <tr>
        <td>{{ $cashBank->chartOfAccount->display_name ?? '-' }}</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($total, 2, ',', '.') }}</td>
        @if($cashBank->contraAccount)
        <td>{{ $cashBank->contraAccount->display_name }}</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($totalArCredit, 2, ',', '.') }}</td>
        @else
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @endif
      </tr>
      {{-- Row 2: Loss on Collection (Debit) jika ada --}}
      @if($lossOnCollection > 0)
      <tr>
        <td>Loss on Collection</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($lossOnCollection, 2, ',', '.') }}</td>
        @if($gainOnCollection > 0)
        <td>Gain on Collection</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($gainOnCollection, 2, ',', '.') }}</td>
        @else
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @endif
      </tr>
      @elseif($gainOnCollection > 0)
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>Gain on Collection</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($gainOnCollection, 2, ',', '.') }}</td>
      </tr>
      @endif
      {{-- Row 3: Kekurangan Bayar Premi (Debit) jika tidak di-tick dan ada selisih --}}
      @if($kekuranganBayarPremi > 0)
      <tr>
        <td>Kekurangan Bayar Premi</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($kekuranganBayarPremi, 2, ',', '.') }}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      @endif
      <tr>
        <td colspan="2" class="bold center">Jumlah</td>
        <td class="right bold">{{ number_format($totalDebit, 2, ',', '.') }}</td>
        <td colspan="2" class="bold center">Jumlah</td>
        <td class="right bold">{{ number_format($totalCredit, 2, ',', '.') }}</td>
      </tr>
    </table>

    <div class="box">
      <div class="bold">Terbilang :</div>
      {{ TerbilangHelper::terbilang($total, 'Rupiah') }}
    </div>

    <div class="box">
      <div class="bold">Keterangan :</div>
      {{ $allocationDescription }}
    </div>

  </div>

  <!-- ===== FOOTER ===== -->
  <div class="footer">
    PT. BRILLIANT INSURANCE BROKERS • Jurnal Penerimaan • {{ \Carbon\Carbon::parse($cashBank->date)->format('d/m/Y') }}<br/>
    Rukan Botanic Junction, Mega Kebon Jeruk Blok I 10 No. 60, Joglo - Jakarta Barat 11640 Telp.: 021-5890 8403, 2254 2676, 2568 0394 Email : admin@brilliantinsbrokers.com
  </div>
  </div>
</body>
</html>
