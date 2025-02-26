<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractStoreRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContractController extends Controller
{
    public function index()
    {
        $contract = Contract::orderBy('id', 'asc')->get();

        return ContractResource::collection($contract);
    }

    public function datatables(Request $request)
    {
        $query = Contract::query();

        return DataTables::of($query)
            ->orderColumn('number', function($query, $order) {
                $query->orderBy('number', $order);
            })
            ->addColumn('contract_type', function(Contract $c) {
                return $c->contractType->name;
            })
            ->addColumn('contact', function(Contract $c) {
                return $c->contact->display_name;
            })
            ->filter(function($query) use ($request) {
                if ($request->has('contract_type') && $request->contract_type != '') {
                    $query->where('contract_type_id', $request->contract_type);
                }

                $searchValue = $request->get('search')['value'] ?? null;
                if ($searchValue) {
                    $query->where('policy_number', 'like', "%{$searchValue}%");
                }
            })
            ->make(true);
    }

    public function store(ContractStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $contract = Contract::create([
                'contract_status' => $data['contract_status'],
                'contract_type_id' => $data['contract_type_id'],
                'number' => $data['number'],
                'policy_number' => $data['policy_number'],
                'contact_id' => $data['contact_id'],
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'currency_code' => $data['currency_code'],
                'exchange_rate' => $data['exchange_rate'],
                'coverage_amount' => $data['coverage_amount'],
                'gross_premium' => $data['gross_premium'],
                'discount' => $data['discount'],
                'stamp_fee' => $data['stamp_fee'],
                'amount' => $data['amount'],
                'installment_count' => $data['installment_count'],
                'memo' => $data['memo'],
                'status' => 'active',
                // 'created_by' => auth()->id()
            ]);

            // check if details is not empty
            if (!empty($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $contract->details()->create([
                        'insurance_id' => $detail['insurance_id'],
                        'description' => $detail['description'],
                        'percentage' => $detail['percentage']
                    ]);
                }
            }

            return response()->json([
                'message' => 'Data has been created',
                'data' => new ContractResource($contract)
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
