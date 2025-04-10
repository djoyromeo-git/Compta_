<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the currencies.
     */
    public function index()
    {
        $currencies = Currency::all();
        return view('currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new currency.
     */
    public function create()
    {
        return view('currencies.create');
    }

    /**
     * Store a newly created currency in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:currencies',
            'code' => 'required|string|max:3|unique:currencies',
            'symbol' => 'required|string|max:10'
        ]);

        Currency::create($validated);

        return redirect()->route('currencies.index')
            ->with('success', 'Devise créée avec succès.');
    }

    /**
     * Show the form for editing the specified currency.
     */
    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    /**
     * Update the specified currency in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:currencies,name,' . $currency->id,
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->id,
            'symbol' => 'required|string|max:10'
        ]);

        $currency->update($validated);

        return redirect()->route('currencies.index')
            ->with('success', 'Devise mise à jour avec succès.');
    }

    /**
     * Remove the specified currency from storage.
     */
    public function destroy(Currency $currency)
    {
        if ($currency->transactions()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette devise car elle est utilisée dans des transactions.');
        }

        $currency->delete();

        return redirect()->route('currencies.index')
            ->with('success', 'Devise supprimée avec succès.');
    }
}
