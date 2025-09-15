<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashBankStoreRequest;
use App\Http\Requests\PaymentAllocationStoreRequest;
use App\Http\Resources\CashBankResource;
use App\Models\CashBank;
use App\Models\PaymentAllocation;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentAllocationController extends Controller
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
        $query = CashBank::with('contact', 'chartOfAccount')
            // ->where('status', '!=', 'approved') // Filter out approved status
            ->orderBy('date', 'desc');

        return DataTables::eloquent($query)
            ->addColumn('contact_name', function (CashBank $cashBank) {
                return $cashBank->contact->display_name;
            })
            ->filterColumn('contact_name', function ($query, $keyword) {
                $query->whereHas('contact', function ($query) use ($keyword) {
                    $query->where('display_name', 'like', "%$keyword%");
                });
            })
            ->make(true);
    }

    public function store(PaymentAllocationStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $allocations = [];
            foreach ($data['debit_note_id'] as $index => $debitNoteId) {
                $allocationAmount = $data['allocation'][$index] ?? 0;

                // skip kalau tidak ada nilai
                if ($allocationAmount <= 0) {
                    continue;
                }

                $allocations[] = PaymentAllocation::create([
                    'cash_bank_id'  => $data['cash_bank_id'][$index] ?? null,
                    'debit_note_id' => $debitNoteId,
                    'allocation'    => $allocationAmount,
                    'status'        => $data['status'][$index] ?? 'draft',
                    // 'created_by' => auth()->id()
                ]);
            }

            return response()->json([
                'message' => 'Data has been created',
                'data'    => $allocations, // bisa pakai resource collection juga
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => [
                        'Something went wrong',
                        $e->getMessage() // untuk debugging
                    ]
                ]
            ], 500);
        }
    }
}
