<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use Illuminate\Http\Request;

class TransactionTypeController extends Controller
{
    /**
     * Display a listing of the transaction types.
     */
    public function index()
    {
        $transactionTypes = TransactionType::all();
        return view('transaction-types.index', compact('transactionTypes'));
    }

    /**
     * Show the form for creating a new transaction type.
     */
    public function create()
    {
        return view('transaction-types.create');
    }

    /**
     * Store a newly created transaction type in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_credit' => 'required|boolean',
            'color' => 'required|string|max:7'
        ]);

        TransactionType::create($validated);

        return redirect()->route('transaction-types.index')
            ->with('success', 'Type de transaction créé avec succès.');
    }

    /**
     * Show the form for editing the specified transaction type.
     */
    public function edit(TransactionType $transactionType)
    {
        return view('transaction-types.edit', compact('transactionType'));
    }

    /**
     * Update the specified transaction type in storage.
     */
    public function update(Request $request, TransactionType $transactionType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_credit' => 'required|boolean',
            'color' => 'required|string|max:7'
        ]);

        $transactionType->update($validated);

        return redirect()->route('transaction-types.index')
            ->with('success', 'Type de transaction mis à jour avec succès.');
    }

    /**
     * Remove the specified transaction type from storage.
     */
    public function destroy(TransactionType $transactionType)
    {
        if ($transactionType->transactions()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce type car il est utilisé dans des transactions.');
        }

        $transactionType->delete();

        return redirect()->route('transaction-types.index')
            ->with('success', 'Type de transaction supprimé avec succès.');
    }
}
