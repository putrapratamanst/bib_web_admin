<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cashout;
use App\Models\DebitNote;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CashoutController extends Controller
{
    public function index()
    {
        $cashouts = Cashout::with(['debitNote', 'insurance'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $cashouts
        ]);
    }

    public function datatables(Request $request)
    {
        $query = Cashout::with(['debitNote.contract.contact', 'insurance'])
            ->select('cashouts.*');

        return DataTables::of($query)
            ->addColumn('debit_note_number', function(Cashout $cashout) {
                return $cashout->debitNote->number ?? '-';
            })
            ->addColumn('contract_number', function(Cashout $cashout) {
                return $cashout->debitNote->contract->number ?? '-';
            })
            ->addColumn('client_name', function(Cashout $cashout) {
                return $cashout->debitNote->contract->contact->display_name ?? '-';
            })
            ->addColumn('insurance_name', function(Cashout $cashout) {
                return $cashout->insurance->display_name ?? '-';
            })
            ->addColumn('date_display', function(Cashout $cashout) {
                return $cashout->date_formatted;
            })
            ->addColumn('due_date_display', function(Cashout $cashout) {
                return $cashout->due_date_formatted;
            })
            ->addColumn('amount_display', function(Cashout $cashout) {
                return $cashout->currency_code . ' ' . $cashout->amount_formatted;
            })
            ->addColumn('status_badge', function(Cashout $cashout) {
                $badgeClass = match($cashout->status) {
                    'pending' => 'bg-warning',
                    'paid' => 'bg-success',
                    'cancelled' => 'bg-danger',
                    default => 'bg-secondary'
                };
                return "<span class='badge {$badgeClass}'>" . ucfirst($cashout->status) . "</span>";
            })
            ->addColumn('action', function(Cashout $cashout) {
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<button type="button" class="btn btn-sm btn-outline-primary" onclick="viewCashout(\'' . $cashout->id . '\')" title="View">';
                $actions .= '<i class="fas fa-eye"></i>';
                $actions .= '</button>';
                
                if ($cashout->status === 'pending') {
                    $actions .= '<button type="button" class="btn btn-sm btn-outline-success" onclick="markAsPaid(\'' . $cashout->id . '\')" title="Mark as Paid">';
                    $actions .= '<i class="fas fa-check"></i>';
                    $actions .= '</button>';
                    $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="markAsCancelled(\'' . $cashout->id . '\')" title="Cancel">';
                    $actions .= '<i class="fas fa-times"></i>';
                    $actions .= '</button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->orderColumn('date_display', function ($query, $order) {
                return $query->orderBy('date', $order);
            })
            ->orderColumn('due_date_display', function ($query, $order) {
                return $query->orderBy('due_date', $order);
            })
            ->orderColumn('amount_display', function ($query, $order) {
                return $query->orderBy('amount', $order);
            })
            ->filterColumn('debit_note_number', function($query, $keyword) {
                $query->whereHas('debitNote', function($q) use ($keyword) {
                    $q->where('number', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('insurance_name', function($query, $keyword) {
                $query->whereHas('insurance', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $cashout = Cashout::with(['debitNote', 'insurance'])->findOrFail($id);
        
        return response()->json([
            'data' => $cashout
        ]);
    }

    public function update(Request $request, $id)
    {
        $cashout = Cashout::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled',
            'description' => 'nullable|string'
        ]);

        $cashout->update($request->only(['status', 'description']));

        return response()->json([
            'message' => 'Cashout updated successfully',
            'data' => $cashout
        ]);
    }

    public function markAsPaid($id)
    {
        $cashout = Cashout::findOrFail($id);
        
        $cashout->update([
            'status' => 'paid',
            'updated_by' => 1 // Default user ID
        ]);

        return response()->json([
            'message' => 'Cashout marked as paid',
            'data' => $cashout
        ]);
    }

    public function markAsCancelled($id)
    {
        $cashout = Cashout::findOrFail($id);
        
        $cashout->update([
            'status' => 'cancelled',
            'updated_by' => 1 // Default user ID
        ]);

        return response()->json([
            'message' => 'Cashout cancelled',
            'data' => $cashout
        ]);
    }
}
