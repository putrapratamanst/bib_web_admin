<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\DebitNote;
use App\Models\DebitNoteBilling;
use App\Models\Cashout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebitNoteBillingController extends Controller
{
    public function create($id = null)
    {
        // If $id provided, it's from Debit Note
        if ($id) {
            $debitNote = DebitNote::with('debitNoteBillings')->findOrFail($id);

            $existingBilledAmount = (float) $debitNote->debitNoteBillings->sum('amount');
            $remainingAvailableAmount = max(0, (float) $debitNote->amount - $existingBilledAmount);

            return view('transaction.debitnotebilling.create', [
                'debitNote' => $debitNote,
                'existingBilledAmount' => $existingBilledAmount,
                'remainingAvailableAmount' => $remainingAvailableAmount,
            ]);
        }
        
        // Otherwise, create standalone billing
        return view('transaction.billing.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'debit_note_id' => 'required|exists:debit_notes,id',
                'billing_number'   => 'required|array',
                'billing_number.*' => 'required|string|max:50|distinct|unique:debit_note_billings,billing_number',
                'date'   => 'required|array',
                'date.*' => 'required|date',
                'due_date'   => 'required|array',
                'due_date.*' => 'required|date|after_or_equal:date.*',
                'amount'   => 'required|array',
                'amount.*' => 'required|numeric|min:0',
                'gross_premium' => 'nullable|numeric',
                'discount_percent' => 'nullable|numeric',
                'discount_amount' => 'nullable|numeric',
                'net_premium_amount' => 'nullable|numeric',
                // kalau status juga array
                // 'status'   => 'required|array',
                // 'status.*' => 'required|in:unpaid,paid,overdue',
            ]);

            // Get the debit note with contract to check amount limit
            $debitNote = DebitNote::with('contract')->findOrFail($request->debit_note_id);
            
            // Calculate total billing amount being added
            $totalNewBillingAmount = 0;
            foreach ($request->amount as $amount) {
                $totalNewBillingAmount += floatval($amount);
            }

            // Calculate total existing billing amount for this debit note
            $existingBilledAmount = (float) DebitNoteBilling::where('debit_note_id', $debitNote->id)->sum('amount');
            $remainingAvailableAmount = max(0, (float) $debitNote->amount - $existingBilledAmount);

            // Total billed after creating new billing(s)
            $totalBilledAfterCreate = $existingBilledAmount + $totalNewBillingAmount;
            
            // Check if total billing exceeds debit note amount (existing + new)
            if ($totalBilledAfterCreate > (float) $debitNote->amount) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Total billing amount melebihi sisa available. Remaining available saat ini: ' . number_format($remainingAvailableAmount, 2) . '.');
            }

            $grossPremium = $request->input('gross_premium');
            $netPremium = $request->input('net_premium_amount');

            if ($grossPremium !== null && $netPremium !== null && is_numeric($grossPremium) && is_numeric($netPremium)) {
                if (floatval($netPremium) > floatval($grossPremium)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Net Amount Premi tidak boleh lebih besar dari Gross Premium.');
                }
            }

            DB::beginTransaction();

            $debitNote->update([
                'gross_premium' => $grossPremium === '' ? null : $grossPremium,
                'discount_percent' => $request->input('discount_percent') === '' ? null : $request->input('discount_percent'),
                'discount_amount' => $request->input('discount_amount') === '' ? null : $request->input('discount_amount'),
                'net_premium_amount' => $netPremium === '' ? null : $netPremium,
                'updated_by' => auth()->id(),
            ]);

            foreach ($request->billing_number as $i => $billingNumber) {
                $debitNoteBilling = new DebitNoteBilling();
                $debitNoteBilling->debit_note_id = $request->debit_note_id;
                $debitNoteBilling->billing_number = $billingNumber;
                $debitNoteBilling->date = $request->date[$i];
                $debitNoteBilling->due_date = $request->due_date[$i];
                
                // Calculate amount with policy_fee + stamp_fee for first installment
                $amount = floatval($request->amount[$i]);
                
                // Check if this is installment 1 (first billing)
                $isFirstInstallment = false;
                if (preg_match('/-INST(\d+)/i', $billingNumber, $matches)) {
                    $isFirstInstallment = ((int)$matches[1] === 1);
                } else {
                    // If no INST pattern, check if this is the first entry in the array
                    $isFirstInstallment = ($i === 0);
                }
                
                // Add policy_fee and stamp_fee for first installment
                if ($isFirstInstallment && $debitNote->contract) {
                    $policyFee = floatval($debitNote->contract->policy_fee ?? 0);
                    $stampFee = floatval($debitNote->contract->stamp_fee ?? 0);
                    $amount += $policyFee + $stampFee;
                }
                
                $debitNoteBilling->amount = $amount;
                $debitNoteBilling->status = 'pending'; // default status is pending
                
                if (!$debitNoteBilling->save()) {
                    throw new \Exception("Failed to save billing: {$billingNumber}");
                }
            }

            DB::commit();

            return redirect()
                ->route('transaction.debit-notes.show', ['id' => $request->debit_note_id])
                ->with('success', 'Debit Note Billings created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validation Error: ' . implode(', ', $e->validator->errors()->all()));

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            Log::error('Database error in DebitNoteBilling store: ' . $e->getMessage());
            
            $errorMessage = 'Database error occurred.';
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage = 'Billing number already exists. Please use different billing numbers.';
            } elseif (str_contains($e->getMessage(), 'foreign key constraint')) {
                $errorMessage = 'Invalid debit note. Please refresh and try again.';
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in DebitNoteBilling store: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saving billings: ' . $e->getMessage());
        }
    }

    // Method untuk menampilkan list billing
    public function index()
    {
        return view('transaction.billing.index');
    }

    public function show($id)
    {
        try {
            $billing = DebitNoteBilling::with(['debitNote.contract.contact', 'debitNote.contract.details'])->findOrFail($id);
            return view('transaction.billing.show', [
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return redirect()->route('transaction.billings.index')->with('error', 'Billing not found');
        }
    }

    public function edit($id)
    {
        try {
            // $id adalah debit note ID, bukan billing ID
            $debitNote = DebitNote::with(['contract', 'billings'])->findOrFail($id);
            
            // Check if billings exist
            if ($debitNote->billings->isEmpty()) {
                return redirect()->back()->with('error', 'Belum ada billing untuk debit note ini');
            }
            
            // Check if all billings are pending
            $nonPendingCount = $debitNote->billings->where('status', '!=', 'pending')->count();
            if ($nonPendingCount > 0) {
                return redirect()->back()->with('error', 'Hanya billing dengan status pending yang dapat di-edit');
            }
            
            return view('transaction.debitnotebilling.edit', [
                'debitNote' => $debitNote,
                'billings' => $debitNote->billings,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Debit Note not found');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // $id adalah debit note ID
            $debitNote = DebitNote::with('contract')->findOrFail($id);
            
            $request->validate([
                'billing_id'   => 'required|array',
                'billing_id.*' => 'required|exists:debit_note_billings,id',
                'date'   => 'required|array',
                'date.*' => 'required|date',
                'due_date'   => 'required|array',
                'due_date.*' => 'required|date|after_or_equal:date.*',
                'amount'   => 'required|array',
                'amount.*' => 'required|numeric|min:0',
                'gross_premium' => 'nullable|numeric',
                'discount_percent' => 'nullable|numeric',
                'discount_amount' => 'nullable|numeric',
                'net_premium_amount' => 'nullable|numeric',
            ]);
            
            // Calculate total all billings + fees untuk INST1
            $policyFee = floatval($debitNote->contract->policy_fee ?? 0);
            $stampFee = floatval($debitNote->contract->stamp_fee ?? 0);
            $totalFees = $policyFee + $stampFee;
            
            $totalAllBillings = 0;
            foreach ($request->billing_id as $i => $billingId) {
                $billing = DebitNoteBilling::findOrFail($billingId);
                $amountValue = floatval($request->amount[$i]);
                
                // Check if this is INST1
                $isFirstInstallment = false;
                if (preg_match('/-INST(\d+)/i', $billing->billing_number, $matches)) {
                    $isFirstInstallment = ((int)$matches[1] === 1);
                } else {
                    $firstBilling = $debitNote->billings()->orderBy('created_at')->first();
                    $isFirstInstallment = ($billing->id === $firstBilling->id);
                }
                
                // Tambahkan fees untuk INST1 saat validasi
                if ($isFirstInstallment && $totalFees > 0) {
                    $amountValue += $totalFees;
                }
                $totalAllBillings += $amountValue;
            }

            $grossPremium = $request->input('gross_premium');
            $netPremium = $request->input('net_premium_amount');

            if ($grossPremium !== null && $netPremium !== null && is_numeric($grossPremium) && is_numeric($netPremium)) {
                if (floatval($netPremium) > floatval($grossPremium)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Net Amount Premi tidak boleh lebih besar dari Gross Premium.');
                }
            }
            
           
            DB::beginTransaction();

            $debitNote->update([
                'gross_premium' => $grossPremium === '' ? null : $grossPremium,
                'discount_percent' => $request->input('discount_percent') === '' ? null : $request->input('discount_percent'),
                'discount_amount' => $request->input('discount_amount') === '' ? null : $request->input('discount_amount'),
                'net_premium_amount' => $netPremium === '' ? null : $netPremium,
                'updated_by' => auth()->id(),
            ]);

            foreach ($request->billing_id as $i => $billingId) {
                $billing = DebitNoteBilling::findOrFail($billingId);
                
                // Check if billing can be edited (only pending status)
                if ($billing->status !== 'pending') {
                    throw new \Exception("Billing {$billing->billing_number} tidak dapat di-edit karena statusnya bukan pending");
                }
                
                $billing->date = $request->date[$i];
                $billing->due_date = $request->due_date[$i];
                
                // Calculate amount with policy_fee + stamp_fee for INST1
                $amount = floatval($request->amount[$i]);
                
                // Check if this is INST1
                $isFirstInstallment = false;
                if (preg_match('/-INST(\d+)/i', $billing->billing_number, $matches)) {
                    $isFirstInstallment = ((int)$matches[1] === 1);
                } else {
                    $firstBilling = $debitNote->billings()->orderBy('created_at')->first();
                    $isFirstInstallment = ($billing->id === $firstBilling->id);
                }
                
                // Tambahkan fees untuk INST1 saat save
                if ($isFirstInstallment && $totalFees > 0) {
                    $amount += $totalFees;
                }
                
                $billing->amount = $amount;
                // $billing->updated_by = auth()->id() ?? 1;
                
                if (!$billing->save()) {
                    throw new \Exception("Failed to update billing: {$billing->billing_number}");
                }
            }

            DB::commit();

            return redirect()
                ->route('transaction.debit-notes.show', ['id' => $debitNote->id])
                ->with('success', 'All billings updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validation Error: ' . implode(', ', $e->validator->errors()->all()));

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating DebitNoteBilling: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating billing: ' . $e->getMessage());
        }
    }

    public function printBilling($id)
    {
        try {
            $billing = DebitNoteBilling::with(['debitNote.contract.contact', 'debitNote.contract.details'])->findOrFail($id);
            return view('transaction.billing.print', [
                'billing' => $billing
            ]);
        } catch (\Exception $e) {
            return redirect()->route('transaction.billings.index')->with('error', 'Billing not found');
        }
    }

    public function printBillingDirectory($id)
    {
        try {
            $billing = DebitNoteBilling::with([
                'debitNote.contract', 
                'debitNote.contract.contact', 
                'debitNote.contract.billingAddress',
                'debitNote.contract.contractType',
                'debitNote.currency'
            ])->findOrFail($id);

            return view('transaction.billing.print-directory', compact('billing'));
        } catch (\Exception $e) {
            return redirect()->route('transaction.billings.index')->with('error', 'Billing not found');
        }
    }

    // Method untuk post billing ke cashout
    public function postToCashout($id)
    {
        try {
            $billing = DebitNoteBilling::with(['debitNote', 'debitNote.contract'])->findOrFail($id);
            
            // Check if already posted to cashout
            $existingCashout = Cashout::where('debit_note_billing_id', $billing->id)->first();
            if ($existingCashout) {
                return response()->json([
                    'success' => false,
                    'message' => 'Billing sudah di-posting ke cashout sebelumnya'
                ], 400);
            }

            // Check if billing is paid
            if ($billing->status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Billing harus berstatus paid untuk dapat di-posting ke cashout'
                ], 400);
            }

            // Hitung installment number berdasarkan urutan billing
            $installmentNumber = $this->getInstallmentNumber($billing);

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
                'installment_number' => $installmentNumber,
                'description' => "Cashout untuk Billing: {$billing->billing_number}",
                'status' => 'pending',
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Billing berhasil di-posting ke cashout',
                'data' => [
                    'cashout_id' => $cashout->id,
                    'cashout_number' => $cashout->number
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error posting billing to cashout: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper method untuk generate nomor cashout
    private function generateCashoutNumber(): string
    {
        $prefix = 'CSH';
        $date = now()->format('Ym');
        $sequence = Cashout::whereRaw('DATE_FORMAT(created_at, "%Y%m") = ?', [$date])->count() + 1;
        
        return "{$prefix}-{$date}-" . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    // Helper method untuk mendapatkan installment number
    private function getInstallmentNumber(DebitNoteBilling $billing): int
    {
        // Hitung urutan billing dalam debit note yang sama berdasarkan tanggal
        $billingOrder = DebitNoteBilling::where('debit_note_id', $billing->debit_note_id)
            ->where('date', '<=', $billing->date)
            ->orderBy('date')
            ->orderBy('id')
            ->pluck('id')
            ->search($billing->id);
        
        return $billingOrder !== false ? $billingOrder + 1 : 1;
    }
}
