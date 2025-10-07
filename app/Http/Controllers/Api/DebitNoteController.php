<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DebitNoteResource;
use App\Models\DebitNote;
use Illuminate\Http\Request;
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
        $query = DebitNote::query();

        return DataTables::of($query)
            ->addColumn('contract', function(DebitNote $b) {
                return $b->contract->number;
            })
            ->addColumn('is_posted', function(DebitNote $b) {
                return $b->is_posted ? 'Yes' : 'No';
            })
            ->make(true);
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
            $debitNote = DebitNote::with('debitNoteDetails')->findOrFail($id);
            
            // Check if already posted
            if ($debitNote->is_posted) {
                return response()->json([
                    'message' => 'Debit Note sudah di-posting sebelumnya',
                    'success' => false
                ], 400);
            }
            // Check if has debit note details
            if ($debitNote->debitNoteDetails->isEmpty()) {
                return response()->json([
                    'message' => 'Debit Note belum memiliki detail insurance. Tambahkan detail terlebih dahulu.',
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
