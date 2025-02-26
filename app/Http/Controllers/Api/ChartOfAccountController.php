<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoaStoreRequest;
use App\Http\Resources\ChartOfAccountResource;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        $chartOfAccounts = ChartOfAccount::where('name', 'like', "%".request('q')."%")
            ->orWhere('code', 'like', "%".request('q')."%")
            ->orderBy('id', 'asc')
            ->paginate(10);

        return ChartOfAccountResource::collection($chartOfAccounts);
    }

    public function datatables()
    {
        $chartOfAccount = ChartOfAccount::with('accountCategory')
            ->orderBy('code', 'asc')->get();

        return DataTables::of($chartOfAccount)
                ->addColumn('category', function(ChartOfAccount $c) {
                    return 'Hi ' . $c->name . '!';
                })
                ->make(true);
    }

    public function select2(Request $request)
    {
        $q = $request->q;
        $c = $request->c;

        $chartOfAccounts = ChartOfAccount::where('name', 'like', "%$q%")
            ->orWhere('code', 'like', "%$q%")
            ->orderBy('id', 'asc')
            ->get();

        if ($c) {
            $chartOfAccounts = $chartOfAccounts->where('account_category_id', $c);
        }

        $formattedCoa = $chartOfAccounts->map(function ($d) {
            return ['id' => $d->id, 'text' => $d->display_name];
        });

        return response()->json([
            'items' => $formattedCoa
        ]);
    }

    public function store(CoaStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $chartOfAccount = ChartOfAccount::create($data);

            return response()->json([
                'message' => 'Data has been created',
                'data' => new ChartOfAccountResource($chartOfAccount)
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
