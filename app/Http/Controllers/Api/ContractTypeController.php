<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Illuminate\Http\Request;

class ContractTypeController extends Controller
{
    public function index()
    {
        $contractTypes = ContractType::orderBy('name', 'asc')->get();
        
        return response()->json($contractTypes);
    }

    public function select2(Request $request)
    {
        $search = $request->get('search');
        
        $query = ContractType::orderBy('name', 'asc');
        
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        $contractTypes = $query->limit(50)->get();
        
        $data = $contractTypes->map(function ($type) {
            return [
                'id' => $type->id,
                'text' => $type->name,
            ];
        });
        
        return response()->json($data);
    }
}