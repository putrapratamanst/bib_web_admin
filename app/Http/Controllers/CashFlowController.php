<?php

namespace App\Http\Controllers;

use App\Exports\CashFlowExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CashFlow;

class CashFlowController extends Controller
{

    public function downloadCashFlow()
    {
        $data = CashFlow::getData();
        return Excel::download(new CashFlowExport($data), 'cashflow.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
