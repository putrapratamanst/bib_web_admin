<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashBankStoreRequest;
use App\Http\Resources\CashBankResource;
use App\Models\CashBank;
use App\Models\CashBankDetail;
use App\Models\DebitNoteBilling;
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
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return CashBankResource::collection($cashBanks);
    }

    public function datatables()
    {
        $query = CashBank::with('contact', 'chartOfAccount', 'contraAccount')->orderBy('created_at', 'desc');
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

    public function show($id)
    {
        try {
            $cashBank = CashBank::with(['contact', 'chartOfAccount', 'contraAccount', 'cashBankDetails.debitNote'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => new CashBankResource($cashBank)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cash Bank not found'
            ], 404);
        }
    }

    public function store(CashBankStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $data['date'] = date('Y-m-d', strtotime($data['date']));

            // Create CashBank
            $cashBank = CashBank::create([
                'type' => $data['type'],
                'number' => $data['number'],
                'contact_id' => $data['contact_id'],
                'date' => $data['date'],
                'chart_of_account_id' => $data['chart_of_account_id'],
                'contra_account_id' => $data['contra_account_id'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'status' => $data['status'],
                // 'created_by' => auth()->id()
            ]);

            return response()->json([
                'message' => 'Data has been created',
                'data' => new CashBankResource($cashBank->load('cashBankDetails.debitNote'))
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => [
                        'Something went wrong: ' . $e->getMessage()
                    ]
                ]
            ], 500);
        }
    }
}
