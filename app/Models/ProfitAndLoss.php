<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitAndLoss extends Model
{
    // Jika ingin menggunakan table 'profit_and_losses'
    // protected $table = 'profit_and_losses';

    // Jika menggunakan database nanti, atur fillable field di sini
    protected $fillable = [
        'urutan', 'uraian', 'kode', 'rincian', 'tipe', 'amount'
    ];

    // Data sementara (hardcoded) untuk sekarang
    public static function getData()
    {
        return [
            ['Urutan', 'Uraian', 'Kode', 'Rincian', 'Tipe', 'Amount'],
            [1, "PENDAPATAN", "", "", "P", ""],
            [2, "Pendapatan Jasa Keperantaraan", "4100", "LR01", "T", ""],
            [3, "Pendapatan Jasa Keperantaraan Langsung", "4110", "", "D", ""],
            [4, "-/- Bagian Pendapatan Jasa Keperantaaran", "4200", "LR01", "D", ""],
            [5, "Pendapatan Jasa Keperantaraan Tidak", "4300", "LR01", "D", ""],
            [6, "Pendapatan Jasa Konsultasi", "4400", "LR01", "D", ""],
            [7, "Pendapatan Jasa Penanganan Klaim", "4900", "", "D", ""],
            [8, "Pendapatan Lainnya", "4004", "", "D", ""],
            [9, "Jumlah Pendapatan", "", "", "T", ""],
            [10, "BEBAN", "", "", "P", ""],
            [11, "Beban Operasional", "6000", "", "T", ""],
            [12, "Beban Pegawai dan Pengurus", "6001", "LR04", "D", ""],
            [13, "Beban Pendidikan dan Latihan", "6002", "LR05", "D", ""],
            [14, "Beban Pemasaran", "6009", "", "D", ""],
            [15, "Beban Komisi", "6025", "LR06", "D", ""],
            [16, "Beban Operasional Lain", "6020", "LR07", "D", ""],
            [17, "Beban Non Operasional", "6200", "LR08", "D", ""],
            [18, "Jumlah Beban", "5000", "", "T", ""],
            [19, "Laba (Rugi) Sebelum Pajak", "6100", "", "T", ""],
            [20, "Beban Pajak", "5600", "", "D", ""],
            [21, "Laba (Rugi) Setelah Pajak", "6250", "", "T", ""],
            [22, "Pendapatan (Beban) Komprehensif", "", "", "T", ""],
            [23, "Kenaikan (Penurunan) Penilaian Aset Tetap", "5710", "", "D", ""],
            [24, "Keuntungan (Kerugian) Mata Uang Asing dan Lainnya", "5720", "", "D", ""],
            [25, "Total Pendapatan (Beban) Komprehensif", "5700", "", "D", ""],
            [26, "Laba (Rugi) Komprehensif", "6300", "", "T", ""],
        ];
    }
}
