<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PiutangReportExport implements FromView, WithEvents, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('export.report.piutang', [
            'report' => $this->data,
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
                    // 'alignment' => [
                    //     'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    //     'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    // ],
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

                for ($row = 3; $row <= $highestRow; $row++) {
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

                $highestColumn = $event->sheet->getDelegate()->getHighestColumn(); // misal 'B'
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // misal 2

                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cell = Coordinate::stringFromColumnIndex($col) . '1'; // A1, B1, ...
                    $value = $event->sheet->getCell($cell)->getValue();

                    // Set kembali dengan huruf kapital
                    $event->sheet->setCellValue($cell, strtoupper($value));
                    $event->sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }


                // Format kolom H sebagai teks
                $event->sheet->getDelegate()
                    ->getStyle('H:H')
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                // Kalau perlu: ubah semua cell di kolom H agar dianggap teks
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cell = 'H' . $row;
                    $value = $event->sheet->getCell($cell)->getValue();
                    // Tambahkan tanda kutip supaya dianggap teks
                    $event->sheet->setCellValueExplicit($cell, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
            }
        ];
    }
}
