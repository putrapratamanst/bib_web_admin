<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashBank;
use App\Models\PaymentAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AdvanceController extends Controller
{
    /**
     * Get datatables for advance list
     */
    public function datatables()
    {
        $query = PaymentAllocation::with(['cashBank.contact', 'cashBank.chartOfAccount'])
            ->where('type', 'advance')
            ->whereNull('debit_note_id')
            ->orderBy('created_at', 'desc');

        return DataTables::eloquent($query)
            ->addColumn('cash_bank_number', function (PaymentAllocation $allocation) {
                return $allocation->cashBank->number ?? '-';
            })
            ->addColumn('contact_name', function (PaymentAllocation $allocation) {
                return $allocation->cashBank->contact->display_name ?? '-';
            })
            ->addColumn('cash_bank_date', function (PaymentAllocation $allocation) {
                return $allocation->cashBank->display_date ?? '-';
            })
            ->filterColumn('cash_bank_number', function ($query, $keyword) {
                $query->whereHas('cashBank', function ($q) use ($keyword) {
                    $q->where('number', 'like', "%$keyword%");
                });
            })
            ->filterColumn('contact_name', function ($query, $keyword) {
                $query->whereHas('cashBank.contact', function ($q) use ($keyword) {
                    $q->where('display_name', 'like', "%$keyword%");
                });
            })
            ->make(true);
    }

    /**
     * Get cash banks with available for allocation > 0
     * For select2 dropdown
     */
    public function cashBankSelect2(Request $request)
    {
        $search = $request->get('q');
        
        $cashBanks = CashBank::with(['contact', 'paymentAllocations'])
            ->where('type', 'receive') // Only receive type
            ->where('status', 'approved') // Only approved
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('number', 'like', "%$search%")
                      ->orWhereHas('contact', function ($contactQuery) use ($search) {
                          $contactQuery->where('display_name', 'like', "%$search%");
                      });
                });
            })
            ->get()
            ->filter(function ($cashBank) {
                // Filter only cash banks with available allocation > 0
                return $cashBank->available_for_allocation > 0;
            })
            ->map(function ($cashBank) {
                return [
                    'id' => $cashBank->id,
                    'text' => $cashBank->number . ' - ' . $cashBank->contact->display_name . ' (Available: Rp ' . $cashBank->available_for_allocation_formatted . ')',
                    'amount' => $cashBank->amount,
                    'available' => $cashBank->available_for_allocation,
                    'contact_id' => $cashBank->contact_id,
                    'date' => $cashBank->date,
                ];
            })
            ->values();

        return response()->json([
            'results' => $cashBanks,
            'pagination' => ['more' => false]
        ]);
    }

    /**
     * Get detail cash bank by ID
     */
    public function getCashBankDetail($id)
    {
        try {
            $cashBank = CashBank::with(['contact', 'chartOfAccount', 'paymentAllocations'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $cashBank->id,
                    'number' => $cashBank->number,
                    'contact_id' => $cashBank->contact_id,
                    'contact_name' => $cashBank->contact->display_name,
                    'date' => $cashBank->date,
                    'display_date' => $cashBank->display_date,
                    'amount' => $cashBank->amount,
                    'display_amount' => $cashBank->display_amount,
                    'available_for_allocation' => $cashBank->available_for_allocation,
                    'available_for_allocation_formatted' => $cashBank->available_for_allocation_formatted,
                    'chart_of_account' => $cashBank->chartOfAccount->display_name ?? '-',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cash Bank not found'
            ], 404);
        }
    }

    /**
     * Store new advance payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cash_bank_id' => 'required|exists:cash_banks,id',
            'allocation' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $cashBank = CashBank::findOrFail($request->cash_bank_id);
            
            // Validate allocation tidak melebihi available
            if ($request->allocation > $cashBank->available_for_allocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Allocation amount cannot exceed available amount (Rp ' . 
                                number_format($cashBank->available_for_allocation, 2, ',', '.') . ')'
                ], 422);
            }

            // Create advance payment allocation
            $advance = PaymentAllocation::create([
                'type' => 'advance',
                'cash_bank_id' => $request->cash_bank_id,
                'debit_note_id' => null, // NULL karena ini advance
                'allocation' => $request->allocation,
                'status' => 'active',
                'description' => $request->description,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Advance payment has been created successfully',
                'data' => $advance->load('cashBank.contact')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create advance payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show advance detail
     */
    public function show($id)
    {
        try {
            $advance = PaymentAllocation::with(['cashBank.contact', 'cashBank.chartOfAccount'])
                ->where('type', 'advance')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $advance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Advance not found'
            ], 404);
        }
    }

    /**
     * Delete/void advance
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $advance = PaymentAllocation::where('type', 'advance')
                ->findOrFail($id);

            // Void the advance instead of hard delete
            $advance->update([
                'status' => 'void',
                // 'updated_by' => auth()->user()?->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Advance payment has been voided successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to void advance payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
