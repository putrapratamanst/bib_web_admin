<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceSheetController extends Controller
{
    public function index()
    {
        $balanceSheet = DB::select("
            SELECT 
                ac.name AS account_category,
                coa.code,
                coa.name,
                SUM(
                    CASE 
                        WHEN ac.name IN ('Kas Bank', 'Akun Piutang', 'Aktiva Lancar Lainnya', 'Aktiva Tetap', 'Aktiva Lainnya') 
                        THEN COALESCE(je.debit, 0) - COALESCE(je.credit, 0) 
                        ELSE COALESCE(je.credit, 0) - COALESCE(je.debit, 0)
                    END
                ) AS nominal
            FROM chart_of_accounts AS coa
            LEFT JOIN account_categories AS ac ON ac.id = coa.account_category_id
            LEFT JOIN journal_entry_details je ON je.chart_of_account_id = coa.id
            WHERE coa.financial_statement = 'balance_sheet'
            GROUP BY ac.name, coa.code, coa.name
            ORDER BY ac.number_order asc
        ");

        $table = '<table class="table table-bordered table-hover table-striped">
        <thead>
            <tr>
                <th>Kategori Akun</th>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($balanceSheet as $row) {
            $table .= '<tr>
                <td>' . $row->account_category . '</td>
                <td>' . $row->code . '</td>
                <td>' . $row->name . '</td>
                <td class="text-end">' . number_format($row->nominal, 0, ',', '.') . '</td>
            </tr>';
        }

        $table .= '</tbody></table>';

        return response()->json([
            'data' => $table
        ]);
    }
}
