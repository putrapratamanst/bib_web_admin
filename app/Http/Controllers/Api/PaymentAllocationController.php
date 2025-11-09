<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashBankStoreRequest;
use App\Http\Requests\PaymentAllocationStoreRequest;
use App\Http\Resources\CashBankResource;
use App\Models\CashBank;
use App\Models\DebitNote;
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
            $allocationSums = []; // menyimpan total allocation per cash_bank_id

            foreach ($data['debit_note_id'] as $index => $debitNoteId) {
                $allocationAmount = $data['allocation'][$index] ?? 0;
                $cashBankId = $data['cash_bank_id'][$index] ?? null;

                // skip kalau tidak ada nilai
                if ($allocationAmount <= 0) {
                    continue;
                }

                $allocationSums[$cashBankId] = ($allocationSums[$cashBankId] ?? 0) + $allocationAmount;
                if ($allocationSums[$cashBankId] > CashBank::find($cashBankId)->amount) {
                    return response()->json([
                        'errors' => [
                            'allocation' => [
                                'Total allocation for Cash Bank exceeds available amount.'
                            ]
                        ]
                    ], 400);
                }
                // check if allocation for this debit note already exists for this cash bank
                $existingAllocation = PaymentAllocation::where('cash_bank_id', $data['cash_bank_id'][$index] ?? null)
                    ->where('debit_note_id', $debitNoteId)
                    ->first();
                if ($existingAllocation) {
                    // update existing allocation
                    $existingAllocation->allocation = $allocationAmount;
                    $existingAllocation->status = $data['status'][$index] ?? 'draft';
                    $existingAllocation->save();
                    $allocations[] = $existingAllocation;
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


    public function storeByCashBankID(Request $request, $cashbankID)
    {
        $request->merge(['cash_bank_id' => $cashbankID]);
        try {
            $request->validate([
                'cash_bank_id' => 'required|exists:cash_banks,id',
                'debit_note_billing_id' => 'required|exists:debit_note_billings,id',
                'allocation' => 'required|numeric|min:0',
            ]);

            $cashBank = CashBank::findOrFail($request->cash_bank_id);

            // Get debit_note_id from billing
            $debitNoteBilling = \App\Models\DebitNoteBilling::with('debitNote')
                ->findOrFail($request->debit_note_billing_id);
            $debitNoteId = $debitNoteBilling->debit_note_id;

            // Get total allocated amount for this cash bank
            $totalAllocated = PaymentAllocation::where('cash_bank_id', $request->cash_bank_id)
                ->sum('allocation');

            // Check if allocation exceeds cash bank amount
            if ($request->allocation >= ($cashBank->amount - $totalAllocated)) {
                if ($totalAllocated + $request->allocation > $cashBank->amount) {
                    return response()->json([
                        'message' => sprintf(
                            'Total allocation amount cannot exceed cash bank amount. Already allocated: %s, Available: %s',
                            number_format($totalAllocated, 2, ',', '.'),
                            number_format($cashBank->amount - $totalAllocated, 2, ',', '.')
                        )
                    ], 422);
                }
            }

            $allocation = PaymentAllocation::create([
                'cash_bank_id' => $request->cash_bank_id,
                'debit_note_id' => $debitNoteId,
                'allocation' => $request->allocation,
                'status' => 'posted',
                'debit_note_billing_id' => $request->debit_note_billing_id,
                // 'created_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Allocation has been saved successfully',
                'data' => $allocation
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving the allocation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
