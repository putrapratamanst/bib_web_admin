<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreditNoteStoreRequest;
use App\Http\Resources\CreditNoteResource;
use App\Models\CreditNote;
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
        $query = CreditNote::query();

        return DataTables::of($query)
            ->addColumn('contract_number', function(CreditNote $b) {
                return $b->contract->number;
            })
            ->addColumn('contract_id', function(CreditNote $b) {
                return $b->contract->id;
            })
            ->addColumn('contact', function(CreditNote $b) {
                return $b->contract->contact->display_name;
            })
            ->orderColumn('contact', function ($query, $order) {
                $query
                    ->join('contracts', 'contracts.id', '=', 'credit_notes.contract_id')
                    ->join('contacts', 'contacts.id', '=', 'contracts.contact_id')
                    ->orderBy('contacts.display_name', $order);
            })
            ->make(true);
    }

    public function store(CreditNoteStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $creditNote = CreditNote::create([
                'contract_id' => $data['contract_id'],
                'debit_note_id' => $data['debit_note_id'] ?? null,
                'number' => $data['number'],
                'date' => $data['date'],
                'description' => $data['description'],
                'currency_code' => $data['currency_code'],
                'exchange_rate' => $data['exchange_rate'],
                'amount' => $data['amount'],
                'status' => $data['status']
            ]);

            return response()->json([
                'message' => 'Data has been created',
                'data' => new CreditNoteResource($creditNote)
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
