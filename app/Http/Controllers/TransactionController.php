<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Site;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request) : View
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

        // Filtrer par site si l'utilisateur n'est pas admin
        if (!Auth::user()->isAdmin()) {
            $site = Site::where('person_id', Auth::user()->person_id)->firstOrFail();
            $query->where('site_id', $site->id);
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
    public function create() : View
    {
        // Récupérer les sites selon le rôle de l'utilisateur
        $sites = Auth::user()->isAdmin()
            ? Site::all()
            : Site::where('person_id', Auth::user()->person_id)->get();

        $transactionTypes = TransactionType::all();
        $currencies = Currency::all();

        return view('transactions.create', compact('sites', 'transactionTypes', 'currencies'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $site = Site::findOrFail($request->input('site_id'));

        // Vérifier si l'utilisateur a le droit d'ajouter une transaction pour ce site
        if (!Auth::user()->isAdmin() && Auth::user()->person_id != $site->person_id) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas l\'autorisation d\'ajouter une transaction pour ce site.');
        }

        Transaction::create(array_merge(
            $request->except('_token'),
            [
                'user_id' => Auth::id(),
                'date' => now()
            ]
        ));

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction créée avec succès.');
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction) : View
    {
        $transaction->load(['site', 'type', 'currency', 'user']);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction) : View|RedirectResponse
    {
        // Vérifier si l'utilisateur a le droit de modifier cette transaction
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('transactions.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de modifier cette transaction.');
        }

        $transactionTypes = TransactionType::all();
        $currencies = Currency::all();
        $sites = Site::all();

        return view('transactions.edit', compact('transaction', 'transactionTypes', 'currencies', 'sites'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction) : RedirectResponse
    {
        // Vérifier si l'utilisateur a le droit de modifier cette transaction
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('transactions.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de modifier cette transaction.');
        }

        $transaction->update($request->only('site_id', 'transaction_type_id', 'currency_id', 'amount', 'description'));

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction mise à jour avec succès.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction) : RedirectResponse
    {
        // Vérifier si l'utilisateur a le droit de supprimer cette transaction
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('transactions.index')
                ->with('error', 'Vous n\'avez pas l\'autorisation de supprimer cette transaction.');
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction supprimée avec succès.');
    }
}
