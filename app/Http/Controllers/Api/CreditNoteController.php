<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditNoteApprovalRequest;
use App\Http\Requests\CreditNoteStoreRequest;
use App\Http\Resources\CreditNoteResource;
use App\Models\CreditNote;
use App\Models\DebitNoteBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CreditNoteController extends Controller
{
    public function index()
    {
        $creditNotes = CreditNote::orderBy('id', 'asc')->get();

        return CreditNoteResource::collection($creditNotes);
    }

    public function datatables(Request $request)
    {
        $query = CreditNote::with(['contract.contractType', 'contract.billingAddress', 'contract.contact'])->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->filled('insurance_type')) {
            $query->whereHas('contract', function($q) use ($request) {
                $q->where('contract_type_id', $request->insurance_type);
            });
        }

        if ($request->filled('currency_code')) {
            $query->where('currency_code', $request->currency_code);
        }

        return DataTables::of($query)
            ->addColumn('contract_number', function (CreditNote $b) {
                return $b->contract->number;
            })
            ->addColumn('contract_id', function (CreditNote $b) {
                return $b->contract->id;
            })
            ->addColumn('insured_name', function (CreditNote $b) {
                return $b->contract->billingAddress ? $b->contract->billingAddress->name : ($b->contract->contact ? $b->contract->contact->display_name : '-');
            })
            ->addColumn('insurance_type', function (CreditNote $b) {
                return $b->contract->contractType ? $b->contract->contractType->name : '-';
            })
            ->addColumn('approval_status_badge', function (CreditNote $b) {
                return $b->approval_status_badge;
            })
            ->addColumn('actions', function (CreditNote $b) {
                /** @var \App\Models\User|null $user */
                $user = Auth::user();
                $canApprove = $user && $user->canApproveCreditNotes();
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('transaction.credit-notes.show', $b->id) . '" class="btn btn-sm btn-outline-primary">View</a>';

                if ($b->canBeEdited()) {
                    $actions .= '<a href="' . route('transaction.credit-notes.edit', $b->id) . '" class="btn btn-sm btn-outline-secondary">Edit</a>';
                }
                
                // Only show approve/reject buttons for approvers
                if ($b->canBeApproved() && $canApprove) {
                    $actions .= '<button class="btn btn-sm btn-success approve-btn" data-id="' . $b->id . '">Approve</button>';
                    $actions .= '<button class="btn btn-sm btn-danger reject-btn" data-id="' . $b->id . '">Reject</button>';
                }
                
                // if ($b->canBePrinted()) {
                //     $actions .= '<button class="btn btn-sm btn-secondary print-btn" data-id="' . $b->id . '">Print</button>';
                // }
                
                $actions .= '</div>';
                return $actions;
            })
            ->orderColumn('contact', function ($query, $order) {
                $query
                    ->join('contracts', 'contracts.id', '=', 'credit_notes.contract_id')
                    ->join('contacts', 'contacts.id', '=', 'contracts.contact_id')
                    ->orderBy('contacts.display_name', $order);
            })
            ->rawColumns(['approval_status_badge', 'actions'])
            ->make(true);
    }

    public function generateNumber()
    {
        $number = $this->generateCreditNoteNumber();
        return response()->json(['number' => $number]);
    }

    /**
     * Generate credit note number for internal use
     */
    private function generateCreditNoteNumber()
    {
        // Format: BIB/CYY/MM-00001
        $year = date('y'); // 2 digit year
        $month = date('m');
        $prefix = "BIB/C{$year}/{$month}-";
        
        $lastCreditNote = CreditNote::where('number', 'like', "{$prefix}%")
            ->orderBy('number', 'desc')
            ->first();
        
        if ($lastCreditNote) {
            $lastNumber = (int) substr($lastCreditNote->number, strrpos($lastCreditNote->number, '-') + 1);
            $runningNumber = $lastNumber + 1;
        } else {
            $runningNumber = 1;
        }
        
        return sprintf("%s%04d", $prefix, $runningNumber);
    }

    public function store(CreditNoteStoreRequest $request)
    {
        try {
            $request->validated();
            $data = $request->all();
            
            // Generate credit note number
            $creditNoteNumber = $this->generateCreditNoteNumber();
            
            $contractID = DebitNoteBilling::find($data['billing_id'])->debitNote->contract_id ?? null;
            $debitNoteID = DebitNoteBilling::find($data['billing_id'])->debitNote->id ?? null;
            
            $creditNote = CreditNote::create([
                'contract_id' => $contractID,
                'debit_note_id' => $debitNoteID,
                'number' => $creditNoteNumber,
                'date' => $data['date'],
                'description' => $data['description'],
                'currency_code' => $data['currency_code'],
                'exchange_rate' => $data['exchange_rate'],
                'amount' => $data['amount'],
                'status' => $data['status'],
                'approval_status' => 'pending', // Default approval status for new credit notes
                'billing_id' => $data['billing_id'],
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Data has been created',
                'data' => new CreditNoteResource($creditNote)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(CreditNoteApprovalRequest $request, $id)
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            // Check if user has approver role
            if (!$user || !$user->canApproveCreditNotes()) {
                return response()->json([
                    'message' => 'You are not authorized to approve Credit Notes. Only users with approver role can perform this action.'
                ], 403);
            }

            $creditNote = CreditNote::findOrFail($id);
            
            if (!$creditNote->canBeApproved()) {
                return response()->json([
                    'message' => 'Credit Note cannot be approved in current status.'
                ], 400);
            }

            $creditNote->update([
                'approval_status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $request->input('notes'),
            ]);

            return response()->json([
                'message' => 'Credit Note has been approved successfully.',
                'data' => new CreditNoteResource($creditNote->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reject(CreditNoteApprovalRequest $request, $id)
    {
        try {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            // Check if user has approver role
            if (!$user || !$user->canApproveCreditNotes()) {
                return response()->json([
                    'message' => 'You are not authorized to reject Credit Notes. Only users with approver role can perform this action.'
                ], 403);
            }

            $creditNote = CreditNote::findOrFail($id);
            
            if (!$creditNote->canBeApproved()) {
                return response()->json([
                    'message' => 'Credit Note cannot be rejected in current status.'
                ], 400);
            }

            $creditNote->update([
                'approval_status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $request->input('notes', 'Rejected'),
            ]);

            return response()->json([
                'message' => 'Credit Note has been rejected.',
                'data' => new CreditNoteResource($creditNote->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $creditNote = CreditNote::findOrFail($id);

            if (!$creditNote->canBeEdited()) {
                return response()->json([
                    'message' => 'Credit Note cannot be edited because it has been approved or rejected.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'billing_id' => 'required|exists:debit_note_billings,id',
                'date' => 'required|date',
                'description' => 'nullable|string',
                'currency_code' => 'required|exists:currencies,code',
                'exchange_rate' => 'required|numeric',
                'amount' => 'required|numeric',
                'status' => 'required|in:active,cancel',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $billing = DebitNoteBilling::with('debitNote')->findOrFail($request->billing_id);

            $creditNote->update([
                'contract_id' => $billing->debitNote->contract_id ?? null,
                'debit_note_id' => $billing->debitNote->id ?? null,
                'billing_id' => $request->billing_id,
                'date' => $request->date,
                'description' => $request->description,
                'currency_code' => $request->currency_code,
                'exchange_rate' => $request->exchange_rate,
                'amount' => $request->amount,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Data has been updated',
                'data' => new CreditNoteResource($creditNote->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
