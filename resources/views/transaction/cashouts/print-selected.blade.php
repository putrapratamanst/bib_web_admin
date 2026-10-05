<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hutang Asuransi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        th, td { border: 1px solid #333; padding: 7px; }
        th { background: #eee; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .nowrap { white-space: nowrap; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <button class="no-print" type="button" onclick="window.print()">Print</button>
    <h2>DAFTAR HUTANG ASURANSI</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nomor Hutang</th>
                <th>Insurance</th>
                <th>Nomor Polis</th>
                <th>Nomor DN</th>
                <th>Nominal Premi</th>
                <th>Nominal Share Premi</th>
                <th>Nominal Basos</th>
                <th>Nominal Diskon</th>
                <th>Nominal Komisi</th>
                <th>PPh</th>
                <th>Biaya Polis</th>
                <th>To Chubb</th>
                <th>Nama Tertanggung</th>
                <th>Jenis Asuransi</th>
                <th>Date</th>
                <th>Due Date</th>
                <th>Installment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashouts as $cashout)
            @php
                $contract = $cashout->debitNote?->contract;
                $contractDetail = $contract?->details?->firstWhere('insurance_id', $cashout->insurance_id);
                $currency = $cashout->currency_code ?? 'IDR';
                $grossPremium = (float) ($contract?->gross_premium ?? 0);
                $percentage = (float) ($contractDetail?->percentage ?? 0);
                $sharePremium = $grossPremium * $percentage / 100;
                $basos = $sharePremium * (float) ($contractDetail?->eng_fee ?? 0) / 100;
                $discount = (float) ($contract?->discount_amount ?? 0) * $percentage / 100;
                $commission = $sharePremium * (float) ($contractDetail?->brokerage_fee ?? 0) / 100;
                $policyCost = $cashout->installment_number == 1
                    ? (float) ($contract?->policy_fee ?? 0)
                        + (float) ($contract?->stamp_fee ?? 0) * $percentage / 100
                    : 0;
                $insuredName = $cashout->debitNote?->billingAddress?->name
                    ?: ($contract?->contact?->display_name ?? '-');
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $cashout->number }}</td>
                <td>{{ $cashout->insurance->display_name ?? '-' }}</td>
                <td>{{ $contract?->policy_number ?: ($contract?->cover_note_number ?? '-') }}</td>
                <td>{{ $cashout->debitNote->number ?? '-' }}</td>
                <td class="text-end nowrap">{{ $currency }} {{ number_format($grossPremium, 2, ',', '.') }}</td>
                <td class="text-end nowrap">{{ $currency }} {{ number_format($sharePremium, 2, ',', '.') }}</td>
                <td class="text-end nowrap">{{ $currency }} {{ number_format($basos, 2, ',', '.') }}</td>
                <td class="text-end nowrap">{{ $currency }} {{ number_format($discount, 2, ',', '.') }}</td>
                <td class="text-end nowrap">{{ $currency }} {{ number_format($commission, 2, ',', '.') }}</td>
                <td class="text-end nowrap">{{ $currency }} {{ number_format($commission * 2 / 100, 2, ',', '.') }}</td>
                <td class="text-end nowrap">{{ $currency }} {{ number_format($policyCost, 2, ',', '.') }}</td>
                <td class="text-end nowrap">{{ $currency }} {{ number_format($cashout->amount, 2, ',', '.') }}</td>
                <td>{{ $insuredName }}</td>
                <td>{{ $contract?->contractType?->name ?? '-' }}</td>
                <td>{{ $cashout->date?->format('d M Y') ?? '-' }}</td>
                <td>{{ $cashout->due_date?->format('d M Y') ?? '-' }}</td>
                <td class="text-center">{{ $cashout->installment_number ?? '-' }}</td>
                <td>{{ ucfirst($cashout->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
