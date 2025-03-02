<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProfitAndLossExport;
use App\Models\ProfitAndLoss;

class ProfitAndLossController extends Controller
{

    public function downloadProfitAndLoss()
    {
        $data = collect(ProfitAndLoss::getData())->map(fn($item, $index) => [
            'no_urut' => $index + 1,
            'name' => $item->name,
            'code' => $item->code,
            'rincian' => "",
            'balance_type' => $item->balance_type,
            'total_credit' => $item->total_credit,
        ]);
        
        return Excel::download(new ProfitAndLossExport($data), 'profitandloss.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
