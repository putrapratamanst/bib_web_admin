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
                <th>Date</th>
                <th>Due Date</th>
                <th>Debit Note</th>
                <th>Installment</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashouts as $cashout)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $cashout->number }}</td>
                <td>{{ $cashout->insurance->display_name ?? '-' }}</td>
                <td>{{ $cashout->date?->format('d M Y') ?? '-' }}</td>
                <td>{{ $cashout->due_date?->format('d M Y') ?? '-' }}</td>
                <td>{{ $cashout->debitNote->number ?? '-' }}</td>
                <td class="text-center">{{ $cashout->installment_number ?? '-' }}</td>
                <td class="text-end">{{ $cashout->currency_code ?? 'IDR' }} {{ number_format($cashout->amount, 2, ',', '.') }}</td>
                <td>{{ ucfirst($cashout->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
