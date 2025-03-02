<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Balance extends Model
{
    // Jika ingin menggunakan table 'balances'
    // protected $table = 'balances';

    // Jika menggunakan database nanti, atur fillable field di sini
    protected $fillable = [
        'urutan',
        'uraian',
        'kode',
        'rincian',
        'tipe',
        'amount'
    ];

    // Data sementara (hardcoded) untuk sekarang
    public static function getData()
    {
        $data = ChartOfAccount::from('chart_of_accounts as ca')
        ->leftJoin('journal_entry_details as jed', 'jed.chart_of_account_id', '=', 'ca.id')
        ->select(
            'ca.id',
            'ca.code',
            'ca.name',
            DB::raw('"D" as balance_type'),
            DB::raw('COALESCE(SUM(jed.debit), 0) as total_debit'),
            DB::raw('COALESCE(SUM(jed.credit), 0) as total_credit')
        )
        ->where(function ($query) {
            $query->where('ca.code', 'LIKE', '1%')
                ->orWhere('ca.code', 'LIKE', '2%')
                ->orWhere('ca.code', 'LIKE', '3%');
        })
        ->where('ca.is_active', 1)
        ->where('ca.financial_statement', 'balance_sheet')
        ->groupBy('ca.id', 'ca.code', 'ca.name', 'ca.balance_type')
        ->orderByRaw('LENGTH(ca.code), ca.code')
        ->get();

    // Kelompokkan berdasarkan angka pertama dari code
    $grouped = $data->groupBy(function ($item) {
        return substr($item->code, 0, 1);
    });

    $finalData = collect();
    $totalLiabilitas = 0;
    $totalEkuitas = 0;

    foreach ($grouped as $key => $items) {
        // Tambahkan header berdasarkan kategori
        if ($key == '1') {
            $headerName = 'Aset';
            $balanceType = 'P';
        } elseif ($key == '2') {
            $headerName = 'Liabilitas & Ekuitas';
            $balanceType = 'P';
        } elseif ($key == '3') {
            $headerName = 'Ekuitas';
            $balanceType = 'H';
        } else {
            $headerName = null;
            $balanceType = null;
        }

        if ($headerName) {
            $finalData->push((object) [
                'id' => null,
                'code' => null,
                'name' => $headerName,
                'balance_type' => $balanceType,
                'total_debit' => null,
                'total_credit' => null,
            ]);
        }

        // Tambahkan "Liabilitas" setelah "Liabilitas & Ekuitas"
        if ($key == '2') {
            $finalData->push((object) [
                'id' => null,
                'code' => null,
                'name' => 'Liabilitas',
                'balance_type' => 'H',
                'total_debit' => null,
                'total_credit' => null,
            ]);
        }

        // Hitung total kategori
        $totalDebit = $items->sum('total_debit');
        $totalCredit = $items->sum('total_credit');

        // Simpan total liabilitas & ekuitas untuk perhitungan terakhir
        if ($key == '2') {
            $totalLiabilitas = $totalDebit - $totalCredit;
        } elseif ($key == '3') {
            $totalEkuitas = $totalDebit - $totalCredit;
        }

        // Tambahkan data asli
        $finalData = $finalData->merge($items);

        // Tentukan nama total berdasarkan kode awal
        if ($key == '1') {
            $totalName = 'Jumlah Aset';
            $balanceType = 'T';
        } elseif ($key == '2') {
            $totalName = 'Jumlah Liabilitas';
            $balanceType = 'T';
        } elseif ($key == '3') {
            $totalName = 'Jumlah Ekuitas';
            $balanceType = 'T';
        } else {
            $totalName = "Total {$key}000";
            $balanceType = null;
        }

        // Tambahkan total untuk kategori
        $finalData->push((object) [
            'id' => null,
            'code' => "{$key}000",
            'name' => $totalName,
            'balance_type' => $balanceType,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);
    }

    // Tambahkan "Jumlah Ekuitas & Liabilitas"
    $finalData->push((object) [
        'id' => null,
        'code' => '4000',
        'name' => 'Jumlah Ekuitas & Liabilitas',
        'balance_type' => 'T',
        'total_debit' => $totalLiabilitas + $totalEkuitas,
        'total_credit' => 0,
    ]);

    // Kembalikan array tanpa key-value
    return $finalData->values()->all();    }
}
