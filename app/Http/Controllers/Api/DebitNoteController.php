<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $query = DebitNote::query()->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addColumn('contract', function(DebitNote $b) {
                return $b->contract->number;
            })
            ->addColumn('is_posted', function(DebitNote $b) {
                return $b->is_posted ? 'Yes' : 'No';
            })
            ->make(true);
    }

    public function store(Request $request)
    {
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
            'contact_id' => 'required|exists:contacts,id',
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
            
            // Generate debit note number dengan format BIB/D24/08-2384
            $newNumber = 'BIB/D' . date('y') . '/' . str_pad(DebitNote::count() + 1, 4, '0', STR_PAD_LEFT);
            
            // Create Debit Note
            $debitNote = DebitNote::create([
                'number' => $newNumber,
                'contact_id' => $request->contact_id,
                'contract_id' => $request->contract_id,
                'billing_address_id' => $request->billing_address_id,
                'date' => $request->date,
                'due_date' => $request->due_date,
                'currency_code' => $request->currency,
                'exchange_rate' => $request->exchange_rate,
                'amount' => $request->amount,
                'description' => $request->description,
                'status' => 'active',
                'installment' => $request->installment,
                'created_at' => $request->created_at ? \Carbon\Carbon::parse($request->created_at) : now(),
                'updated_at' => now(),
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
        $debitNote = DebitNote::with(['contract', 'debitNoteDetails', 'cashouts'])->findOrFail($id);
        
        return response()->json([
            'data' => new DebitNoteResource($debitNote)
        ]);
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
}
