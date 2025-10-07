<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class CashoutReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $cashouts;
    protected $summary;

    public function __construct($cashouts, $summary)
    {
        $this->cashouts = $cashouts;
        $this->summary = $summary;
    }

    public function collection()
    {
        return collect($this->cashouts);
    }

    public function headings(): array
    {
        return [
            'Cashout Number',
            'Date',
            'Due Date',
            'Debit Note',
            'Contract',
            'Client',
            'Insurance Company',
            'Currency',
            'Amount',
            'Status',
            'Description',
            'Created',
            'Updated'
        ];
    }

    public function map($cashout): array
    {
        return [
            $cashout['cashout_number'],
            $cashout['cashout_date'],
            $cashout['due_date'],
            $cashout['debit_note_number'],
            $cashout['contract_number'],
            $cashout['client_name'],
            $cashout['insurance_name'],
            $cashout['currency_code'],
            $cashout['amount'],
            $cashout['status'],
            $cashout['description'],
            $cashout['created_at'],
            $cashout['updated_at']
        ];
    }

    public function title(): string
    {
        return 'Cashout Report';
    }
}
