<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JournalEntryStoreRequest;
use App\Http\Resources\JournalEntryResource;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JournalEntryController extends Controller
{
    public function index()
    {
        $journalEntries = JournalEntry::where('number', 'like', "%".request('q')."%")
            ->orWhere('reference', 'like', "%".request('q')."%")
            ->orderBy('id', 'asc')
            ->paginate(10);

        return JournalEntryResource::collection($journalEntries);
    }

    public function datatables()
    {
        $journalEntry = JournalEntry::with('details.chartOfAccount')
            ->orderBy('entry_date', 'desc')->get();

        return DataTables::of($journalEntry)
                ->make(true);
    }

    public function store(JournalEntryStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $formattedDate = Carbon::createFromFormat('d-m-Y', $data['entry_date'])->setTime(14, 33, 0)->format('Y-m-d H:i:s');

            $journalEntries = JournalEntry::create([
                'number' => $data['number'],
                'entry_date' => $formattedDate,
                'reference' => $data['reference'],
                'description' => $data['description'],
                'status' => $data['status'],
                // 'created_by' => auth()->id(),
            ]);

            // check if details is not empty
            if (!empty($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $journalEntries->details()->create([
                        'chart_of_account_id' => $detail['chart_of_account_id'],
                        'debit' => $detail['debit'],
                        'credit' => $detail['credit'],
                        'description' => $detail['description'],
                    ]);
                }
            }

            return response()->json([
                'message' => 'Data has been created',
                'data' => new JournalEntryResource($journalEntries)
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
