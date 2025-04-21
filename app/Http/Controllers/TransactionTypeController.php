<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionTypeRequest;
use App\Http\Requests\UpdateTransactionTypeRequest;
use App\Models\TransactionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionTypeController extends Controller
{
    /**
     * Affiche la liste des types de transactions.
     *
     * @return View La vue affichant la liste des types de transactions
     */
    public function index() : View
    {
        return view('transaction-types.index', [
            'transaction_types' => TransactionType::all()
        ]);
    }

    /**
     * Affiche le formulaire de création d'un nouveau type de transaction.
     * 
     * @return View La vue du formulaire de création
     */
    public function create() : View
    {
        return view('transaction-types.create');
    }

    /**
     * Stocke un nouveau type de transaction dans la base de données.
     *
     * @param StoreTransactionTypeRequest $request La requête validée contenant les données du type
     * @return RedirectResponse Redirection vers la liste des types avec un message de succès
     */
    public function store(StoreTransactionTypeRequest $request) : RedirectResponse
    {
        TransactionType::create($request->all());

        return redirect()->route('transaction-types.index')
            ->with('success', 'Type de transaction créé avec succès.');
    }

    /**
     * Affiche le formulaire de modification d'un type de transaction.
     * 
     * @param TransactionType $transaction_type Le type de transaction à modifier
     * @return View La vue du formulaire de modification
     */
    public function edit(TransactionType $transaction_type) : View
    {
        return view('transaction-types.edit', compact('transaction_type'));
    }

    /**
     * Met à jour un type de transaction existant dans la base de données.
     *
     * @param UpdateTransactionTypeRequest $request La requête validée contenant les nouvelles données
     * @param TransactionType $transactionType Le type de transaction à mettre à jour
     * @return RedirectResponse Redirection vers la liste des types avec un message de succès
     */
    public function update(UpdateTransactionTypeRequest $request, TransactionType $transactionType) : RedirectResponse
    {
        $transactionType->update($request->only('name', 'description', 'is_credit'));

        return redirect()->route('transaction-types.index')
            ->with('success', 'Type de transaction mis à jour avec succès.');
    }

    /**
     * Supprime un type de transaction de la base de données.
     *
     * @param TransactionType $transactionType Le type de transaction à supprimer
     * @return RedirectResponse Redirection vers la liste des types avec un message de succès
     */
    public function destroy(TransactionType $transactionType) : RedirectResponse
    {
        if ($transactionType->transactions()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce type car il est utilisé dans des transactions.');
        }

        $transactionType->delete();

        return redirect()->route('transaction-types.index')
            ->with('success', 'Type de transaction supprimé avec succès.');
    }
}
