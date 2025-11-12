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
        // Load debit notes with related billings (relation must exist on the model)
        $debitNotes = DebitNote::with(['contact', 'contract', 'creditNotes', 'paymentAllocations', 'billings'])
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

        $rows = collect();

        foreach ($debitNotes as $dn) {
            $creditNotesAmount = $dn->creditNotes->sum('amount');
            $paymentAllocationsAmount = $dn->paymentAllocations->sum('amount');

            if ($dn->relationLoaded('billings') && $dn->billings->count()) {
                foreach ($dn->billings as $billing) {
                    $rows->push((object)[
                        'debit_note' => $dn,
                        'billing' => $billing,
                        'credit_notes_amount' => $creditNotesAmount,
                        'payment_allocations_amount' => $paymentAllocationsAmount,
                    ]);
                }
            } else {
                // Fallback to a single row representing the whole debit note (keeps existing behaviour)
                $rows->push((object)[
                    'debit_note' => $dn,
                    'billing' => null,
                    'credit_notes_amount' => $creditNotesAmount,
                    'payment_allocations_amount' => $paymentAllocationsAmount,
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'DN Number',
            'Billing Number',
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

    public function map($row): array
    {
        // $row is an object with keys: debit_note, billing, credit_notes_amount, payment_allocations_amount
        $debitNote = $row->debit_note;
        $billing = $row->billing;

        // Determine amounts: use billing amount when available, otherwise use debit note total
        $amount = $billing ? $billing->amount : $debitNote->amount;

        // Pro-rate credit notes and payment allocations to billing when billing exists
        $creditNotesAmount = $row->credit_notes_amount;
        $paymentAllocationsAmount = $row->payment_allocations_amount;

        $proportion = 0;
        if ($billing && $debitNote->amount > 0) {
            $proportion = $amount / $debitNote->amount;
        }

        $creditApplied = $billing ? round($creditNotesAmount * $proportion, 2) : $creditNotesAmount;
        $paymentApplied = $billing ? round($paymentAllocationsAmount * $proportion, 2) : $paymentAllocationsAmount;

        $outstandingAmount = $amount - $creditApplied - $paymentApplied;

        // Convert to IDR if currency is not IDR
        $amountInIdr = $debitNote->currency_code === 'IDR'
            ? $amount
            : $amount * $debitNote->exchange_rate;

        return [
            $debitNote->number,
            $billing ? ($billing->number ?? $billing->id ?? '') : '',
            $debitNote->contract ? $debitNote->contract->number : '',
            $debitNote->contact ? $debitNote->contact->display_name : '',
            $debitNote->date ? Carbon::parse($debitNote->date)->format('d/m/Y') : '',
            $debitNote->due_date ? Carbon::parse($debitNote->due_date)->format('d/m/Y') : '',
            $debitNote->currency_code,
            number_format($debitNote->exchange_rate, 2, ',', '.'),
            number_format($amount, 2, ',', '.'),
            number_format($amountInIdr, 2, ',', '.'),
            $debitNote->installment,
            ucfirst($debitNote->status),
            $debitNote->is_posted ? 'Yes' : 'No',
            number_format($outstandingAmount, 2, ',', '.'),
            number_format($creditApplied, 2, ',', '.'),
            number_format($paymentApplied, 2, ',', '.'),
            $debitNote->created_at ? $debitNote->created_at->format('d/m/Y H:i') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Auto-size columns A..Q (17 columns)
        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style header row
        $sheet->getStyle('A1:Q1')->applyFromArray([
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
    $sheet->getStyle('A1:Q' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

    // Right align numeric columns:
    // Exchange Rate (H), Amount (I), Amount(IDR) (J) and Outstanding (N), Credit (O), Payment (P)
    $sheet->getStyle('H2:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('N2:P' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

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