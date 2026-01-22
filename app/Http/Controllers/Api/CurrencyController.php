<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::all();
        return response()->json($currencies);
    }

    public function datatables(Request $request)
    {
        $currencies = Currency::orderBy('code', 'asc');

        return DataTables::of($currencies)->make(true);
    }

    public function select2(Request $request)
    {
        $search = $request->get('search', '');
        $currencies = Currency::where('code', 'like', "%{$search}%")
            ->orWhere('name', 'like', "%{$search}%")
            ->orderBy('code')
            ->get();

        $data = $currencies->map(function ($currency) {
            return [
                'id' => $currency->code,
                'text' => $currency->code . ' - ' . $currency->name,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code',
            'name' => 'required|string|max:100',
        ]);

        $currency = Currency::create($request->only(['code', 'name']));

        return response()->json($currency, 201);
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->code . ',code',
            'name' => 'required|string|max:100',
        ]);

        $currency->update($request->only(['code', 'name']));

        return response()->json($currency);
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return response()->json(['message' => 'Currency deleted']);
    }
}