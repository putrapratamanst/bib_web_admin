@php
use App\Helpers\TerbilangHelper;
$total = $cashBank->amount;

// Calculate Loss/Gain on Forex Different Rate from allocations (yang sudah di-tick)
$lossOnForex = 0;
$gainOnForex = 0;

// Calculate Kekurangan Bayar Premi (yang belum di-tick tapi ada selisih)
$kekuranganBayarPremi = 0;

// Total allocation amount
$totalAllocation = $allocations->sum('allocation');

foreach ($allocations as $allocation) {
    if ($allocation->write_off_type === 'loss') {
        $lossOnForex += floatval($allocation->write_off_amount);
    } elseif ($allocation->write_off_type === 'gain') {
        $gainOnForex += floatval($allocation->write_off_amount);
    } else {
        // write_off_type = 'none' - belum di-tick, hitung kekurangan
        // Get billing amount for this allocation
        $billing = $allocation->debitNoteBilling;
        if ($billing) {
            $billingAmount = floatval($billing->amount);
            
            // Calculate difference (billing - allocated)
            $difference = $billingAmount - floatval($allocation->allocation);
            if ($difference > 0) {
                $kekuranganBayarPremi += $difference;
            }
        }
    }
}

// For payment journal, the logic is reversed from receive journal
// Debit = Contra Account (AP/Hutang/expense)
// Credit = Bank + Gain on Forex

// Total AP to debit = total allocation + gain (karena gain = hutang yang bertambah karena lebih bayar)
$totalApDebit = $totalAllocation + $gainOnForex;

// Total Debit = AP + Loss on Forex + Kekurangan Bayar Premi
$totalDebit = $totalApDebit + $lossOnForex + $kekuranganBayarPremi;

// Total Credit = Bank + Gain on Forex + Kekurangan Bayar Premi
$totalCredit = $total + $gainOnForex + $kekuranganBayarPremi;
@endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Jurnal Pembayaran - {{ $cashBank->number }}</title>
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
    <div class="title">JURNAL PEMBAYARAN</div>
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
          <td class="bold">Dibayar kepada</td>
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
      {{-- Row 1: AP/Hutang (Debit) dan Bank (Kredit) --}}
      <tr>
        @if($cashBank->contraAccount)
        <td>{{ $cashBank->contraAccount->display_name }}</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($totalApDebit, 2, ',', '.') }}</td>
        @else
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @endif
        <td>{{ $cashBank->chartOfAccount->display_name ?? '-' }}</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($total, 2, ',', '.') }}</td>
      </tr>
      {{-- Row 2: Loss on Forex (Debit) atau Gain on Forex (Kredit) --}}
      @if($lossOnForex > 0)
      <tr>
        <td>Loss on Forex Different Rate</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($lossOnForex, 2, ',', '.') }}</td>
        @if($gainOnForex > 0)
        <td>Gain on Forex Different Rate</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($gainOnForex, 2, ',', '.') }}</td>
        @else
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @endif
      </tr>
      @elseif($gainOnForex > 0)
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>Gain on Forex Different Rate</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($gainOnForex, 2, ',', '.') }}</td>
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
    PT. BRILLIANT INSURANCE BROKERS • Jurnal Pembayaran • {{ \Carbon\Carbon::parse($cashBank->date)->format('d/m/Y') }}<br/>
    Rukan Botanic Junction, Mega Kebon Jeruk Blok I 10 No. 60, Joglo - Jakarta Barat 11640 Telp.: 021-5890 8403, 2254 2676, 2568 0394 Email : admin@brilliantinsbrokers.com
  </div>
  </div>
</body>
</html>
