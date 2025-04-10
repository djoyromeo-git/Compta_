@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tableau de bord</h5>
                    <div>
                        <a href="{{ route('reports.dashboard', request()->query()) }}" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Exporter en PDF
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Date de début</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                        value="{{ request('start_date', now()->subMonths(6)->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">Date de fin</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                        value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="currency">Devise</label>
                                    <select class="form-control" id="currency" name="currency">
                                        <option value="all">Toutes les devises</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->code }}" 
                                                {{ request('currency') == $currency->code ? 'selected' : '' }}>
                                                {{ $currency->code }} - {{ $currency->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filtrer</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Résumé des transactions -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total des transactions</h6>
                                    <h3 class="mb-0">{{ $transactionsByType->sum('count') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total des crédits</h6>
                                    <h3 class="mb-0">{{ number_format($debitCreditSummary['credit']['total'], 2, ',', ' ') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total des débits</h6>
                                    <h3 class="mb-0">{{ number_format($debitCreditSummary['debit']['total'], 2, ',', ' ') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Solde</h6>
                                    <h3 class="mb-0">{{ number_format($debitCreditSummary['credit']['total'] - $debitCreditSummary['debit']['total'], 2, ',', ' ') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphiques principaux -->
                    <div class="row">
                        <!-- Graphique des transactions par mois -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Évolution mensuelle
                                </div>
                                <div class="card-body">
                                    @if(empty($transactionsByMonth))
                                        <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                        <canvas id="monthlyChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Graphique du solde cumulé -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Solde cumulé
                                </div>
                                <div class="card-body">
                                    @if(empty($cumulativeBalance))
                                    <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                    <canvas id="cumulativeBalanceChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Graphique des moyennes mensuelles -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Moyennes mensuelles par type
                                </div>
                                <div class="card-body">
                                    @if($monthlyAverages->isEmpty())
                                        <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                        <canvas id="monthlyAveragesChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Graphique des tendances -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Évolution mensuelle des transactions
                                </div>
                                <div class="card-body">
                                    @if(empty($trends))
                                    <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                    <canvas id="trendsChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Graphique des transactions par devise -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Répartition par devise
                                </div>
                                <div class="card-body">
                                    @if(empty($transactionsByCurrency))
                                    <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                    <canvas id="currencyChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Graphique de répartition débit/crédit -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Répartition Débit/Crédit
                                </div>
                                <div class="card-body">
                                    @if($transactionsByDebitCredit->isEmpty())
                                        <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                        <canvas id="debitCreditChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nouveaux graphiques -->
                    <div class="row">
                        <!-- Analyse des transactions par site et type -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Transactions par Site et Type
                                </div>
                                <div class="card-body">
                                    @if(empty($transactionsBySiteAndType))
                                        <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                        <canvas id="siteTypeChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Analyse des montants moyens par type -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Montants Moyens par Type de Transaction
                                </div>
                                <div class="card-body">
                                    @if(empty($averageAmountsByType))
                                        <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                        <canvas id="averageAmountsChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Analyse des transactions par jour de la semaine -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Transactions par Jour de la Semaine
                                </div>
                                <div class="card-body">
                                    @if(empty($transactionsByDayOfWeek))
                                        <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                        <canvas id="dayOfWeekChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Analyse des transactions par devise et type -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Transactions par Devise et Type
                                </div>
                                <div class="card-body">
                                    @if(empty($transactionsByCurrencyAndType))
                                        <p class="text-center">Aucune transaction trouvée pour la période sélectionnée.</p>
                                    @else
                                        <canvas id="currencyTypeChart"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nuage de mots des descriptions -->
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    Mots-clés des Descriptions
                                </div>
                                <div class="card-body">
                                    @if(empty($descriptionKeywords))
                                        <p class="text-center">Aucune description trouvée pour la période sélectionnée.</p>
                                    @else
                                        <div id="wordCloud" style="height: 400px;"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-cloud@1.2.5/build/d3.layout.cloud.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour les graphiques
    const transactionsByType = @json($transactionsByType);
    const transactionsByMonth = @json($transactionsByMonth);
    const transactionsBySite = @json($transactionsBySite);
    const transactionsByTypeAndSite = @json($transactionsByTypeAndSite);
    const cumulativeBalance = @json($cumulativeBalance);
    const transactionsByCurrency = @json($transactionsByCurrency);
    const monthlyAverages = @json($monthlyAverages);
    const trends = @json($trends);
    const transactionsByDebitCredit = @json($transactionsByDebitCredit);
    const debitCreditSummary = @json($debitCreditSummary);
    const debitCreditByMonth = @json($debitCreditByMonth);
    const transactionsBySiteAndType = @json($transactionsBySiteAndType);
    const averageAmountsByType = @json($averageAmountsByType);
    const transactionsByDayOfWeek = @json($transactionsByDayOfWeek);
    const transactionsByCurrencyAndType = @json($transactionsByCurrencyAndType);
    const descriptionKeywords = @json($descriptionKeywords);

    // Configuration commune pour les graphiques
    Chart.defaults.font.family = "'Figtree', sans-serif";
    Chart.defaults.color = '#666';

    // Graphique des transactions par mois
    if (transactionsByMonth && Object.keys(transactionsByMonth).length > 0) {
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            // Trier les mois chronologiquement
            const sortedMonths = Object.keys(transactionsByMonth).sort((a, b) => {
                const [yearA, monthA] = a.split('-');
                const [yearB, monthB] = b.split('-');
                return new Date(yearA, monthA - 1) - new Date(yearB, monthB - 1);
            });
            
            // Formater les labels pour l'affichage
            const formattedLabels = sortedMonths.map(month => {
                const [year, monthNum] = month.split('-');
                const date = new Date(year, monthNum - 1);
                return date.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
            });
            
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: formattedLabels,
                    datasets: [{
                        label: 'Total des transactions',
                        data: sortedMonths.map(month => transactionsByMonth[month].total),
                        backgroundColor: '#4BC0C0'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    return `Total: ${value.toLocaleString('fr-FR')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('fr-FR');
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique du solde cumulé
    if (cumulativeBalance.length > 0) {
        const balanceCtx = document.getElementById('cumulativeBalanceChart');
        if (balanceCtx) {
            new Chart(balanceCtx, {
                type: 'line',
                data: {
                    labels: cumulativeBalance.map(item => item.date),
                    datasets: [{
                        label: 'Solde cumulé',
                        data: cumulativeBalance.map(item => item.balance),
                        borderColor: '#4BC0C0',
                        tension: 0.1,
                        fill: true,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    return `Solde: ${value.toLocaleString('fr-FR')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('fr-FR');
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique des transactions par devise
    if (transactionsByCurrency && Object.keys(transactionsByCurrency).length > 0) {
        const currencyCtx = document.getElementById('currencyChart');
        if (currencyCtx) {
            const currencyData = transactionsByCurrency;
            const labels = Object.keys(currencyData).map(code => {
                const currency = @json($currencies).find(c => c.code === code);
                return currency ? `${code} - ${currency.name}` : code;
            });
            
            new Chart(currencyCtx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: Object.values(currencyData).map(item => item.total),
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                            '#FF9F40', '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value.toLocaleString('fr-FR')} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique des moyennes mensuelles
    if (monthlyAverages.length > 0) {
        const averagesCtx = document.getElementById('monthlyAveragesChart');
        if (averagesCtx) {
            new Chart(averagesCtx, {
                type: 'bar',
                data: {
                    labels: monthlyAverages.map(item => item.month),
                    datasets: [{
                        label: 'Moyenne mensuelle',
                        data: monthlyAverages.map(item => item.average),
                        backgroundColor: '#FFCE56'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    return `Moyenne: ${value.toLocaleString('fr-FR')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('fr-FR');
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique des tendances
    if (trends.length > 0) {
        const trendsCtx = document.getElementById('trendsChart');
        if (trendsCtx) {
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: trends.map(item => item.month),
                    datasets: [{
                        label: 'Nombre de transactions',
                        data: trends.map(item => item.count),
                        borderColor: '#FF6384',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique de répartition débit/crédit
    if (Object.keys(transactionsByDebitCredit).length > 0) {
        const debitCreditCtx = document.getElementById('debitCreditChart');
        if (debitCreditCtx) {
            new Chart(debitCreditCtx, {
                type: 'pie',
                data: {
                    labels: ['Débits', 'Crédits'],
                    datasets: [{
                        data: [
                            debitCreditSummary.debit.total,
                            debitCreditSummary.credit.total
                        ],
                        backgroundColor: ['#FF6384', '#36A2EB']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value.toLocaleString('fr-FR')} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique des transactions par site et type
    if (Object.keys(transactionsBySiteAndType).length > 0) {
        const siteTypeCtx = document.getElementById('siteTypeChart');
        if (siteTypeCtx) {
            const sites = [...new Set(Object.values(transactionsBySiteAndType).flatMap(group => 
                Object.values(group).map(item => item.site.name)
            ))];
            const types = [...new Set(Object.values(transactionsBySiteAndType).flatMap(group => 
                Object.values(group).map(item => item.type.name)
            ))];

            new Chart(siteTypeCtx, {
                type: 'bar',
                data: {
                    labels: sites,
                    datasets: types.map((type, index) => ({
                        label: type,
                        data: sites.map(site => {
                            const siteData = Object.values(transactionsBySiteAndType).find(group => 
                                Object.values(group).some(item => item.site.name === site)
                            );
                            if (siteData) {
                                const typeData = Object.values(siteData).find(item => 
                                    item.type.name === type
                                );
                                return typeData ? typeData.total : 0;
                            }
                            return 0;
                        }),
                        backgroundColor: `hsl(${(index * 360) / types.length}, 70%, 50%)`
                    }))
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value.toLocaleString('fr-FR')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('fr-FR');
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique des montants moyens par type
    if (averageAmountsByType && averageAmountsByType.length > 0) {
        const averageAmountsCtx = document.getElementById('averageAmountsChart');
        if (averageAmountsCtx) {
            new Chart(averageAmountsCtx, {
                type: 'bar',
                data: {
                    labels: averageAmountsByType.map(item => item.type.name),
                    datasets: [{
                        label: 'Montant moyen',
                        data: averageAmountsByType.map(item => item.average),
                        backgroundColor: averageAmountsByType.map(item => 
                            item.type.is_credit ? '#36A2EB' : '#FF6384'
                        )
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    return `Moyenne: ${value.toLocaleString('fr-FR')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('fr-FR');
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique des transactions par jour de la semaine
    if (Object.keys(transactionsByDayOfWeek).length > 0) {
        const dayOfWeekCtx = document.getElementById('dayOfWeekChart');
        if (dayOfWeekCtx) {
            const days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
            const dayOrder = {
                'Monday': 0, 'Tuesday': 1, 'Wednesday': 2, 'Thursday': 3,
                'Friday': 4, 'Saturday': 5, 'Sunday': 6
            };

            new Chart(dayOfWeekCtx, {
                type: 'line',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Nombre de transactions',
                        data: days.map(day => {
                            const dayData = Object.entries(transactionsByDayOfWeek).find(([key]) => 
                                dayOrder[key] === days.indexOf(day)
                            );
                            return dayData ? dayData[1].count : 0;
                        }),
                        borderColor: '#4BC0C0',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique des transactions par devise et type
    if (Object.keys(transactionsByCurrencyAndType).length > 0) {
        const currencyTypeCtx = document.getElementById('currencyTypeChart');
        if (currencyTypeCtx) {
            const currencies = Object.keys(transactionsByCurrencyAndType);
            const types = [...new Set(Object.values(transactionsByCurrencyAndType).flatMap(group => 
                Object.values(group).map(item => item.type.name)
            ))];

            new Chart(currencyTypeCtx, {
                type: 'bar',
                data: {
                    labels: currencies,
                    datasets: types.map((type, index) => ({
                        label: type,
                        data: currencies.map(currency => {
                            const currencyData = transactionsByCurrencyAndType[currency];
                            const typeData = Object.values(currencyData).find(item => 
                                item.type.name === type
                            );
                            return typeData ? typeData.total : 0;
                        }),
                        backgroundColor: `hsl(${(index * 360) / types.length}, 70%, 50%)`
                    }))
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value.toLocaleString('fr-FR')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('fr-FR');
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Nuage de mots des descriptions
    if (Object.keys(descriptionKeywords).length > 0) {
        const words = Object.entries(descriptionKeywords).map(([word, count]) => ({
            text: word,
            size: 10 + (count * 2)
        }));

        const width = document.getElementById('wordCloud').offsetWidth;
        const height = 400;

        d3.select('#wordCloud')
            .append('svg')
            .attr('width', width)
            .attr('height', height)
            .append('g')
            .attr('transform', `translate(${width/2},${height/2})`)
            .selectAll('text')
            .data(words)
            .enter()
            .append('text')
            .style('font-size', d => `${d.size}px`)
            .style('font-family', 'Figtree')
            .style('fill', (d, i) => `hsl(${(i * 360) / words.length}, 70%, 50%)`)
            .attr('text-anchor', 'middle')
            .attr('transform', d => `translate(${d.x},${d.y})rotate(${d.rotate})`)
            .text(d => d.text);

        d3.layout.cloud()
            .size([width, height])
            .words(words)
            .padding(5)
            .rotate(() => ~~(Math.random() * 2) * 90)
            .font('Figtree')
            .fontSize(d => d.size)
            .on('end', draw)
            .start();

        function draw(words) {
            d3.select('#wordCloud svg g')
                .selectAll('text')
                .data(words)
                .attr('transform', d => `translate(${d.x},${d.y})rotate(${d.rotate})`);
        }
    }
});
</script>
@endpush
@endsection 