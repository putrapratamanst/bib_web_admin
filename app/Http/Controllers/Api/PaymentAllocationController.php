<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashBankStoreRequest;
use App\Http\Requests\PaymentAllocationStoreRequest;
use App\Http\Resources\CashBankResource;
use App\Models\CashBank;
use App\Models\Cashout;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\DebitNoteBilling;
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
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return CashBankResource::collection($cashBanks);
    }

    public function datatables()
    {
        $query = CashBank::with('contact', 'chartOfAccount')
            // ->where('status', '!=', 'approved') // Filter out approved status
            ->orderBy('created_at', 'desc');

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


    public function storeAll(Request $request, $cashbankID)
    {
        try {
            $cashBank = CashBank::findOrFail($cashbankID);

            // Get all available debit note billings for this contact
            $debitNoteBillings = \App\Models\DebitNoteBilling::whereHas('debitNote', function ($query) use ($cashBank) {
                $query->where('contact_id', $cashBank->contact_id);
            })
                ->with('debitNote')
                ->get()
                ->map(function ($billing) {
                    // Calculate allocated amount
                    $allocated_amount = PaymentAllocation::where('debit_note_billing_id', $billing->id)
                        ->sum('allocation');
                    $billing->allocated_amount = $allocated_amount;
                    $billing->remaining_amount = $billing->amount - $allocated_amount;
                    return $billing;
                })
                ->filter(function ($billing) {
                    return $billing->remaining_amount > 0;
                })
                ->sortBy('due_date');

            // Get total allocated amount for this cash bank
            $totalAllocated = PaymentAllocation::where('cash_bank_id', $cashbankID)
                ->sum('allocation');

            $availableAmount = $cashBank->amount - $totalAllocated;
            $allocations = [];

            // Allocate to each billing until we run out of money
            foreach ($debitNoteBillings as $billing) {
                if ($availableAmount <= 0) break;

                $allocationAmount = min($availableAmount, $billing->remaining_amount);

                if ($allocationAmount > 0) {
                    $allocation = PaymentAllocation::create([
                        'cash_bank_id' => $cashbankID,
                        'debit_note_id' => $billing->debit_note_id,
                        'debit_note_billing_id' => $billing->id,
                        'allocation' => $allocationAmount,
                        'status' => 'posted'
                    ]);

                    $allocations[] = $allocation;
                    $availableAmount -= $allocationAmount;
                }
            }

            return response()->json([
                'message' => 'All available amounts have been allocated successfully',
                'data' => $allocations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while allocating payments',
                'error' => $e->getMessage()
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
            $billing = DebitNoteBilling::with('debitNote')
                ->findOrFail($request->debit_note_billing_id);
            $debitNoteId = $billing->debit_note_id;

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

            // lakukan cashout disini
            $detailContract = $billing->debitNote->contract;
            if ($totalAllocated == 0) {
                            if ($detailContract->details) {
                $listInsurance = $detailContract->details ?? [];
                $bilAmount = $billing->amount;
                foreach ($listInsurance as $insurance) {
                    $creditNote = CreditNote::where('billing_id', $billing->id)->first();
                    if ($creditNote) {
                        $bilAmount -= $creditNote->amount;
                    }
                    $share = $bilAmount * ($insurance->percentage / 100);
                    $brokerfee = $share * ($insurance->brokerage_fee / 100);
                    $engfee = $share * ($insurance->eng_fee / 100); // ini basos
                    $discount = $share * ($detailContract->discount / 100); // ini tambahan discount
                    $komisi = $brokerfee - $discount; // komisi setelah diskon
                    $pph = $komisi * 0.02; // contoh perhitungan PPh 2%
                    $stamp = 0;
                    if ($insurance->description === 'Leader') {
                        $stamp = $detailContract->stamp; // materai untuk leader
                    }

                    $amountForCashout = $share - $discount - $komisi + $pph + $stamp;
                    Cashout::create([
                        'debit_note_id' => $billing->debit_note_id,
                        'debit_note_billing_id' => $billing->id,
                        'insurance_id' => $insurance->insurance_id,
                        'number' => $this->generateCashoutNumber(),
                        'date' => now()->toDateString(),
                        'due_date' => $billing->due_date,
                        'currency_code' => $billing->debitNote->currency_code,
                        'exchange_rate' => $billing->debitNote->exchange_rate,
                        'amount' => $amountForCashout,
                        'installment_number' => $this->getInstallmentNumber($billing),
                        'description' => "Cashout untuk Billing: {$billing->billing_number}",
                        'status' => 'pending',
                        'created_by' => 1,
                        'updated_by' => 1,

                    ]);
                    // sepertinya cashout saya tidak save , bantu cek
                    $bilAmount = $billing->amount;
                }
            }
            }

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

    public function storeByCashBankIDForCashout(Request $request, $cashbankID)
    {
        $request->merge(['cash_bank_id' => $cashbankID]);
        try {
            $request->validate([
                'cash_bank_id' => 'required|exists:cash_banks,id',
                'cashout_id' => 'required|exists:cashouts,id',
                'allocation' => 'required|numeric|min:0',
            ]);

            $cashBank = CashBank::findOrFail($request->cash_bank_id);

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

            // Type pay - handle cashout
            $cashout = Cashout::with('insurance')
                ->findOrFail($request->cashout_id);

            $allocation = PaymentAllocation::create([
                'cash_bank_id' => $request->cash_bank_id,
                'debit_note_id' => $cashout->debit_note_id,
                'allocation' => $request->allocation,
                'status' => 'posted',
                'cashout_id' => $request->cashout_id,
            ]);

            // Update cashout status to paid if fully allocated
            $totalCashoutAllocated = PaymentAllocation::where('cashout_id', $cashout->id)
                ->sum('allocation');
            
            if ($totalCashoutAllocated >= $cashout->amount) {
                $cashout->status = 'paid';
                $cashout->save();
            }

            return response()->json([
                'message' => 'Cashout allocation has been saved successfully',
                'data' => $allocation
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving the cashout allocation',
                'error' => $e->getMessage()
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
        $previousBillingsCount = DebitNoteBilling::where('debit_note_id', $billing->debit_note_id)
            ->where('id', '<', $billing->id)
            ->count();

        return $previousBillingsCount + 1;
    }
}
