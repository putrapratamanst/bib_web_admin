@php
use App\Helpers\TerbilangHelper;
$total = $cashBank->amount;
@endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Jurnal Penerimaan - {{ $cashBank->number }}</title>
  <style>
    @page {
      size: A4;
      margin: 20mm 15mm 25mm 15mm;
    }

    body {
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;
      color: #111;
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
      @php
        $details = $cashBank->cashBankDetails;
        $maxRows = max(count($details), 1);
      @endphp
      @for($i = 0; $i < $maxRows; $i++)
      <tr>
        @if($i == 0)
        <td>{{ $cashBank->chartOfAccount->display_name }}</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($total, 2, ',', '.') }}</td>
        @else
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @endif
        @if(isset($details[$i]))
        <td>{{ $details[$i]->debitNote ? $details[$i]->debitNote->number : 'Penerimaan' }}</td>
        <td class="center">✓</td>
        <td class="right">{{ number_format($details[$i]->amount, 2, ',', '.') }}</td>
        @else
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        @endif
      </tr>
      @endfor
      <tr>
        <td colspan="2" class="bold center">Jumlah</td>
        <td class="right bold">{{ number_format($total, 2, ',', '.') }}</td>
        <td colspan="2" class="bold center">Jumlah</td>
        <td class="right bold">{{ number_format($total, 2, ',', '.') }}</td>
      </tr>
    </table>

    <div class="box">
      <div class="bold">Terbilang :</div>
      {{ TerbilangHelper::terbilang($total, 'Rupiah') }}
    </div>

    <div class="box">
      <div class="bold">Keterangan :</div>
      {{ $cashBank->description }}
    </div>

  </div>

  <!-- ===== FOOTER ===== -->
  <div class="footer">
    PT. BRILLIANT INSURANCE BROKERS • Jurnal Penerimaan • {{ \Carbon\Carbon::parse($cashBank->date)->format('d/m/Y') }}<br/>
    Rukan Botanic Junction, Mega Kebon Jeruk Blok I 10 No. 60, Joglo - Jakarta Barat 11640 Telp.: 021-5890 8403, 2254 2676, 2568 0394 Email : admin@brilliantinsbrokers.com
  </div>
</body>
</html>

