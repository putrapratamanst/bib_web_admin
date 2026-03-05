<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ContractType;

class ContractTypeController extends Controller
{
    public function index()
    {
        return view('master.contract-type.index');
    }

    public function create()
    {
        return view('master.contract-type.create');
    }

    public function edit($id)
    {
        $contractType = ContractType::findOrFail($id);

        return view('master.contract-type.edit', compact('contractType'));
    }
}
