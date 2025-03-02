<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitAndLossExport implements FromArray, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data->toArray();
    }

    public function headings(): array
    {
        return [
            'Urutan',
            'Uraian',
            'Kode',
            'Rincian',
            'Tipe',
            'Amount'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Atur border untuk seluruh tabel
        $sheet->getStyle('A1:F32')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Atur heading tebal
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        return [];
    }
}
