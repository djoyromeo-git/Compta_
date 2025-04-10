<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Site;
use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Génère un rapport PDF des transactions
     */
    public function transactionsReport(Request $request)
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
        $transactions = $query->orderBy('date', 'desc')->get();
        
        // S'assurer que toutes les dates sont des objets Carbon
        $transactions->each(function($transaction) {
            $transaction->date = Carbon::parse($transaction->date);
        });
        
        // Calculer les statistiques
        $totalTransactions = $transactions->count();
        $totalAmount = $transactions->sum('amount');
        $creditAmount = $transactions->where('type.is_credit', true)->sum('amount');
        $debitAmount = $transactions->where('type.is_credit', false)->sum('amount');
        
        // Grouper par type de transaction
        $transactionsByType = $transactions->groupBy('type.name')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });
        
        // Grouper par site
        $transactionsBySite = $transactions->groupBy('site.name')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });
        
        // Grouper par devise
        $transactionsByCurrency = $transactions->groupBy('currency.code')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });
        
        // Générer le PDF
        $pdf = PDF::loadView('reports.transactions', compact(
            'transactions',
            'startDate',
            'endDate',
            'selectedCurrency',
            'totalTransactions',
            'totalAmount',
            'creditAmount',
            'debitAmount',
            'transactionsByType',
            'transactionsBySite',
            'transactionsByCurrency'
        ));
        
        return $pdf->download('rapport-transactions.pdf');
    }
    
    /**
     * Génère un rapport PDF du tableau de bord
     */
    public function dashboardReport(Request $request)
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
        $transactions = $query->get();
        
        // S'assurer que toutes les dates sont des objets Carbon
        $transactions->each(function($transaction) {
            $transaction->date = Carbon::parse($transaction->date);
        });
        
        // Calculer les statistiques pour le tableau de bord
        $transactionsByType = $transactions->groupBy('type.name')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });
        
        $transactionsByMonth = $transactions->groupBy(function($transaction) {
                return Carbon::parse($transaction->date)->format('Y-m');
            })->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });
        
        $transactionsBySite = $transactions->groupBy('site.name')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });
        
        $transactionsByCurrency = $transactions->groupBy('currency.code')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });
        
        // Calculer le solde cumulé
        $cumulativeBalance = [];
        $balance = 0;
        $sortedTransactions = $transactions->sortBy('date');
        
        foreach ($sortedTransactions as $transaction) {
            $date = Carbon::parse($transaction->date)->format('Y-m-d');
            $amount = $transaction->type->is_credit ? $transaction->amount : -$transaction->amount;
            $balance += $amount;
            
            $cumulativeBalance[] = [
                'date' => $date,
                'balance' => $balance
            ];
        }
        
        // Calculer les moyennes mensuelles
        $monthlyAverages = $transactionsByMonth->map(function($data, $month) {
            return [
                'month' => $month,
                'average' => $data['count'] > 0 ? $data['total'] / $data['count'] : 0
            ];
        })->values();
        
        // Calculer les tendances
        $trends = [];
        $previousCount = 0;
        
        foreach ($transactionsByMonth as $month => $data) {
            $percentageChange = $previousCount > 0 
                ? (($data['count'] - $previousCount) / $previousCount) * 100 
                : 0;
            
            $trends[] = [
                'month' => $month,
                'count' => $data['count'],
                'percentage_change' => $percentageChange
            ];
            
            $previousCount = $data['count'];
        }
        
        // Calculer les statistiques débit/crédit
        $debitCreditSummary = [
            'debit' => [
                'count' => $transactions->where('type.is_credit', false)->count(),
                'total' => $transactions->where('type.is_credit', false)->sum('amount')
            ],
            'credit' => [
                'count' => $transactions->where('type.is_credit', true)->count(),
                'total' => $transactions->where('type.is_credit', true)->sum('amount')
            ]
        ];
        
        // Générer le PDF
        $pdf = PDF::loadView('reports.dashboard', compact(
            'startDate',
            'endDate',
            'selectedCurrency',
            'transactionsByType',
            'transactionsByMonth',
            'transactionsBySite',
            'cumulativeBalance',
            'transactionsByCurrency',
            'monthlyAverages',
            'trends',
            'debitCreditSummary'
        ));
        
        return $pdf->download('rapport-tableau-de-bord.pdf');
    }
} 