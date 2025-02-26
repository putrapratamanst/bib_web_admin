<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashBankStoreRequest;
use App\Http\Resources\CashBankResource;
use App\Models\CashBank;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CashBankController extends Controller
{
    public function index()
    {
        $q = request('q');

        $cashBanks = CashBank::where('number', 'like', "%$q%")
            ->orWhereHas('contact', function ($query) use ($q) {
                $query->where('display_name', 'like', "%$q%");
            })
            ->orderBy('date', 'desc')
            ->paginate(10);

        return CashBankResource::collection($cashBanks);
    }

    public function datatables()
    {
        $query = CashBank::with('contact', 'chartOfAccount')->orderBy('date', 'desc');

        return DataTables::eloquent($query)
            ->addColumn('contact_name', function (CashBank $cashBank){
                return $cashBank->contact->display_name;
            })
            ->filterColumn('contact_name', function ($query, $keyword) {
                $query->whereHas('contact', function ($query) use ($keyword) {
                    $query->where('display_name', 'like', "%$keyword%");
                });
            })
            ->make(true);
    }

    public function store(CashBankStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $data['date'] = date('Y-m-d', strtotime($data['date']));

            $cashBanks = CashBank::create([
                'type' => $data['type'],
                'number' => $data['number'],
                'contact_id' => $data['contact_id'],
                'date' => $data['date'],
                'chart_of_account_id' => $data['chart_of_account_id'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'reference' => $data['reference'],
                'status' => $data['status'],
                // 'created_by' => auth()->id()
            ]);

            return response()->json([
                'message' => 'Data has been created',
                'data' => new CashBankResource($cashBanks)
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => [
                        'Something went wrong'
                    ]
                ]
            ], 500);
        }
    }
}
