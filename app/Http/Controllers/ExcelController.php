<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BalanceExport;
use App\Exports\ProfitAndLossExport;

class ExcelController extends Controller
{
    public function downloadBalance()
    {
        return Excel::download(new BalanceExport, 'balance.xlsx',\Maatwebsite\Excel\Excel::XLSX);
    }

    public function downloadProfitAndLoss()
    {
        return Excel::download(new ProfitAndLossExport, 'profit&loss.xlsx',\Maatwebsite\Excel\Excel::XLSX);
    }
}
