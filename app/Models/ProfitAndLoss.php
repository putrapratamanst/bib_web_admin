<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProfitAndLoss extends Model
{
    // Jika ingin menggunakan table 'profit_and_losses'
    // protected $table = 'profit_and_losses';

    // Jika menggunakan database nanti, atur fillable field di sini
    protected $fillable = [
        'urutan',
        'uraian',
        'kode',
        'rincian',
        'tipe',
        'amount'
    ];

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
                $query->where('ca.code', 'LIKE', '4%')
                    ->orWhere('ca.code', 'LIKE', '5%')
                    ->orWhere('ca.code', 'LIKE', '6%');
            })
            ->where('ca.is_active', 1)
            ->where('ca.financial_statement', 'profit_loss')
            ->groupBy('ca.id', 'ca.code', 'ca.name', 'ca.balance_type')
            ->orderByRaw("
            CASE 
                WHEN ca.code = '4100' THEN 1
                WHEN ca.code = '4110' THEN 2
                WHEN ca.code = '4200' THEN 3
                WHEN ca.code = '4300' THEN 4
                WHEN ca.code = '4400' THEN 5
                WHEN ca.code = '4900' THEN 6
                WHEN ca.code = '4004' THEN 7
                WHEN ca.code = '6000' THEN 8
                WHEN ca.code = '6001' THEN 9
                WHEN ca.code = '6002' THEN 10
                WHEN ca.code = '6009' THEN 11
                WHEN ca.code = '6025' THEN 12
                WHEN ca.code = '6020' THEN 13
                WHEN ca.code = '6200' THEN 14
                WHEN ca.code = '5000' THEN 15
                WHEN ca.code = '6100' THEN 16
                WHEN ca.code = '5600' THEN 17
                WHEN ca.code = '6250' THEN 18
                WHEN ca.code = '5710' THEN 19
                WHEN ca.code = '5720' THEN 20
                WHEN ca.code = '5700' THEN 21
                WHEN ca.code = '6300' THEN 22
                ELSE 99
            END, ca.code
        ")->get();

        // Kelompokkan berdasarkan angka pertama dari code
        $grouped = $data->groupBy(function ($item) {
            return substr($item->code, 0, 1);
        });

        $finalData = collect();

        foreach ($grouped as $key => $items) {
            if ($key == '4') {
                $headerName = 'PENDAPATAN';
                $balanceType = 'P';
            } elseif ($key == '6') {
                $headerName = 'BEBAN';
                $balanceType = 'P';
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
            foreach ($items as $item) {
                if ($item->code === '6000' || $item->code === '4100') {
                    $item->balance_type = 'T';
                }

                // Sisipkan "Pendapatan (Beban) Komprehensif" sebelum kode 5710
                if ($item->code === '5710') {
                    $finalData->push((object) [
                        'id' => null,
                        'code' => null,
                        'name' => 'Pendapatan (Beban) Komprehensif',
                        'balance_type' => 'T',
                        'total_debit' => null,
                        'total_credit' => null,
                    ]);
                }

                $finalData->push($item);
            }

            // Hitung total kategori
            
            $totalDebit = $items->sum('total_debit');
            $totalCredit = $items->sum('total_credit');
            if ($item->code === '6200') {

                $finalData->push((object) [
                    'id' => null,
                    'code' => '5000',
                    'name' => 'Jumlah Beban',
                    'balance_type' => 'T',
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                ]);
            }
            if ($item->code === '4004') {
                $finalData->push((object) [
                    'id' => null,
                    'code' => '',
                    'name' => 'Jumlah Pendapatan',
                    'balance_type' => 'T',
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                ]);
            }
        }

        return $finalData->values()->all();
    }
}
