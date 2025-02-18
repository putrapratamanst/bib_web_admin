<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $pageSize = $request->get('pageSize', 1);

        $cashTransactions = CashTransaction::with(['client', 'bank', 'currency'])
            ->where('number', 'like', '%' . $search . '%')
            ->orWhereHas('client', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('bank', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('currency', function ($query) use ($search) {
                $query->where('code', 'like', '%' . $search . '%');
            })
            ->orderBy('number', 'desc')
            ->paginate($pageSize);

        // new data
        $cashTransactions->getCollection()->transform(function ($cashTransaction) {
            $cashTransaction->date = date('d-m-Y', strtotime($cashTransaction->date));
            $cashTransaction->type = $cashTransaction->type == 'out' ? 'Cash Out' : 'Cash In';

            return [
                // 'id' => $cashTransaction->id,
                'number' => $cashTransaction->number,
                'client' => $cashTransaction->client->name,
                'date' => $cashTransaction->date,
                'type' => $cashTransaction->type,
                'bank' => $cashTransaction->bank->name,
                'bank_account_name' => $cashTransaction->bank_account_name,
                'bank_account_number' => $cashTransaction->bank_account_number,
                'amount' => $cashTransaction->amount,
                'currency' => $cashTransaction->currency->code,
                'currency_rate' => $cashTransaction->currency_rate,
                'description' => $cashTransaction->description,
                'status' => $cashTransaction->status,
                'detail_url' => route("transaction.cash-transaction.show", $cashTransaction->id),
            ];
        });

        return response()->json($cashTransactions, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|numeric|exists:clients,id',
            'date' => 'required',
            'type' => 'required|in:in,out',
            'bank_id' => 'required|numeric|exists:banks,id',
            'bank_account_name' => 'required|max:50',
            'bank_account_number' => 'required|max:50',
            'amount' => 'required',
            'currency_id' => 'required|numeric|exists:currencies,id',
            'currency_rate' => 'required',
            'description' => 'nullable',
        ], [
            'client_id.required' => 'Client is required',
            'client_id.numeric' => 'Client must be a number',
            'client_id.exists' => 'Client is not exists',
            'date.required' => 'Date is required',
            'type.required' => 'Type is required',
            'type.in' => 'Type must be in or out',
            'bank_id.required' => 'Bank is required',
            'bank_id.numeric' => 'Bank must be a number',
            'bank_id.exists' => 'Bank is not exists',
            'bank_account_name.required' => 'Bank Account Name is required',
            'bank_account_name.max' => 'Bank Account Name maximum 50 characters',
            'bank_account_number.required' => 'Bank Account Number is required',
            'bank_account_number.max' => 'Bank Account Number maximum 50 characters',
            'amount.required' => 'Amount is required',
            'currency_id.required' => 'Currency is required',
            'currency_id.numeric' => 'Currency must be a number',
            'currency_id.exists' => 'Currency is not exists',
            'currency_rate.required' => 'Currency Rate is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $customDate = date('Ymd', strtotime($request->date));
        $request->date = date('Y-m-d', strtotime($request->date));
        $request->amount = str_replace('.', '', $request->amount);
        $request->amount = str_replace(',', '.', $request->amount);
        $request->currency_rate = str_replace('.', '', $request->currency_rate);
        $request->currency_rate = str_replace(',', '.', $request->currency_rate);

        $lastCashTransaction = CashTransaction::where('date', $request->date)->orderBy('id', 'desc')->first();
        $sequenceNumber = $lastCashTransaction == null ? 1 : (int) substr($lastCashTransaction->number, -4) + 1;
        $sequenceNumber = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        $number = 'CT-' . $customDate . '-' . $sequenceNumber;

        $save = CashTransaction::create([
            'id' => Str::uuid(),
            'client_id' => $request->client_id,
            'number' => $number,
            'date' => $request->date,
            'type' => $request->type,
            'bank_id' => $request->bank_id,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
            'amount' => $request->amount,
            'currency_id' => $request->currency_id,
            'currency_rate' => $request->currency_rate,
            'description' => $request->description,
            'status' => 'active',
        ]);

        if ($save) {
            return response()->json([
                'message' => 'Cash Transaction created successfully',
                'redirect' => route('transaction.cash-transaction.index'),
            ], 200);
        }
        
        return response()->json([
            'message' => 'Failed to create Cash Transaction',
        ], 500);
    }
}
