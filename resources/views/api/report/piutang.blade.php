<div class="container">
    <link rel="stylesheet" href="{{ asset("assets/bootstrap/css/bootstrap.min.css") }}" />
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-striped">
            <thead>
                <tr>
                    <th>Billing No</th>
                    <th>Billing Date</th>
                    <th>Amount</th>
                    <th>Credit Note</th>
                    <th>Allocation</th>
                    <th>Outstanding</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report as $row)
                <tr>
                    <td>{{ $row['billing_no'] }}</td>
                    <td>{{ $row['billing_date'] }}</td>
                    <td style="text-align:right">{{ number_format($row['amount'], 2) }}</td>
                    <td style="text-align:right">{{ number_format($row['credit_note'], 2) }}</td>
                    <td style="text-align:right">{{ number_format($row['allocation'], 2) }}</td>
                    <td style="text-align:right">{{ number_format($row['outstanding'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>