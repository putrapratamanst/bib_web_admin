<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class CashoutReconciliationExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $reconciliation;
    protected $asOfDate;

    public function __construct($reconciliation, $asOfDate)
    {
        $this->reconciliation = $reconciliation;
        $this->asOfDate = $asOfDate;
    }

    public function collection()
    {
        return collect($this->reconciliation);
    }

    public function headings(): array
    {
        return [
            'Insurance Company',
            'Total Cashouts',
            'Total Amount',
            'Pending Count',
            'Pending Amount',
            'Paid Count',
            'Paid Amount',
            'Cancelled Count',
            'Cancelled Amount',
            'Outstanding Amount'
        ];
    }

    public function map($item): array
    {
        return [
            $item['insurance_name'],
            $item['total_cashouts'],
            $item['total_amount'],
            $item['pending_count'],
            $item['pending_amount'],
            $item['paid_count'],
            $item['paid_amount'],
            $item['cancelled_count'],
            $item['cancelled_amount'],
            $item['outstanding_amount']
        ];
    }

    public function title(): string
    {
        return 'Cashout Reconciliation - ' . $this->asOfDate;
    }
}
