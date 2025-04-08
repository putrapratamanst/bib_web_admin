<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ConsoleReportExport implements FromView, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('export.report.console', [
            'data' => $this->data,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                // Set font and alignment
                $sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');
                $sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setSize(12);
                $sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                // $sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setIndent(1);
                // $sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setTextRotation(0);
                $sheet->getDelegate()->getParent()->getDefaultStyle()->getAlignment()->setWrapText(true);


                // Get the highest column in use
                $highestColumn = $sheet->getHighestColumn();

                // Apply borders to all cells
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Apply bold font to the first row (A1) and set alignment to center
                $sheet->getStyle("1:2")->applyFromArray([
                    'font' => [
                        'bold' => true, // Set font bold
                    ],
                ]);

                $sheet->getStyle("B1:C{$highestRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'FFFF00'], // Yellow color
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Teks di tengah secara horizontal
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Teks di tengah secara vertikal
                    ],
                ]);

                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellB = "B{$row}";
                    $cellC = "C{$row}";

                    $valueB = $sheet->getCell($cellB)->getValue();
                    $valueC = $sheet->getCell($cellC)->getValue();

                    // Set nilai baru yang sudah diubah menjadi kapital
                    if (!empty($valueB)) {
                        $sheet->setCellValue($cellB, strtoupper($valueB));
                    }

                    if (!empty($valueC)) {
                        $sheet->setCellValue($cellC, strtoupper($valueC));
                    }
                }
            }
        ];
    }
}
