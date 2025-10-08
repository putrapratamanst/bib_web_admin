<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DebitNoteBilling;
use App\Models\Cashout;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DebitNoteBillingController extends Controller
{
    public function datatables(Request $request)
    {
        $query = DebitNoteBilling::with(['debitNote', 'debitNote.contract']);

        return DataTables::of($query)
            ->addColumn('contract_number', function(DebitNoteBilling $billing) {
                return $billing->debitNote->contract->number ?? '-';
            })
            ->addColumn('debit_note_number', function(DebitNoteBilling $billing) {
                return $billing->debitNote->number ?? '-';
            })
            ->addColumn('is_posted_to_cashout', function(DebitNoteBilling $billing) {
                $hasLinkedCashout = Cashout::where('debit_note_billing_id', $billing->id)->exists();
                return $hasLinkedCashout ? 'Yes' : 'No';
            })
            ->addColumn('action', function(DebitNoteBilling $billing) {
                $hasLinkedCashout = Cashout::where('debit_note_billing_id', $billing->id)->exists();
                
                if (!$hasLinkedCashout && $billing->status === 'paid') {
                    return '<button class="btn btn-primary btn-sm" onclick="postBillingToCashout(\'' . $billing->id . '\')">
                        <i class="fas fa-paper-plane"></i> Post to Cashout
                    </button>';
                } elseif ($hasLinkedCashout) {
                    return '<span class="badge bg-success">Posted</span>';
                } else {
                    return '<span class="badge bg-secondary">Not Ready</span>';
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function postToCashout($id)
    {
        try {
            $billing = DebitNoteBilling::with(['debitNote', 'debitNote.contract'])->findOrFail($id);
            
            // Check if already posted to cashout
            $existingCashout = Cashout::where('debit_note_billing_id', $billing->id)->first();
            if ($existingCashout) {
                return response()->json([
                    'message' => 'Billing sudah di-posting ke cashout sebelumnya',
                    'success' => false
                ], 400);
            }

            // Check if billing is paid
            if ($billing->status !== 'paid') {
                return response()->json([
                    'message' => 'Billing harus berstatus paid untuk dapat di-posting ke cashout',
                    'success' => false
                ], 400);
            }

            // Create cashout
            $cashout = Cashout::create([
                'debit_note_id' => $billing->debit_note_id,
                'debit_note_billing_id' => $billing->id,
                'insurance_id' => $billing->debitNote->contract->contact_id ?? null,
                'number' => $this->generateCashoutNumber(),
                'date' => now()->toDateString(),
                'due_date' => $billing->due_date,
                'currency_code' => $billing->debitNote->currency_code,
                'exchange_rate' => $billing->debitNote->exchange_rate,
                'amount' => $billing->amount,
                'installment_number' => $this->getInstallmentNumber($billing),
                'description' => "Cashout untuk Billing: {$billing->billing_number}",
                'status' => 'pending',
                'created_by' => auth()->id() ?? 1,
            ]);

            return response()->json([
                'message' => 'Billing berhasil di-posting ke cashout',
                'success' => true,
                'data' => [
                    'cashout' => $cashout
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    private function generateCashoutNumber(): string
    {
        $prefix = 'CSH';
        $date = now()->format('Ym');
        $sequence = Cashout::whereRaw('DATE_FORMAT(created_at, "%Y%m") = ?', [$date])->count() + 1;
        
        return "{$prefix}-{$date}-" . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    private function getInstallmentNumber(DebitNoteBilling $billing): int
    {
        // Hitung urutan billing dalam debit note yang sama
        $billingOrder = DebitNoteBilling::where('debit_note_id', $billing->debit_note_id)
            ->where('date', '<=', $billing->date)
            ->orderBy('date')
            ->orderBy('id')
            ->pluck('id')
            ->search($billing->id);
        
        return $billingOrder !== false ? $billingOrder + 1 : 1;
    }
}