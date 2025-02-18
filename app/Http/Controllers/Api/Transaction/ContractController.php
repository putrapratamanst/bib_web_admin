<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $pageSize = $request->get('pageSize', 1);

        $contracts = Contract::with(['client', 'contractType'])
            ->where('number', 'like', '%' . $search . '%')
            ->orWhereHas('client', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('contractType', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);

        // new data
        $contracts->getCollection()->transform(function ($contract) {
            return [
                'id' => $contract->id,
                'number' => $contract->number,
                'contract_type' => $contract->contractType ? $contract->contractType->name : '',
                'client' => $contract->client->name,
                'address' => $contract->address,
                'period' => $contract->period,
                'description' => $contract->description,
                'count_of_item' => $contract->count_of_item,
                'status' => $contract->status,
                'currency' => $contract->currency->code,
                'currency_rate' => $contract->currency_rate,
                'discount' => $contract->discount,
                'gross_amount' => $contract->gross_amount,
                'nett_amount' => $contract->nett_amount,
                'nett_amount_formatted' => number_format($contract->nett_amount, 0, ',', '.'),
                'detail_url' => route("transaction.contract.show", $contract->id),
            ];
        });

        return response()->json($contracts, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_type_id' => 'required|numeric|exists:contract_types,id',
            'client_id' => 'required|exists:contacts,id',
            'period_start' => 'required|date|before:period_end',
            'period_end' => 'required|date|after:period_start',
            'count_of_item' => 'required|numeric',
            'description' => 'required',
            'currency_id' => 'required',
            'currency_rate' => 'required',
            'discount' => 'required',
            'gross_amount' => 'required',
        ], [
            'contract_type_id.required' => 'Insurance type is required.',
            'contract_type_id.exists' => 'Insurance type not found.',
            'client_id.required' => 'Client is required.',
            'client_id.numeric' => 'Client must be a number.',
            'client_id.exists' => 'Client not found.',
            'period_start.required' => 'Period start is required.',
            'period_start.date' => 'Period start must be a date.',
            'period_start.before' => 'Period start must be before period end.',
            'period_end.required' => 'Period end is required.',
            'period_end.date' => 'Period end must be a date.',
            'period_end.after' => 'Period end must be after period start.',
            'count_of_item.required' => 'Count of item is required.',
            'count_of_item.numeric' => 'Count of item must be a number.',
            'description.required' => 'Description is required.',
            'currency_id.required' => 'Currency is required.',
            'currency_rate.required' => 'Currency rate is required.',
            'discount.required' => 'Discount is required.',
            'gross_amount.required' => 'Gross amount is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $lastContract = Contract::whereDate('created_at', date('Y-m-d'))->orderBy('id', 'desc')->first();
        $sequenceNumber = $lastContract == null ? 1 : (int) substr($lastContract->number, -4) + 1;
        $sequenceNumber = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        $number = 'C-' . date('Ymd') . '-' . $sequenceNumber;

        $request->period_start = date('Y-m-d', strtotime($request->period_start));
        $request->period_end = date('Y-m-d', strtotime($request->period_end));

        $request->currency_rate = str_replace('.', '', $request->currency_rate);
        $request->currency_rate = str_replace(',', '.', $request->currency_rate);

        $request->gross_amount = str_replace('.', '', $request->gross_amount);
        $request->gross_amount = str_replace(',', '.', $request->gross_amount);

        $contract = Contract::create([
            'number' => $number,
            'contract_type_id' => $request->contract_type_id,
            'client_id' => $request->client_id,
            'address' => $request->address,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'description' => $request->description,
            'count_of_item' => $request->count_of_item,
            'status' => 'active',
            'currency_id' => $request->currency_id,
            'currency_rate' => $request->currency_rate,
            'discount' => $request->discount,
            'gross_amount' => $request->gross_amount,
        ]);

        if ($contract) {
            return response()->json([
                'message' => 'Contract created.',
                'redirect' => route('transaction.contract.index'),
            ], 200);
        }
        else {
            return response()->json([
                'message' => 'Failed to create contract.',
            ], 500);
        }
    }
}
