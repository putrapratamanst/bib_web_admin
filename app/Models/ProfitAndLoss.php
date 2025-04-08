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
        $data = ReportFinance::from('report_finance as rf')
            ->leftJoin('chart_of_accounts as coa', function ($join) {
                $join->on('coa.code', '>=', DB::raw('rf.from_account_code'))
                    ->where('coa.code', '<=', DB::raw('rf.to_account_code'));
                //  ->whereIn('coa.prefix', [1, 2, 3]);
            })
            ->leftJoin('journal_entry_details as jed', 'jed.chart_of_account_id', '=', 'coa.id')

            ->select(
                'rf.id',
                'rf.code',
                'rf.name',
                DB::raw('"D" as balance_type'),
                DB::raw('COALESCE(SUM(jed.debit), 0) - COALESCE(SUM(jed.credit), 0) as total_debit')
            )
            ->where(function ($query) {
                $query->where('rf.code', 'LIKE', '4%')
                    ->orWhere('rf.code', 'LIKE', '5%')
                    ->orWhere('rf.code', 'LIKE', '6%');
            })
            ->where('rf.is_active', 1)
            ->where('rf.financial_statement', 'profit_loss')
            ->groupBy('rf.id', 'rf.code', 'rf.name', 'rf.balance_type')
            ->orderByRaw("
            CASE 
                WHEN rf.code = '4100' THEN 1
                WHEN rf.code = '4110' THEN 2
                WHEN rf.code = '4200' THEN 3
                WHEN rf.code = '4300' THEN 4
                WHEN rf.code = '4400' THEN 5
                WHEN rf.code = '4900' THEN 6
                WHEN rf.code = '4004' THEN 7
                WHEN rf.code = '6000' THEN 8
                WHEN rf.code = '6001' THEN 9
                WHEN rf.code = '6002' THEN 10
                WHEN rf.code = '6009' THEN 11
                WHEN rf.code = '6025' THEN 12
                WHEN rf.code = '6020' THEN 13
                WHEN rf.code = '6200' THEN 14
                WHEN rf.code = '5000' THEN 15
                WHEN rf.code = '6100' THEN 16
                WHEN rf.code = '5600' THEN 17
                WHEN rf.code = '6250' THEN 18
                WHEN rf.code = '5710' THEN 19
                WHEN rf.code = '5720' THEN 20
                WHEN rf.code = '5700' THEN 21
                WHEN rf.code = '6300' THEN 22
                ELSE 99
            END, rf.code
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
