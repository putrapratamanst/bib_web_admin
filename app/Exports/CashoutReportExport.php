<?php

namespace App\Exports;

use App\Models\Cashout;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class CashoutReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $dateFrom;
    protected $dateTo;
    protected $insuranceId;
    protected $status;
    protected $currencyCode;
    protected $contractTypeId;

    public function __construct($dateFrom = null, $dateTo = null, $insuranceId = null, $status = null, $currencyCode = null, $contractTypeId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->insuranceId = $insuranceId;
        $this->status = $status;
        $this->currencyCode = $currencyCode;
        $this->contractTypeId = $contractTypeId;
    }

    public function collection()
    {
        return Cashout::with(['debitNote.contract.contact', 'debitNote.contract.contractType', 'debitNoteBilling', 'insurance'])
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('date', '<=', $this->dateTo);
            })
            ->when($this->insuranceId, function ($q) {
                $q->where('insurance_id', $this->insuranceId);
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->currencyCode, function ($q) {
                $q->where('currency_code', $this->currencyCode);
            })
            ->when($this->contractTypeId, function ($q) {
                $q->whereHas('debitNote.contract', function ($q) {
                    $q->where('contract_type_id', $this->contractTypeId);
                });
            })
            ->orderBy('date', 'desc')
            ->orderBy('number', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Cashout Number',
            'Billing Number',
            'Debit Note Number',
            'Contract Number',
            'Policy Number',
            'Contract Type',
            'Client',
            'Insurance Company',
            'Date',
            'Due Date',
            'Currency',
            'Exchange Rate',
            'Amount',
            'Amount (IDR)',
            'Installment',
            'Status',
            'Description',
            'Created Date'
        ];
    }

    public function map($cashout): array
    {
        // Convert to IDR if currency is not IDR
        $amountInIdr = $cashout->currency_code === 'IDR'
            ? $cashout->amount
            : $cashout->amount * $cashout->exchange_rate;

        return [
            $cashout->number,
            $cashout->debitNoteBilling ? ($cashout->debitNoteBilling->billing_number ?? $cashout->debitNoteBilling->id ?? '') : '',
            $cashout->debitNote ? $cashout->debitNote->number : '',
            $cashout->debitNote && $cashout->debitNote->contract ? $cashout->debitNote->contract->number : '',
            $cashout->debitNote && $cashout->debitNote->contract ? ($cashout->debitNote->contract->policy_number ?? '') : '',
            $cashout->debitNote && $cashout->debitNote->contract && $cashout->debitNote->contract->contractType ? $cashout->debitNote->contract->contractType->name : '',
            $cashout->debitNote && $cashout->debitNote->contract && $cashout->debitNote->contract->contact ? $cashout->debitNote->contract->contact->display_name : '',
            $cashout->insurance ? $cashout->insurance->display_name : '',
            $cashout->date ? Carbon::parse($cashout->date)->format('d/m/Y') : '',
            $cashout->due_date ? Carbon::parse($cashout->due_date)->format('d/m/Y') : '',
            $cashout->currency_code,
            number_format($cashout->exchange_rate, 2, ',', '.'),
            number_format($cashout->amount, 2, ',', '.'),
            number_format($amountInIdr, 2, ',', '.'),
            $cashout->installment_number ?? '',
            ucfirst($cashout->status),
            $cashout->description ?? '',
            $cashout->created_at ? $cashout->created_at->format('d/m/Y H:i') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Auto-size columns A..R (18 columns)
        foreach (range('A', 'R') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style header row
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Add borders to all data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:R' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

        // Right align numeric columns:
        // Exchange Rate (L), Amount (M), Amount(IDR) (N)
        $sheet->getStyle('L2:N' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    public function title(): string
    {
        $period = '';
        if ($this->dateFrom && $this->dateTo) {
            $period = ' (' . Carbon::parse($this->dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($this->dateTo)->format('d/m/Y') . ')';
        }

        return 'Cashout Report' . $period;
    }
}
