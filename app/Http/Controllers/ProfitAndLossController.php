<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitAndLossExport;
use App\Models\ProfitAndLoss;

class ProfitAndLossController extends Controller
{

    public function downloadProfitAndLoss()
    {
        $data = ProfitAndLoss::getData();
        return Excel::download(new ProfitAndLossExport($data), 'profitandloss.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
