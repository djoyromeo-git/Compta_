<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    /**
     * Affiche la liste des devises.
     *
     * @return View La vue affichant la liste des devises
     */
    public function index() : View
    {
        return view('currencies.index', [
            'currencies' => Currency::all()
        ]);
    }

    /**
     * Affiche le formulaire de création d'une nouvelle devise.
     * 
     * @return View La vue du formulaire de création
     */
    public function create()
    {
        return view('currencies.create');
    }

    /**
     * Stocke une nouvelle devise dans la base de données.
     *
     * @param StoreCurrencyRequest $request La requête validée contenant les données de la devise
     * @return RedirectResponse Redirection vers la liste des devises avec un message de succès
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
     * Met à jour une devise existante dans la base de données.
     *
     * @param UpdateCurrencyRequest $request La requête validée contenant les nouvelles données
     * @param Currency $currency La devise à mettre à jour
     * @return RedirectResponse Redirection vers la liste des devises avec un message de succès
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency): RedirectResponse
    {
        $currency->update($request->only('name', 'code', 'symbol'));

        return redirect()->route('currencies.index')
            ->with('success', 'Devise mise à jour avec succès.');
    }

    /**
     * Supprime une devise de la base de données.
     *
     * @param Currency $currency La devise à supprimer
     * @return RedirectResponse Redirection vers la liste des devises avec un message de succès
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
