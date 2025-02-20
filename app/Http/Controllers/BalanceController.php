<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BalanceExport;
use App\Models\Balance;

class BalanceController extends Controller
{

    public function downloadBalance()
    {
        $data = Balance::getData();
        return Excel::download(new BalanceExport($data), 'balance.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
