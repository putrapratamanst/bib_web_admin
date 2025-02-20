<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    // Jika ingin menggunakan table 'profit_and_losses'
    // protected $table = 'profit_and_losses';

    // Jika menggunakan database nanti, atur fillable field di sini
    protected $fillable = [
        'no',
        'description',
        'tipe',
        'amount'
    ];

    // Data sementara (hardcoded) untuk sekarang
    public static function getData()
    {
        return [
            ['No', 'Description', 'Tipe', 'Amount'],
            [1, "Saldo Awal Kas-Bank", "T", ""],
            [2, "Arus Kas dari Aktivitas Operasi", "H", ""],
            [3, "Arus Kas Masuk untuk Aktivitas Operasi", "T", ""],
            [4, "Arus Kas Masuk dari Premi (Netto)", "D", ""],
            [5, "Arus Kas Masuk dari Pendapatan Jasa Keperantaraan", "D", ""],
            [6, "Arus Kas Masuk dari Pendapatan Jasa Keperantaraan Tidak Langsung", "D", ""],
            [7, "Arus Kas masuk dari Jasa Konsultasi", "D", ""],
            [8, "Arus Kas Masuk dari Pendapatan Jasa Penanganan Klaim", "D", ""],
            [9, "Arus Kas Masuk dari Klaim Perusahaan Asuransi", "D", ""],
            [10, "Arus Kas Masuk dari Pendapatan Aktivitas Operasi Lainnya", "D", ""],
            [11, "Arus Kas Keluar untuk Aktivitas Operasi", "T", ""],
            [12, "Arus Kas Keluar untuk Premi ke Perusahaan Asuransi", "D", ""],
            [13, "Arus Kas Keluar untuk Pengembalian Premi ke Tertanggung", "D", ""],
            [14, "Arus Kas Keluar untuk Beban Pegawai dan Pengurus", "D", ""],
            [15, "Arus Kas Keluar untuk Beban Pendidikan", "D", ""],
            [16, "Arus Kas Keluar untuk Beban Pemasaran", "D", ""],
            [17, "Arus Kas Keluar untuk Beban Komisi", "D", ""],
            [18, "Arus Kas Keluar untuk Klaim Kepada Tertanggung", "D", ""],
            [19, "Arus Kas Keluar untuk Pembayaran Aktivitas Operasi Lainnya", "D", ""],
            [20, "Jumlah Arus Kas dari Aktivitas Operasi", "T", ""],
            [21, "Arus Kas dari Aktivitas Investasi", "H", ""],
            [22, "Arus Kas Masuk dari Aktivitas Investasi", "T", ""],
            [23, "Arus Kas Masuk dari Penerimaan Hasil investasi", "D", ""],
            [24, "Arus Kas Masuk dari Pencairan Investasi", "D", ""],
            [25, "Arus Kas Masuk dari Penjualan Aset", "D", ""],
            [26, "Arus Kas Masuk dari Aktivitas Investasi Lainnya", "D", ""],
            [27, "Arus Kas Keluar untuk Aktivitas Investasi", "T", ""],
            [28, "Arus Kas Keluar untuk Penempatan Investasi", "D", ""],
            [29, "Arus Kas Keluar untuk Pembelian Aset Tetap", "D", ""],
            [30, "Arus Kas Keluar untuk Aktivitas Investasi Lainnya", "D", ""],
            [31, "Jumlah Arus Kas dari Aktivitas Investasi", "T", ""],
            [32, "Arus Kas dari Aktivitas Pendanaan", "H", ""],
            [33, "Arus Kas Masuk dari Aktivitas Pendanaan", "T", ""],
            [34, "Arus Kas Masuk dari Penyetoran", "D", ""],
            [35, "Arus Kas Masuk dari Pinjaman", "D", ""],
            [36, "Arus Kas Masuk dari Aktivitas Pendanaan Lainnya", "D", ""],
            [37, "Arus Kas Keluar untuk Aktivitas Pendanaan", "T", ""],
            [38, "Arus Kas Keluar untuk Pembayaran Dividen", "D", ""],
            [39, "Arus Kas Keluar untuk Pembayaran Pinjaman", "D", ""],
            [40, "Arus Kas Keluar untuk Aktivitas Pendanaan Lainnya", "D", ""],
            [41, "Jumlah Arus Kas dari Aktivitas Pendanaan", "T", ""],
            [42, "Kenaikan (Penurunan) Bersih Kas dan Setara Kas", "T", ""],
            [43, "Saldo Akhir Kas-Bank", "T", ""]
        ];
    }
}
