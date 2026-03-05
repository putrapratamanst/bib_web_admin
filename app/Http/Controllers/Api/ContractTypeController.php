<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\QueryException;

class ContractTypeController extends Controller
{
    public function index()
    {
        $contractTypes = ContractType::orderBy('name', 'asc')
            ->get(['id', 'code', 'name']);
        
        return response()->json($contractTypes);
    }

    public function datatables()
    {
        $contractTypes = ContractType::orderBy('name', 'asc')
            ->select(['id', 'code', 'name']);

        return DataTables::of($contractTypes)->make(true);
    }

    public function show($id)
    {
        $contractType = ContractType::findOrFail($id);

        return response()->json($contractType);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:contract_types,code',
            'name' => 'required|string|max:50',
        ]);

        $contractType = ContractType::create($validated);

        return response()->json([
            'message' => 'Contract type created successfully.',
            'data' => $contractType,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $contractType = ContractType::findOrFail($id);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('contract_types', 'code')->ignore($contractType->id),
            ],
            'name' => 'required|string|max:50',
        ]);

        $contractType->update($validated);

        return response()->json([
            'message' => 'Contract type updated successfully.',
            'data' => $contractType,
        ]);
    }

    public function destroy($id)
    {
        $contractType = ContractType::findOrFail($id);

        try {
            $contractType->delete();

            return response()->json([
                'message' => 'Contract type deleted successfully.'
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Contract type cannot be deleted because it is already used in contract data.'
            ], 422);
        }
    }

    public function select2(Request $request)
    {
        $search = $request->get('search');
        
        $query = ContractType::orderBy('name', 'asc');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        
        $contractTypes = $query->limit(50)->get(['id', 'code', 'name']);
        
        $data = $contractTypes->map(function ($type) {
            return [
                'id' => $type->id,
                'text' => ($type->code ? $type->code . ' - ' : '') . $type->name,
            ];
        });
        
        return response()->json($data);
    }
}