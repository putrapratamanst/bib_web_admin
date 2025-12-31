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
        
        $cashBanks = CashBank::with(['contact'])
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
            ->map(function ($cashBank) {
                // Calculate total allocated from payment_allocations for this cash_bank_id
                $totalAllocated = PaymentAllocation::where('cash_bank_id', $cashBank->id)
                    ->where('status', 'posted')
                    ->sum('allocation');
                
                $available = $cashBank->amount - $totalAllocated;
                
                return [
                    'cash_bank' => $cashBank,
                    'total_allocated' => $totalAllocated,
                    'available' => $available,
                ];
            })
            ->filter(function ($item) {
                // Filter only cash banks with available > 0
                return $item['available'] > 0;
            })
            ->map(function ($item) {
                $cashBank = $item['cash_bank'];
                $available = $item['available'];
                
                return [
                    'id' => $cashBank->id,
                    'text' => $cashBank->number . ' - ' . $cashBank->contact->display_name . ' (Available: Rp ' . number_format($available, 2, ',', '.') . ')',
                    'amount' => $cashBank->amount,
                    'available' => $available,
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
            $cashBank = CashBank::with(['contact', 'chartOfAccount'])
                ->findOrFail($id);

            // Calculate total allocated from payment_allocations for this cash_bank_id
            $totalAllocated = PaymentAllocation::where('cash_bank_id', $cashBank->id)
                ->where('status', 'posted')
                ->sum('allocation');
            
            $available = $cashBank->amount - $totalAllocated;
            
            // Debug: get all allocations for this cash bank
            $allocations = PaymentAllocation::where('cash_bank_id', $cashBank->id)
                ->where('status', 'posted')
                ->get(['id', 'type', 'allocation', 'status', 'created_at']);

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
                    'total_allocated' => $totalAllocated,
                    'available_for_allocation' => $available,
                    'available_for_allocation_formatted' => number_format($available, 2, ',', '.'),
                    'chart_of_account' => $cashBank->chartOfAccount->display_name ?? '-',
                    // Debug info
                    'debug' => [
                        'allocations_count' => $allocations->count(),
                        'allocations' => $allocations
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cash Bank not found: ' . $e->getMessage()
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
            
            // Calculate total allocated from payment_allocations for this cash_bank_id
            $totalAllocated = PaymentAllocation::where('cash_bank_id', $cashBank->id)
                ->where('status', 'posted')
                ->sum('allocation');
            
            $available = $cashBank->amount - $totalAllocated;
            
            // Validate allocation tidak melebihi available
            if ($request->allocation > $available) {
                return response()->json([
                    'success' => false,
                    'message' => 'Allocation amount cannot exceed available amount (Rp ' . 
                                number_format($available, 2, ',', '.') . ')'
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
