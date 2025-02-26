<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountCategoryResource;
use App\Models\AccountCategory;
use Illuminate\Http\Request;

class AccountCategoryController extends Controller
{
    public function index()
    {
        $accountCategories = AccountCategory::orderBy('id', 'asc')->get();

        return AccountCategoryResource::collection($accountCategories);
    }

    public function select2(Request $request)
    {
        $search = $request->q;

        $accountCategories = AccountCategory::where('name', 'like', "%$search%")
            ->orderBy('id', 'asc')
            ->get();

        $formattedUsers = $accountCategories->map(function ($d) {
            return ['id' => $d->id, 'text' => $d->name];
        });

        return response()->json([
            'items' => $formattedUsers
        ]);
    }
}
