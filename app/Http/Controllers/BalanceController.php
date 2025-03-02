<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BalanceExport;
use App\Models\Balance;

class BalanceController extends Controller
{

    public function downloadBalance()
    {
        $data = Balance::getData()->map(fn($item, $index) => [
            'no_urut' => $index + 1,
            'name' => $item->name,
            'code' => $item->code,
            'rincian' => "",
            'balance_type' => $item->balance_type,
            'total_credit' => $item->total_credit,
        ]);
        return Excel::download(new BalanceExport($data), 'balance.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
