<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request)
    {
        // Récupérer les paramètres de filtrage
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date')) 
            : Carbon::now()->subMonths(6)->startOfMonth();
        
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date')) 
            : Carbon::now()->endOfMonth();
        
        $selectedCurrency = $request->input('currency', 'all');
        
        // Construire la requête de base
        $query = Transaction::with(['type', 'site', 'currency', 'user'])
            ->whereBetween('date', [$startDate, $endDate]);
        
        // Filtrer par devise si nécessaire
        if ($selectedCurrency !== 'all') {
            $query->whereHas('currency', function($q) use ($selectedCurrency) {
                $q->where('code', $selectedCurrency);
            });
        }
        
        // Récupérer les transactions
        $transactions = $query->orderBy('date', 'desc')->paginate(10);
        
        // Récupérer toutes les devises pour le filtre
        $currencies = Currency::all();
        
        return view('transactions.index', compact(
            'transactions',
            'startDate',
            'endDate',
            'selectedCurrency',
            'currencies'
        ));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create()
    {
        $sites = Site::all();
        $transactionTypes = TransactionType::all();
        $currencies = Currency::all();

        return view('transactions.create', compact('sites', 'transactionTypes', 'currencies'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string'
        ]);

        $validated['user_id'] = Auth::id();

        Transaction::create($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction créée avec succès.');
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['site', 'type', 'currency', 'user']);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction)
    {
        $sites = Site::all();
        $transactionTypes = TransactionType::all();
        $currencies = Currency::all();

        return view('transactions.edit', compact('transaction', 'sites', 'transactionTypes', 'currencies'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string'
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction mise à jour avec succès.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction supprimée avec succès.');
    }
}
