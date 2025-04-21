<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the currencies.
     */
    public function index() : View
    {
        return view('currencies.index', [
            'currencies' => Currency::all()
        ]);
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
    public function store(StoreCurrencyRequest $request) : RedirectResponse
    {
        Currency::create($request->only('name', 'code', 'symbol'));

        return redirect()->route('currencies.index')
            ->with('success', 'Devise créée avec succès.');
    }

    /**
     * Show the form for editing the specified currency.
     */
    public function edit(Currency $currency) : View
    {
        return view('currencies.edit', compact('currency'));
    }

    /**
     * Update the specified currency in storage.
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency): RedirectResponse
    {
        $currency->update($request->only('name', 'code', 'symbol'));

        return redirect()->route('currencies.index')
            ->with('success', 'Devise mise à jour avec succès.');
    }

    /**
     * Remove the specified currency from storage.
     */
    public function destroy(Currency $currency): RedirectResponse
    {
        if ($currency->transactions()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette devise car elle est utilisée dans des transactions.');
        }

        $currency->delete();

        return redirect()->route('currencies.index')
            ->with('success', 'Devise supprimée avec succès.');
    }
}
