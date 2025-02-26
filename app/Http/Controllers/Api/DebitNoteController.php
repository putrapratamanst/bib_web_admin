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
            ->make(true);
    }
}
