<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_id' => 'required|numeric|exists:contracts,id',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'amount' => 'required',
            'description' => 'nullable',
        ], [
            'contract_id.required' => 'Contract is required',
            'contract_id.numeric' => 'Contract must be a number',
            'contract_id.exists' => 'Contract is not exists',
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a date',
            'due_date.required' => 'Due Date is required',
            'due_date.date' => 'Due Date must be a date',
            'amount.required' => 'Amount is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $customDate = date('Ymd', strtotime($request->date));
        $request->date = date('Y-m-d', strtotime($request->date));
        $request->due_date = date('Y-m-d', strtotime($request->due_date));
        $request->amount = str_replace('.', '', $request->amount);
        $request->amount = str_replace(',', '.', $request->amount);

        $lastBilling = Billing::whereDate('date', $request->date)->orderBy('id', 'desc')->first();
        $sequenceNumber = $lastBilling == null ? 1 : (int) substr($lastBilling->number, -4) + 1;
        $sequenceNumber = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        $number = 'B-' . $customDate . '-' . $sequenceNumber;

        $createBilling = Billing::create([
            'contract_id' => $request->contract_id,
            'number' => $number,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => 'unpaid',
        ]);

        if ($createBilling) {
            return response()->json([
                'message' => 'Billing created successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to create billing',
            ], 500);
        }
    }
}
