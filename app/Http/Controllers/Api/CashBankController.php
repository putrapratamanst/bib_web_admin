<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashBankRequest;
use App\Http\Requests\UpdateCashBankRequest;
use App\Http\Resources\CashBankResource;
use App\Models\CashBank;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class CashBankController extends Controller
{
    public function index(Request $request)
    {
        $cashBanks = CashBank::with(['chartOfAccount', 'contact', 'currency', 'details'])
            ->when($request->keyword, function ($query) use ($request) {
                $query->where('number', 'like', "%$request->keyword%")
                    ->orWhere('date', 'like', "%$request->keyword%")
                    ->orWhere('reference', 'like', "%$request->keyword%")
                    ->orWhere('memo', 'like', "%$request->keyword%");
            })
            ->orderBy('date', 'desc')
            ->paginate($request->pageSize);

        return CashBankResource::collection($cashBanks);
    }

    
    public function store(StoreCashBankRequest $request)
    {
        try {
            $data = $request->validated();

            $cashBank = CashBank::create([
                'number' => $data['number'],
                'type' => $data['type'],
                'chart_of_account_id' => $data['chart_of_account_id'],
                'contact_id' => $data['contact_id'],
                'date' => $data['date'],
                'reference' => $data['reference'],
                'memo' => $data['memo'],
                'currency_id' => $data['currency_id'],
                'exchange_rate' => $data['exchange_rate'],
                'amount' => $data['amount'],
            ]);

            $cashBank->details()->createMany($data['details']);

            return response()->json([
                'message' => 'Transaction created successfully',
            ], 201);
        }
        catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => [
                        $e->getMessage()
                    ]
                ]
            ])->setStatusCode(500);
        }
    }

    public function show(string $id): CashBankResource
    {
        $cashBank = CashBank::with(['chartOfAccount', 'contact', 'currency', 'details'])->find($id);

        if (!$cashBank) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        "data not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new CashBankResource($cashBank);
    }

    public function update(UpdateCashBankRequest $request, string $id)
    {
        try {
            $data = $request->validated();

            $cashBank = CashBank::find($id);

            if (!$cashBank) {
                throw new HttpResponseException(response()->json([
                    'errors' => [
                        'message' => [
                            "data not found"
                        ]
                    ]
                ])->setStatusCode(404));
            }

            $cashBank->update([
                'number' => $data['number'],
                'type' => $data['type'],
                'chart_of_account_id' => $data['chart_of_account_id'],
                'contact_id' => $data['contact_id'],
                'date' => $data['date'],
                'reference' => $data['reference'],
                'memo' => $data['memo'],
                'currency_id' => $data['currency_id'],
                'exchange_rate' => $data['exchange_rate'],
                'amount' => $data['amount'],
            ]);

            $cashBank->details()->delete();
            $cashBank->details()->createMany($data['details']);

            return response()->json([
                'message' => 'Transaction updated successfully',
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => [
                        $e->getMessage()
                    ]
                ]
            ])->setStatusCode(500);
        }
    }

    public function destroy(string $id)
    {
        
    }
}
