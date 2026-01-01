<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashBank;
use App\Models\PaymentAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RefundController extends Controller
{
    /**
     * Get datatables for refund list
     */
    public function datatables()
    {
        $query = PaymentAllocation::with(['cashBank.contact', 'cashBank.chartOfAccount'])
            ->where('type', 'refund')
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
            ->addColumn('action', function (PaymentAllocation $allocation) {
                $showUrl = route('transaction.refunds.show', $allocation->id);
                $actions = '<a href="' . $showUrl . '" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>';
                
                if ($allocation->status === 'active') {
                    $actions .= ' <button class="btn btn-sm btn-danger btn-void" data-id="' . $allocation->id . '"><i class="bi bi-x-circle"></i> Void</button>';
                }
                
                return $actions;
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
            ->orderColumn('cash_bank_date', function ($query, $order) {
                $query->orderBy('created_at', $order);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Get advances with balance > 0 for refund
     * For select2 dropdown
     */
    public function advanceSelect2(Request $request)
    {
        $search = $request->get('q');
        
        // Get all active advances that can be refunded
        $advances = PaymentAllocation::with(['cashBank.contact'])
            ->where('type', 'advance')
            ->where('status', 'active')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('cashBank', function ($q) use ($search) {
                    $q->where('number', 'like', "%$search%")
                      ->orWhereHas('contact', function ($contactQuery) use ($search) {
                          $contactQuery->where('display_name', 'like', "%$search%");
                      });
                });
            })
            ->get()
            ->map(function ($advance) {
                // Calculate how much has been refunded from this advance
                $totalRefunded = PaymentAllocation::where('type', 'refund')
                    ->where('cash_bank_id', $advance->cash_bank_id)
                    ->where('status', 'active')
                    ->where('description', 'like', "%Advance ID: {$advance->id}%")
                    ->sum('allocation');
                
                $availableForRefund = $advance->allocation - $totalRefunded;
                
                return [
                    'advance' => $advance,
                    'total_refunded' => $totalRefunded,
                    'available_for_refund' => $availableForRefund,
                ];
            })
            ->filter(function ($item) {
                // Filter only advances with available > 0
                return $item['available_for_refund'] > 0;
            })
            ->map(function ($item) {
                $advance = $item['advance'];
                $available = $item['available_for_refund'];
                
                return [
                    'id' => $advance->id,
                    'text' => $advance->cashBank->number . ' - ' . $advance->cashBank->contact->display_name . ' (Available: Rp ' . number_format($available, 2, ',', '.') . ')',
                    'allocation' => $advance->allocation,
                    'available_for_refund' => $available,
                    'cash_bank_id' => $advance->cash_bank_id,
                    'contact_id' => $advance->cashBank->contact_id,
                    'date' => $advance->cashBank->date,
                ];
            })
            ->values();

        return response()->json([
            'results' => $advances,
            'pagination' => ['more' => false]
        ]);
    }

    /**
     * Get detail advance by ID for refund
     */
    public function getAdvanceDetail($id)
    {
        try {
            $advance = PaymentAllocation::with(['cashBank.contact', 'cashBank.chartOfAccount'])
                ->where('type', 'advance')
                ->where('status', 'active')
                ->findOrFail($id);

            // Calculate how much has been refunded from this advance
            $totalRefunded = PaymentAllocation::where('type', 'refund')
                ->where('cash_bank_id', $advance->cash_bank_id)
                ->where('status', 'active')
                ->where('description', 'like', "%Advance ID: {$advance->id}%")
                ->sum('allocation');
            
            $availableForRefund = $advance->allocation - $totalRefunded;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $advance->id,
                    'cash_bank_id' => $advance->cash_bank_id,
                    'cash_bank_number' => $advance->cashBank->number,
                    'contact_id' => $advance->cashBank->contact_id,
                    'contact_name' => $advance->cashBank->contact->display_name,
                    'date' => $advance->cashBank->date,
                    'display_date' => $advance->cashBank->display_date,
                    'advance_amount' => $advance->allocation,
                    'advance_amount_formatted' => number_format($advance->allocation, 2, ',', '.'),
                    'total_refunded' => $totalRefunded,
                    'total_refunded_formatted' => number_format($totalRefunded, 2, ',', '.'),
                    'available_for_refund' => $availableForRefund,
                    'available_for_refund_formatted' => number_format($availableForRefund, 2, ',', '.'),
                    'chart_of_account' => $advance->cashBank->chartOfAccount->display_name ?? '-',
                    'advance_description' => $advance->description,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Advance not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store new refund
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'advance_id' => 'required|exists:payment_allocations,id',
            'refund_amount' => 'required|numeric|min:0.01',
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
            $advance = PaymentAllocation::where('type', 'advance')
                ->where('status', 'active')
                ->findOrFail($request->advance_id);
            
            // Calculate how much has been refunded from this advance
            $totalRefunded = PaymentAllocation::where('type', 'refund')
                ->where('cash_bank_id', $advance->cash_bank_id)
                ->where('status', 'active')
                ->where('description', 'like', "%Advance ID: {$advance->id}%")
                ->sum('allocation');
            
            $availableForRefund = $advance->allocation - $totalRefunded;
            
            // Validate refund amount tidak melebihi available
            if ($request->refund_amount > $availableForRefund) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund amount cannot exceed available amount (Rp ' . 
                                number_format($availableForRefund, 2, ',', '.') . ')'
                ], 422);
            }

            // Create refund payment allocation (negative allocation to reverse advance)
            $description = $request->description 
                ? $request->description . ' | Advance ID: ' . $advance->id
                : 'Refund from Advance ID: ' . $advance->id;
            
            $refund = PaymentAllocation::create([
                'type' => 'refund',
                'cash_bank_id' => $advance->cash_bank_id,
                'debit_note_id' => null,
                'allocation' => $request->refund_amount,
                'status' => 'active',
                'description' => $description,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund has been created successfully',
                'data' => $refund->load('cashBank.contact')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create refund: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show refund detail
     */
    public function show($id)
    {
        try {
            $refund = PaymentAllocation::with(['cashBank.contact', 'cashBank.chartOfAccount'])
                ->where('type', 'refund')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $refund
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Refund not found'
            ], 404);
        }
    }

    /**
     * Delete/void refund
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $refund = PaymentAllocation::where('type', 'refund')
                ->findOrFail($id);

            // Void the refund instead of hard delete
            $refund->update([
                'status' => 'void',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund has been voided successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to void refund: ' . $e->getMessage()
            ], 500);
        }
    }
}
