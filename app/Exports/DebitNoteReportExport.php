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
    protected $approvalStatus;
    protected $contractTypeId;
    protected $currencyCode;
    protected $isPosted;

    public function __construct($dateFrom = null, $dateTo = null, $contactId = null, $status = null, $approvalStatus = null, $contractTypeId = null, $currencyCode = null, $isPosted = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->contactId = $contactId;
        $this->status = $status;
        $this->approvalStatus = $approvalStatus;
        $this->contractTypeId = $contractTypeId;
        $this->currencyCode = $currencyCode;
        $this->isPosted = $isPosted;
    }

    public function collection()
    {
        // Load debit notes with related billings (relation must exist on the model)
        $debitNotes = DebitNote::with(['contact', 'contract', 'creditNotes', 'paymentAllocations.cashBank.chartOfAccount', 'billings.paymentAllocations.cashBank.chartOfAccount'])
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
            ->when($this->approvalStatus, function ($q) {
                $q->where('approval_status', $this->approvalStatus);
            })
            ->when($this->contractTypeId, function ($q) {
                $q->whereHas('contract', function ($q) {
                    $q->where('contract_type_id', $this->contractTypeId);
                });
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

            if ($dn->relationLoaded('billings') && $dn->billings->count()) {
                foreach ($dn->billings as $billing) {
                    // Get payment allocations directly from billing relation
                    $paymentForBilling = $billing->paymentAllocations->sum('allocation');

                    // Get bank info from payment allocations for this billing
                    $bankInfo = $this->getBankInfoForBilling($billing);

                    $rows->push((object)[
                        'debit_note' => $dn,
                        'billing' => $billing,
                        'credit_notes_amount' => $creditNotesAmount,
                        'payment_allocations_amount' => $paymentForBilling,
                        'bank_name' => $bankInfo['bank_name'],
                        'bank_transaction_number' => $bankInfo['bank_transaction_number'],
                        'bank_date' => $bankInfo['bank_date'],
                    ]);
                }
            } else {
                // No billing rows: treat allocation as total allocated on the debit note (unlinked allocations)
                $totalAllocation = $dn->paymentAllocations->sum('allocation');

                // Get bank info from payment allocations
                $bankInfo = $this->getBankInfoFromAllocations($dn->paymentAllocations);

                $rows->push((object)[
                    'debit_note' => $dn,
                    'billing' => null,
                    'credit_notes_amount' => $creditNotesAmount,
                    'payment_allocations_amount' => $totalAllocation,
                    'bank_name' => $bankInfo['bank_name'],
                    'bank_transaction_number' => $bankInfo['bank_transaction_number'],
                    'bank_date' => $bankInfo['bank_date'],
                ]);
            }
        }

        // Filter out rows with outstanding = 0
        $rows = $rows->filter(function ($row) {
            $amount = $row->billing ? $row->billing->amount : $row->debit_note->amount;
            $outstanding = $amount - $row->credit_notes_amount - $row->payment_allocations_amount;
            return $outstanding != 0;
        });

        return $rows;
    }

    private function getBankInfoForBilling($billing): array
    {
        $bankName = '';
        $bankTransactionNumber = '';
        $bankDate = null;

        if ($billing->relationLoaded('paymentAllocations') && $billing->paymentAllocations->count()) {
            foreach ($billing->paymentAllocations as $allocation) {
                if ($allocation->cashBank) {
                    $bankName = $allocation->cashBank->chartOfAccount->name ?? '';
                    $bankTransactionNumber = $allocation->cashBank->number ?? '';
                    $bankDate = $allocation->cashBank->date;
                    break;
                }
            }
        }

        return [
            'bank_name' => $bankName,
            'bank_transaction_number' => $bankTransactionNumber,
            'bank_date' => $bankDate,
        ];
    }

    private function getBankInfoFromAllocations($allocations): array
    {
        $bankName = '';
        $bankTransactionNumber = '';
        $bankDate = null;

        foreach ($allocations as $allocation) {
            if ($allocation->cashBank) {
                $bankName = $allocation->cashBank->chartOfAccount->name ?? '';
                $bankTransactionNumber = $allocation->cashBank->number ?? '';
                $bankDate = $allocation->cashBank->date;
                break;
            }
        }

        return [
            'bank_name' => $bankName,
            'bank_transaction_number' => $bankTransactionNumber,
            'bank_date' => $bankDate,
        ];
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
            'Bank',
            'No. Trans. Bank',
            'Tgl Bank',
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

        $creditNotesAmount = $row->credit_notes_amount;
        $paymentAllocationsAmount = $row->payment_allocations_amount;


        $creditApplied = $creditNotesAmount;
        $paymentApplied =  $paymentAllocationsAmount;

        // Outstanding = Amount - Credit Notes - Payment Allocations
        $outstandingAmount = $amount - $creditApplied - $paymentApplied;

        // Convert to IDR if currency is not IDR
        $amountInIdr = $debitNote->currency_code === 'IDR'
            ? $amount
            : $amount * $debitNote->exchange_rate;

        return [
            $debitNote->number,
            $billing ? ($billing->billing_number ?? $billing->id ?? '') : '',
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
            $row->bank_name ?? '',
            $row->bank_transaction_number ?? '',
            $row->bank_date ? Carbon::parse($row->bank_date)->format('d/m/Y') : '',
            $debitNote->created_at ? $debitNote->created_at->format('d/m/Y H:i') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Auto-size columns A..T (20 columns)
        foreach (range('A', 'T') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Style header row
        $sheet->getStyle('A1:T1')->applyFromArray([
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
        $sheet->getStyle('A1:T' . $lastRow)->applyFromArray([
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
