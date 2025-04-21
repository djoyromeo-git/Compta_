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
     * Display a listing of the transaction types.
     */
    public function index() : View
    {
        return view('transaction-types.index', [
            'transaction_types' => TransactionType::all()
        ]);
    }

    /**
     * Show the form for creating a new transaction type.
     */
    public function create() : View
    {
        return view('transaction-types.create');
    }

    /**
     * Store a newly created transaction type in storage.
     */
    public function store(StoreTransactionTypeRequest $request) : RedirectResponse
    {
        TransactionType::create($request->all());

        return redirect()->route('transaction-types.index')
            ->with('success', 'Type de transaction créé avec succès.');
    }

    /**
     * Show the form for editing the specified transaction type.
     */
    public function edit(TransactionType $transaction_type)
    {
        return view('transaction-types.edit', compact('transaction_type'));
    }

    /**
     * Update the specified transaction type in storage.
     */
    public function update(UpdateTransactionTypeRequest $request, TransactionType $transaction_type) : RedirectResponse
    {
        $transaction_type->update($request->only('name', 'description', 'is_credit'));

        return redirect()->route('transaction-types.index')
            ->with('success', 'Type de transaction mis à jour avec succès.');
    }

    /**
     * Remove the specified transaction type from storage.
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
