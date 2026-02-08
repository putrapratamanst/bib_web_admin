<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditNoteApprovalRequest;
use App\Http\Resources\DebitNoteResource;
use App\Models\DebitNote;
use App\Models\DebitNoteDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DebitNoteController extends Controller
{
    public function index()
    {
        $debitNotes = DebitNote::orderBy('id', 'asc')->get();

        return DebitNoteResource::collection($debitNotes);
    }

    public function datatables(Request $request)
    {
        $query = DebitNote::with(['contract.contractType'])->orderBy('created_at', 'desc');

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

        if ($request->filled('is_posted')) {
            $query->where('is_posted', $request->is_posted == '1');
        }

        return DataTables::of($query)
            ->addColumn('contract', function(DebitNote $b) {
                return $b->contract->number;
            })
            ->addColumn('policy_number', function(DebitNote $b) {
                return $b->contract->policy_number ?? '-';
            })
            ->addColumn('insurance_type', function(DebitNote $b) {
                return $b->contract->contractType ? $b->contract->contractType->name : '-';
            })
            ->addColumn('is_posted', function(DebitNote $b) {
                return $b->is_posted ? 'Yes' : 'No';
            })
            ->addColumn('approval_status_badge', function (DebitNote $b) {
                return $b->approval_status_badge;
            })
            ->addColumn('actions', function (DebitNote $b) {
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('transaction.debit-notes.show', $b->id) . '" class="btn btn-sm btn-outline-primary">View</a>';
                
                // Add edit button only if debit note can be edited
                if ($b->canBeEdited()) {
                    $actions .= '<a href="' . route('transaction.debit-notes.edit', $b->id) . '" class="btn btn-sm btn-outline-secondary">Edit</a>';
                }
                
                // Only show approve/reject buttons for approvers
                if ($b->canBeApproved() && auth()->user()->canApproveCreditNotes()) {
                    $actions .= '<button class="btn btn-sm btn-success approve-btn" data-id="' . $b->id . '">Approve</button>';
                    $actions .= '<button class="btn btn-sm btn-danger reject-btn" data-id="' . $b->id . '">Reject</button>';
                }
                
                // if ($b->canBePrinted()) {
                //     $actions .= '<button class="btn btn-sm btn-secondary print-btn" data-id="' . $b->id . '">Print</button>';
                // }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['approval_status_badge', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        // Debug: Log incoming data
        \Log::info('DebitNote Store Request Data:', $request->all());
        
        // Clean up comma-separated numbers
        $cleanedData = $request->all();
        if (isset($cleanedData['exchange_rate'])) {
            $cleanedData['exchange_rate'] = str_replace(',', '', $cleanedData['exchange_rate']);
        }
        if (isset($cleanedData['amount'])) {
            $cleanedData['amount'] = str_replace(',', '', $cleanedData['amount']);
        }

        // Replace request data with cleaned data
        $request->merge($cleanedData);
        
        \Log::info('Billing Address ID from request:', ['billing_address_id' => $request->billing_address_id]);
        
        // Validation
        $validator = Validator::make($request->all(), [
            'number' => 'required|string|max:255|unique:debit_notes,number',
            'contract_id' => 'required|exists:contracts,id',
            'billing_address_id' => 'required|exists:billing_addresses,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'created_at' => 'nullable|date',
            'currency' => 'required|string|max:3',
            'exchange_rate' => 'required|numeric|min:0',
            'installment' => 'required|integer|max:12',
            'amount' => 'required|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'success' => false
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // Get contact_id from billing_address
            $billingAddress = \App\Models\BillingAddress::findOrFail($request->billing_address_id);
            
            // Create Debit Note
            $debitNote = DebitNote::create([
                'number' => $request->number,
                'contact_id' => $billingAddress->contact_id,
                'contract_id' => $request->contract_id,
                'billing_address_id' => $request->billing_address_id,
                'date' => $request->date,
                'due_date' => $request->due_date,
                'currency_code' => $request->currency,
                'exchange_rate' => $request->exchange_rate,
                'amount' => $request->amount,
                'description' => $request->description,
                'status' => 'active',
                'approval_status' => 'pending', // Default pending status
                'installment' => $request->installment,
                'created_at' => $request->created_at ? \Carbon\Carbon::parse($request->created_at) : now(),
                'updated_at' => now(),
                'created_by' => auth()->id(),
            ]);

            // Create Debit Note Details
            if (isset($request->details) && is_array($request->details)) {
                foreach ($request->details as $detail) {
                    DebitNoteDetail::create([
                        'debit_note_id' => $debitNote->id,
                        'item_description' => $detail['item_description'],
                        'amount' => $detail['amount'],
                        'created_by' => 1,
                        'updated_by' => 1,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Debit Note created successfully',
                'success' => true,
                'data' => new DebitNoteResource($debitNote->fresh())
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to create Debit Note: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function show($id)
    {
        $debitNote = DebitNote::with(['contract', 'billingAddress', 'debitNoteDetails', 'cashouts'])->findOrFail($id);
        
        return response()->json([
            'data' => new DebitNoteResource($debitNote)
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $debitNote = DebitNote::findOrFail($id);
            
            // Check if the debit note can be edited
            if (!$debitNote->canBeEdited()) {
                return response()->json([
                    'message' => 'Debit Note cannot be edited because it has been submitted for approval or is already approved/rejected.',
                    'success' => false
                ], 403);
            }

            // Clean up comma-separated numbers
            $cleanedData = $request->all();
            if (isset($cleanedData['exchange_rate'])) {
                $cleanedData['exchange_rate'] = str_replace(',', '', $cleanedData['exchange_rate']);
            }
            if (isset($cleanedData['amount'])) {
                $cleanedData['amount'] = str_replace(',', '', $cleanedData['amount']);
            }

            // Replace request data with cleaned data
            $request->merge($cleanedData);

            // Validation
            $validator = Validator::make($request->all(), [
                'number' => 'required|string|max:255|unique:debit_notes,number,' . $id,
                'contract_id' => 'required|exists:contracts,id',
                'billing_address_id' => 'required|exists:billing_addresses,id',
                'date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:date',
                'currency' => 'required|string|max:3',
                'exchange_rate' => 'required|numeric|min:0',
                'installment' => 'required|integer|max:12',
                'amount' => 'required|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'success' => false
                ], 422);
            }

            DB::beginTransaction();
            
            // Get contact_id from billing_address
            $billingAddress = \App\Models\BillingAddress::findOrFail($request->billing_address_id);
            
            // Update Debit Note
            $debitNote->update([
                'number' => $request->number,
                'contact_id' => $billingAddress->contact_id,
                'contract_id' => $request->contract_id,
                'billing_address_id' => $request->billing_address_id,
                'date' => $request->date,
                'due_date' => $request->due_date,
                'currency_code' => $request->currency,
                'exchange_rate' => $request->exchange_rate,
                'amount' => $request->amount,
                'description' => $request->description,
                'installment' => $request->installment,
                'updated_by' => auth()->id(),
            ]);

            // Update Debit Note Details if provided
            if (isset($request->details) && is_array($request->details)) {
                // Delete existing details
                $debitNote->debitNoteDetails()->delete();
                
                // Create new details
                foreach ($request->details as $detail) {
                    DebitNoteDetail::create([
                        'debit_note_id' => $debitNote->id,
                        'item_description' => $detail['item_description'],
                        'amount' => $detail['amount'],
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Debit Note updated successfully',
                'success' => true,
                'data' => new DebitNoteResource($debitNote->fresh())
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to update Debit Note: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function postDebitNote($id)
    {
        try {
            $debitNote = DebitNote::with(['debitNoteBillings', 'contract'])->findOrFail($id);
            // Check if already posted
            if ($debitNote->is_posted) {
                return response()->json([
                    'message' => 'Debit Note sudah di-posting sebelumnya',
                    'success' => false
                ], 400);
            }
        
            // Check if status is active
            if ($debitNote->status !== 'active') {
                return response()->json([
                    'message' => 'Debit Note tidak aktif dan tidak dapat di-posting',
                    'success' => false
                ], 400);
            }

            // Execute posting
            $success = $debitNote->postDebitNote();
            
            if ($success) {
                return response()->json([
                    'message' => 'Debit Note berhasil di-posting. Cashout telah dibuat otomatis.',
                    'success' => true,
                    'data' => [
                        'debit_note' => new DebitNoteResource($debitNote->fresh()),
                        'cashouts_count' => $debitNote->cashouts()->count()
                    ]
                ]);
            } else {
                return response()->json([
                    'message' => 'Gagal posting Debit Note. Pastikan status masih active.',
                    'success' => false
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function submitForApproval($id)
    {
        try {
            $debitNote = DebitNote::findOrFail($id);
            
            if (!$debitNote->canBeSubmittedForApproval()) {
                return response()->json([
                    'message' => 'Debit Note cannot be submitted for approval in current status.'
                ], 400);
            }

            $debitNote->update([
                'approval_status' => 'pending',
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Debit Note has been submitted for approval successfully.',
                'data' => new DebitNoteResource($debitNote->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(CreditNoteApprovalRequest $request, $id)
    {
        try {
            // Check if user has approver role
            if (!auth()->user()->canApproveCreditNotes()) {
                return response()->json([
                    'message' => 'You are not authorized to approve Debit Notes. Only users with approver role can perform this action.'
                ], 403);
            }

            $debitNote = DebitNote::findOrFail($id);
            
            if (!$debitNote->canBeApproved()) {
                return response()->json([
                    'message' => 'Debit Note cannot be approved in current status.'
                ], 400);
            }

            $debitNote->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $request->input('notes'),
            ]);

            return response()->json([
                'message' => 'Debit Note has been approved successfully.',
                'data' => new DebitNoteResource($debitNote->fresh())
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
            // Check if user has approver role
            if (!auth()->user()->canApproveCreditNotes()) {
                return response()->json([
                    'message' => 'You are not authorized to reject Debit Notes. Only users with approver role can perform this action.'
                ], 403);
            }

            $debitNote = DebitNote::findOrFail($id);
            
            if (!$debitNote->canBeApproved()) {
                return response()->json([
                    'message' => 'Debit Note cannot be rejected in current status.'
                ], 400);
            }

            $debitNote->update([
                'approval_status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $request->input('notes', 'Rejected'),
            ]);

            return response()->json([
                'message' => 'Debit Note has been rejected.',
                'data' => new DebitNoteResource($debitNote->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
