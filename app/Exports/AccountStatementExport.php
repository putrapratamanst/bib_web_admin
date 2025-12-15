<?php

namespace App\Exports;

use App\Models\AccountStatement;
use App\Models\ChartOfAccount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class AccountStatementExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $chartOfAccountId;
    protected $dateFrom;
    protected $dateTo;
    protected $statement;
    protected $chartOfAccount;

    public function __construct($chartOfAccountId, $dateFrom = null, $dateTo = null)
    {
        $this->chartOfAccountId = $chartOfAccountId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->statement = AccountStatement::buildStatement($chartOfAccountId, $dateFrom, $dateTo);
        $this->chartOfAccount = ChartOfAccount::find($chartOfAccountId);
    }

    public function collection()
    {
        return $this->statement['transactions'];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Transaction Type',
            'Reference',
            'Description',
            'Debit',
            'Credit',
            'Balance'
        ];
    }

    public function map($row): array
    {
        return [
            Carbon::parse($row->date)->format('d/m/Y'),
            $row->transaction_type,
            $row->reference,
            $row->description,
            number_format($row->debit, 2, ',', '.'),
            number_format($row->credit, 2, ',', '.'),
            number_format($row->balance, 2, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Auto-size columns
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        $period = '';
        if ($this->dateFrom && $this->dateTo) {
            $period = ' (' . Carbon::parse($this->dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($this->dateTo)->format('d/m/Y') . ')';
        }

        return 'Account Statement' . $period;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add title rows
                $sheet->insertNewRowBefore(1, 4);

                // Title
                $accountName = $this->chartOfAccount ? $this->chartOfAccount->display_name : '';
                $sheet->setCellValue('A1', 'ACCOUNT STATEMENT / BUKU BANK');
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Account name
                $sheet->setCellValue('A2', 'Account: ' . $accountName);
                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ]);

                // Period
                $periodText = 'Period: ';
                if ($this->dateFrom && $this->dateTo) {
                    $periodText .= Carbon::parse($this->dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($this->dateTo)->format('d/m/Y');
                } else if ($this->dateFrom) {
                    $periodText .= 'From ' . Carbon::parse($this->dateFrom)->format('d/m/Y');
                } else if ($this->dateTo) {
                    $periodText .= 'Until ' . Carbon::parse($this->dateTo)->format('d/m/Y');
                } else {
                    $periodText .= 'All Period';
                }

                $sheet->setCellValue('A3', $periodText);
                $sheet->mergeCells('A3:G3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ]);

                // Opening balance
                $sheet->setCellValue('A4', 'Opening Balance');
                $sheet->setCellValue('G4', number_format($this->statement['opening_balance'], 2, ',', '.'));
                $sheet->getStyle('A4:G4')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F4F8']
                    ]
                ]);
                $sheet->getStyle('G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Style header row (now row 5)
                $sheet->getStyle('A5:G5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
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
                $sheet->getStyle('A5:G' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);

                // Right align numeric columns (E, F, G = Debit, Credit, Balance)
                $sheet->getStyle('E6:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Add summary rows
                $summaryRow = $lastRow + 1;
                $sheet->setCellValue('A' . $summaryRow, 'TOTAL');
                $sheet->setCellValue('E' . $summaryRow, number_format($this->statement['total_debit'], 2, ',', '.'));
                $sheet->setCellValue('F' . $summaryRow, number_format($this->statement['total_credit'], 2, ',', '.'));
                $sheet->mergeCells('A' . $summaryRow . ':D' . $summaryRow);

                $closingRow = $summaryRow + 1;
                $sheet->setCellValue('A' . $closingRow, 'CLOSING BALANCE');
                $sheet->setCellValue('G' . $closingRow, number_format($this->statement['closing_balance'], 2, ',', '.'));
                $sheet->mergeCells('A' . $closingRow . ':F' . $closingRow);

                // Style summary rows
                $sheet->getStyle('A' . $summaryRow . ':G' . $closingRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F4F8']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                $sheet->getStyle('E' . $summaryRow . ':G' . $closingRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        ];
    }
}
