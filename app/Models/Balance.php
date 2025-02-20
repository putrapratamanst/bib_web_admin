<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    // Jika ingin menggunakan table 'balances'
    // protected $table = 'balances';

    // Jika menggunakan database nanti, atur fillable field di sini
    protected $fillable = [
        'urutan', 'uraian', 'kode', 'rincian', 'tipe', 'amount'
    ];

    // Data sementara (hardcoded) untuk sekarang
    public static function getData()
    {
        return [
            ['Urutan', 'Uraian', 'Kode', 'Rincian', 'Tipe', 'Amount'],
            ['1', 'ASET', '', '', 'P', ''],
            ['2', 'Kas dan Setara Kas', '1100', 'PK01', 'H', ''],
            ['3', 'Rekening Operasional', '1110', '', 'D', ''],
            ['4', 'Rekening Premi', '1120', 'PK02', 'D', ''],
            ['5', 'Investasi', '1200', 'PK03', 'D', ''],
            ['6', 'Piutang Premi', '1300', 'PK04', 'D', ''],
            ['7', 'Piutang Jasa Keperantaraan', '1400', 'PK05', 'D', ''],
            ['8', 'Piutang Klaim', '', '', 'D', ''],
            ['9', 'Piutang Konsultasi', '1500', 'PK06', 'D', ''],
            ['10', 'Piutang Jasa Penangan Klaim', '1600', 'PK06', 'D', ''],
            ['11', 'Aset Tetap', '1700', 'PK07', 'D', ''],
            ['12', 'Aset Lain', '1900', 'PK08', 'D', ''],
            ['13', 'Jumlah Aset', '1000', '', 'T', ''],
            ['14', 'LIABILITAS & EKUITAS', '', '', 'P', ''],
            ['15', 'Liabilitas', '', '', 'H', ''],
            ['16', 'Utang Premi', '2100', 'PK09', 'D', ''],
            ['17', 'Pendapatan Jasa Keperantaraan', '2200', '', 'D', ''],
            ['18', 'Utang Klaim', '2300', 'PK10', 'D', ''],
            ['19', 'Utang Komisi', '2400', 'PK11', 'D', ''],
            ['20', 'Utang Pajak', '2500', 'PK12', 'D', ''],
            ['21', 'Utang Lain', '2900', 'PK13', 'D', ''],
            ['22', 'Jumlah Liabilitas', '2000', '', 'T', ''],
            ['23', 'Ekuitas', '', '', 'H', ''],
            ['24', 'Modal Disetor', '3100', '', 'D', ''],
            ['25', 'Tambahan Modal Disetor', '3200', '', 'D', ''],
            ['26', 'Laba Ditahan', '3300', '', 'D', ''],
            ['27', 'Laba Tahun Berjalan', '3400', '', 'D', ''],
            ['28', 'Ekuitas Lainnya', '3500', '', 'D', ''],
            ['29', 'Saldo Komponen Ekuitas', '3510', '', 'D', ''],
            ['30', 'Kenaikan (Kerugian) Komponen', '3520', '', 'D', ''],
            ['31', 'Jumlah Ekuitas', '3000', '', 'T', ''],
            ['32', 'Jumlah Liabilitas dan Ekuitas', '', '', 'T', ''],
        ];
    }
}
