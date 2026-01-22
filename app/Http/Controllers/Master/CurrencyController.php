<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CurrencyController extends Controller
{
    public function index()
    {
        return view('master.currency.index');
    }

    public function datatables(Request $request)
    {
        $currencies = Currency::orderBy('code', 'asc');

        return DataTables::of($currencies)
            ->addColumn('actions', function (Currency $currency) {
                return view('master.currency.partials.actions', compact('currency'));
            })
            ->make(true);
    }

    public function create()
    {
        return view('master.currency.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code',
            'name' => 'required|string|max:100',
        ]);

        Currency::create($request->only(['code', 'name']));

        return redirect()->route('master.currencies.index')->with('success', 'Currency created successfully.');
    }

    public function show(Currency $currency)
    {
        return view('master.currency.show', compact('currency'));
    }

    public function edit(Currency $currency)
    {
        return view('master.currency.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->code . ',code',
            'name' => 'required|string|max:100',
        ]);

        $currency->update($request->only(['code', 'name']));

        return redirect()->route('master.currencies.index')->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return redirect()->route('master.currencies.index')->with('success', 'Currency deleted successfully.');
    }
}