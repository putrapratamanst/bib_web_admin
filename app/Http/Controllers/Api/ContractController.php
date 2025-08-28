<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractStoreRequest;
use App\Http\Resources\ContractResource;
use App\Models\AutoMobileUnit;
use App\Models\Contract;
use App\Models\PropertyUnit;
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
            ->orderColumn('number', function ($query, $order) {
                $query->orderBy('number', $order);
            })
            ->addColumn('contract_type', function (Contract $c) {
                return $c->contractType->name;
            })
            ->addColumn('contact', function (Contract $c) {
                return $c->contact->display_name;
            })
            ->filter(function ($query) use ($request) {
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
                'covered_item' => $data['covered_item'],
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function storeAutomobileUnit(Request $request, Contract $contract)
    {
        $request->validate([
            'units' => 'required|array',
            'units.*.no_polisi' => 'required|string',
            'units.*.merk' => 'required|string',
            'units.*.tahun' => 'required|string',
            'units.*.no_rangka' => 'required|string',
            'units.*.no_mesin' => 'required|string',
            'units.*.penggunaan' => 'required|string',
            'units.*.total' => 'required|numeric',
            'units.*.valuta' => 'nullable|string',
            'units.*.cover' => 'nullable|string',
            'units.*.discount' => 'nullable|numeric',
            'units.*.rate' => 'nullable|numeric',
            'units.*.brokerage' => 'nullable|numeric',
        ]);

        foreach ($request->units as $unit) {
            AutoMobileUnit::create([
                'contract_id' => $contract->id,
                'nopolisi' => $unit['no_polisi'],
                'merk' => $unit['merk'],
                'tahun' => $unit['tahun'],
                'norangka' => $unit['no_rangka'],
                'nomesin' => $unit['no_mesin'],
                'penggunaan' => $unit['penggunaan'],
                'valuta' => $unit['valuta'] ?? 'IDR',
                'total' => $unit['total'],
                'idcover' => $unit['cover'] ?? null,
                'discount' => $unit['discount'] ?? null,
                'rate' => $unit['rate'] ?? null,
                'brokerage' => $unit['brokerage'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Automobile units successfully saved.']);
    }

    public function storePropertyUnit(Request $request, Contract $contract)
    {
        $request->validate([
            'units' => 'required|array',
            'units.*.location' => 'required|string',
            'units.*.risk_type' => 'required|string',
            'units.*.reinstallment_value_clause' => 'string',
            'units.*.nominated_loss_adjuster' => 'string',
            'units.*.discount' => 'required|string',
        ]);

        foreach ($request->units as $unit) {
            PropertyUnit::create([
                'contract_id' => $contract->id,
                'location' => $unit['location'],
                'risk_type' => $unit['risk_type'],
                'reinstallment_value_clause' => $unit['reinstallment_value_clause'] ?? 0,
                'nominated_loss_adjuster' => $unit['nominated_loss_adjuster'] ?? 0,
                'discount' => $unit['discount'] ?? 0,
            ]);
        }

        return response()->json(['message' => 'Property units successfully saved.']);
    }
}
