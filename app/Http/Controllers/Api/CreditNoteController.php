<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditNoteApprovalRequest;
use App\Http\Requests\CreditNoteStoreRequest;
use App\Http\Resources\CreditNoteResource;
use App\Models\CreditNote;
use App\Models\DebitNoteBilling;
use Illuminate\Http\Request;
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
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('transaction.credit-notes.show', $b->id) . '" class="btn btn-sm btn-outline-primary">View</a>';
                
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
        $newNumber = 'BIB/C' . date('y') . '/' . str_pad(CreditNote::count() + 1, 4, '0', STR_PAD_LEFT);
        return response()->json(['number' => $newNumber]);
    }

    public function store(CreditNoteStoreRequest $request)
    {
        try {
            $request->validated();
            $data = $request->all();
            $contractID = DebitNoteBilling::find($data['billing_id'])->debitNote->contract_id ?? null;
            $debitNoteID = DebitNoteBilling::find($data['billing_id'])->debitNote->id ?? null;
            $creditNote = CreditNote::create([
                'contract_id' => $contractID,
                'debit_note_id' => $debitNoteID,
                'number' => $data['number'],
                'date' => $data['date'],
                'description' => $data['description'],
                'currency_code' => $data['currency_code'],
                'exchange_rate' => $data['exchange_rate'],
                'amount' => $data['amount'],
                'status' => $data['status'],
                'approval_status' => 'pending', // Default approval status for new credit notes
                'billing_id' => $data['billing_id'],
                'created_by' => auth()->id(),
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
            // Check if user has approver role
            if (!auth()->user()->canApproveCreditNotes()) {
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
                'approved_by' => auth()->id(),
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
            // Check if user has approver role
            if (!auth()->user()->canApproveCreditNotes()) {
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
                'approved_by' => auth()->id(),
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
}
