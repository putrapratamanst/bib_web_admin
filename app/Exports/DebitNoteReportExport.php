<?php

namespace App\Exports;

use App\Models\DebitNote;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class DebitNoteReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $dateFrom;
    protected $dateTo;
    protected $contactId;
    protected $status;
    protected $currencyCode;
    protected $isPosted;

    public function __construct($dateFrom = null, $dateTo = null, $contactId = null, $status = null, $currencyCode = null, $isPosted = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->contactId = $contactId;
        $this->status = $status;
        $this->currencyCode = $currencyCode;
        $this->isPosted = $isPosted;
    }

    public function collection()
    {
        return DebitNote::with(['contact', 'contract', 'creditNotes', 'paymentAllocations'])
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('date', '<=', $this->dateTo);
            })
            ->when($this->contactId, function ($q) {
                $q->where('contact_id', $this->contactId);
            })
            ->when($this->status, function ($q) {
                $q->where('status', $this->status);
            })
            ->when($this->currencyCode, function ($q) {
                $q->where('currency_code', $this->currencyCode);
            })
            ->when($this->isPosted !== null && $this->isPosted !== '', function ($q) {
                $q->where('is_posted', $this->isPosted);
            })
            ->orderBy('date', 'desc')
            ->orderBy('number', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'DN Number',
            'Contract Number',
            'Contact',
            'Date',
            'Due Date',
            'Currency',
            'Exchange Rate',
            'Amount',
            'Amount (IDR)',
            'Installment',
            'Status',
            'Posted',
            'Outstanding Amount',
            'Credit Notes',
            'Payment Allocations',
            'Created Date'
        ];
    }

    public function map($debitNote): array
    {
        // Calculate outstanding amount
        $creditNotesAmount = $debitNote->creditNotes->sum('amount');
        $paymentAllocationsAmount = $debitNote->paymentAllocations->sum('amount');
        $outstandingAmount = $debitNote->amount - $creditNotesAmount - $paymentAllocationsAmount;

        // Convert to IDR if currency is not IDR
        $amountInIdr = $debitNote->currency_code === 'IDR' 
            ? $debitNote->amount 
            : $debitNote->amount * $debitNote->exchange_rate;

        return [
            $debitNote->number,
            $debitNote->contract ? $debitNote->contract->number : '',
            $debitNote->contact ? $debitNote->contact->display_name : '',
            $debitNote->date ? Carbon::parse($debitNote->date)->format('d/m/Y') : '',
            $debitNote->due_date ? Carbon::parse($debitNote->due_date)->format('d/m/Y') : '',
            $debitNote->currency_code,
            number_format($debitNote->exchange_rate, 2, ',', '.'),
            number_format($debitNote->amount, 2, ',', '.'),
            number_format($amountInIdr, 2, ',', '.'),
            $debitNote->installment,
            ucfirst($debitNote->status),
            $debitNote->is_posted ? 'Yes' : 'No',
            number_format($outstandingAmount, 2, ',', '.'),
            number_format($creditNotesAmount, 2, ',', '.'),
            number_format($paymentAllocationsAmount, 2, ',', '.'),
            $debitNote->created_at ? $debitNote->created_at->format('d/m/Y H:i') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Auto-size columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style header row
        $sheet->getStyle('A1:P1')->applyFromArray([
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
        $sheet->getStyle('A1:P' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

        // Right align numeric columns
        $sheet->getStyle('G2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('M2:O' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    public function title(): string
    {
        $period = '';
        if ($this->dateFrom && $this->dateTo) {
            $period = ' (' . Carbon::parse($this->dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($this->dateTo)->format('d/m/Y') . ')';
        }
        
        return 'Debit Note Report' . $period;
    }
}