<div class="container">
    <link rel="stylesheet" href="{{ asset("assets/bootstrap/css/bootstrap.min.css") }}" />
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-striped">
            <thead>
                <tr>
                    <th>Billing No</th>
                    <th>Billing Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Credit Note</th>
                    <th>Allocation</th>
                    <th>Outstanding</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report as $row)
                <tr class="{{ $row['row_type'] === 'billing' ? 'table-light' : '' }}">
                    <td>{{ $row['billing_no'] }}</td>
                    <td>{{ $row['billing_date'] }}</td>
                    <td>{{ $row['billing_due'] ?? $row['due_date'] }}</td>
                    <td {!! ($row['billing_is_overdue'] ?? $row['is_overdue']) ? 'style="color: red;"' : '' !!}>
                        @php
                            $days = $row['billing_days_until_due'] ?? $row['days_until_due'] ?? null;
                            if ($days < 0) {
                                echo floor(abs($days)) . ' days overdue';
                            } elseif ($days == 0) {
                                echo 'Due today';
                            } else {
                                echo floor($days) . ' days left';
                            }
                        @endphp
                    </td>
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