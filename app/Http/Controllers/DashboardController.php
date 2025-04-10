<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Currency;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Définir la période par défaut (6 derniers mois)
        $endDate = now();
        $startDate = $endDate->copy()->subMonths(6);

        // Récupérer les dates de la requête si présentes
        if ($request->has(['start_date', 'end_date'])) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        }

        // Récupérer la devise sélectionnée
        $selectedCurrency = $request->get('currency', 'EUR');

        // Récupérer toutes les transactions de la période
        $transactions = Transaction::with(['type', 'site', 'currency'])
            ->whereBetween('date', [$startDate, $endDate])
            ->when($selectedCurrency !== 'all', function ($query) use ($selectedCurrency) {
                return $query->whereHas('currency', function($q) use ($selectedCurrency) {
                    $q->where('code', $selectedCurrency);
                });
            })
            ->get();

        // Récupérer les types de transactions
        $transactionTypes = TransactionType::all();

        // Récupérer les sites
        $sites = Site::all();

        // Récupérer les devises disponibles
        $currencies = Currency::all();

        // Calculer les statistiques par type de transaction
        $transactionsByType = $transactions->groupBy('transaction_type_id')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                    'average' => $group->avg('amount'),
                    'type' => $group->first()->type
                ];
            });

        // Calculer les statistiques par mois
        $transactionsByMonth = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date)->format('Y-m');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'average' => $group->avg('amount')
            ];
        });

        // Calculer les statistiques par site
        $transactionsBySite = $transactions->groupBy('site_id')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                    'average' => $group->avg('amount'),
                    'site' => $group->first()->site
                ];
            });

        // Calculer les statistiques par type et site
        $transactionsByTypeAndSite = $transactions->groupBy(['transaction_type_id', 'site_id'])
            ->map(function ($typeGroup) {
                return $typeGroup->map(function ($siteGroup) {
                    return [
                        'count' => $siteGroup->count(),
                        'total' => $siteGroup->sum('amount'),
                        'average' => $siteGroup->avg('amount'),
                        'site' => $siteGroup->first()->site,
                        'type' => $siteGroup->first()->type
                    ];
                });
            });

        // Calculer le solde cumulé
        $cumulativeBalance = $transactions->sortBy('date')
            ->reduce(function ($carry, $transaction) {
                $amount = $transaction->type->is_credit ? $transaction->amount : -$transaction->amount;
                $carry[] = [
                    'date' => Carbon::parse($transaction->date)->format('Y-m-d'),
                    'balance' => ($carry[count($carry) - 1]['balance'] ?? 0) + $amount
                ];
                return $carry;
            }, []);

        // Calculer les statistiques par devise
        $transactionsByCurrency = $transactions->groupBy('currency_id')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                    'average' => $group->avg('amount')
                ];
            });

        // Calculer les moyennes mensuelles par type
        $monthlyAverages = $transactions->groupBy(['transaction_type_id', function ($transaction) {
            return Carbon::parse($transaction->date)->format('Y-m');
        }])->map(function ($group) {
            return [
                'type' => $group->first()->first()->type,
                'month' => $group->keys()->first(),
                'average' => $group->first()->avg('amount')
            ];
        })->values();

        // Calculer les tendances
        $trends = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date)->format('Y-m');
        })->map(function ($group) {
            return [
                'month' => Carbon::parse($group->first()->date)->format('Y-m'),
                'total' => $group->sum('amount'),
                'count' => $group->count()
            ];
        })->values();

        // Calculer les statistiques par type (débit/crédit)
        $transactionsByDebitCredit = $transactions->groupBy(function ($transaction) {
            return $transaction->type->is_credit ? 'credit' : 'debit';
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'average' => $group->avg('amount')
            ];
        });

        // Calculer le résumé des débits et crédits
        $debitCreditSummary = [
            'debit' => [
                'total' => $transactionsByDebitCredit['debit']['total'] ?? 0,
                'count' => $transactionsByDebitCredit['debit']['count'] ?? 0
            ],
            'credit' => [
                'total' => $transactionsByDebitCredit['credit']['total'] ?? 0,
                'count' => $transactionsByDebitCredit['credit']['count'] ?? 0
            ]
        ];

        // Calculer les statistiques par type (débit/crédit) par mois
        $debitCreditByMonth = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date)->format('Y-m');
        })->map(function ($monthGroup) {
            return $monthGroup->groupBy(function ($transaction) {
                return $transaction->type->is_credit ? 'credit' : 'debit';
            })->map(function ($typeGroup) {
                return [
                    'count' => $typeGroup->count(),
                    'total' => $typeGroup->sum('amount')
                ];
            });
        });

        // Nouveaux calculs pour les graphiques supplémentaires

        // 1. Analyse des transactions par site et type
        $transactionsBySiteAndType = $transactions->groupBy(['site_id', 'transaction_type_id'])
            ->map(function ($siteGroup) {
                return $siteGroup->map(function ($typeGroup) {
                    return [
                        'count' => $typeGroup->count(),
                        'total' => $typeGroup->sum('amount'),
                        'site' => $typeGroup->first()->site,
                        'type' => $typeGroup->first()->type
                    ];
                });
            });

        // 2. Analyse des montants moyens par type de transaction
        $averageAmountsByType = $transactions->groupBy('transaction_type_id')
            ->map(function ($group) {
                return [
                    'type' => $group->first()->type,
                    'average' => $group->avg('amount'),
                    'count' => $group->count()
                ];
            })->values();

        // 3. Analyse des transactions par jour de la semaine
        $transactionsByDayOfWeek = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date)->format('l'); // Retourne le jour de la semaine
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount')
            ];
        });

        // 4. Analyse des transactions par devise et type
        $transactionsByCurrencyAndType = $transactions->groupBy(['currency_id', 'transaction_type_id'])
            ->map(function ($currencyGroup) {
                return $currencyGroup->map(function ($typeGroup) {
                    return [
                        'count' => $typeGroup->count(),
                        'total' => $typeGroup->sum('amount'),
                        'type' => $typeGroup->first()->type
                    ];
                });
            });

        // 5. Analyse des mots-clés des descriptions
        $descriptionKeywords = $transactions->pluck('description')
            ->filter()
            ->map(function ($description) {
                // Nettoyer et diviser la description en mots
                $words = str_word_count(strtolower($description), 1, 'àáãâçéêíóôõúüñ');
                return array_count_values($words);
            })
            ->reduce(function ($carry, $wordCounts) {
                foreach ($wordCounts as $word => $count) {
                    $carry[$word] = ($carry[$word] ?? 0) + $count;
                }
                return $carry;
            }, []);

        // Trier les mots-clés par fréquence
        arsort($descriptionKeywords);
        $descriptionKeywords = array_slice($descriptionKeywords, 0, 50); // Limiter aux 50 mots les plus fréquents

        return view('dashboard.index', compact(
            'transactionsByType',
            'transactionsByMonth',
            'transactionsBySite',
            'transactionsByTypeAndSite',
            'cumulativeBalance',
            'transactionsByCurrency',
            'monthlyAverages',
            'trends',
            'transactionsByDebitCredit',
            'debitCreditSummary',
            'debitCreditByMonth',
            'transactionsBySiteAndType',
            'averageAmountsByType',
            'transactionsByDayOfWeek',
            'transactionsByCurrencyAndType',
            'descriptionKeywords',
            'startDate',
            'endDate',
            'selectedCurrency',
            'currencies'
        ));
    }
}
