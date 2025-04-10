<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport du Tableau de Bord</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        .summary-item {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .positive {
            color: green;
        }
        .negative {
            color: red;
        }
        .trend-up {
            color: green;
        }
        .trend-down {
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport du Tableau de Bord</h1>
        <p>Période : {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
        @if($selectedCurrency !== 'all')
            <p>Devise : {{ $selectedCurrency }}</p>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title">Résumé Débit/Crédit</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Crédits</td>
                    <td>{{ $debitCreditSummary['credit']['count'] }}</td>
                    <td class="positive">{{ number_format($debitCreditSummary['credit']['total'], 2) }}</td>
                </tr>
                <tr>
                    <td>Débits</td>
                    <td>{{ $debitCreditSummary['debit']['count'] }}</td>
                    <td class="negative">{{ number_format($debitCreditSummary['debit']['total'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Évolution mensuelle</h2>
        <table>
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Nombre de transactions</th>
                    <th>Montant total</th>
                    <th>Moyenne par transaction</th>
                    <th>Évolution</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactionsByMonth as $month => $data)
                    @php
                        $trend = $trends->firstWhere('month', $month);
                        $monthlyAverage = $monthlyAverages->firstWhere('month', $month);
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</td>
                        <td>{{ $data['count'] }}</td>
                        <td>{{ number_format($data['total'], 2) }}</td>
                        <td>{{ number_format($monthlyAverage['average'], 2) }}</td>
                        <td class="{{ $trend['percentage_change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                            {{ number_format($trend['percentage_change'], 2) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Répartition par type de transaction</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactionsByType as $type => $data)
                    <tr>
                        <td>{{ $type }}</td>
                        <td>{{ $data['count'] }}</td>
                        <td>{{ number_format($data['total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Répartition par site</h2>
        <table>
            <thead>
                <tr>
                    <th>Site</th>
                    <th>Nombre</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactionsBySite as $site => $data)
                    <tr>
                        <td>{{ $site }}</td>
                        <td>{{ $data['count'] }}</td>
                        <td>{{ number_format($data['total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Répartition par devise</h2>
        <table>
            <thead>
                <tr>
                    <th>Devise</th>
                    <th>Nombre</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactionsByCurrency as $currency => $data)
                    <tr>
                        <td>{{ $currency }}</td>
                        <td>{{ $data['count'] }}</td>
                        <td>{{ number_format($data['total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Solde cumulé</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Solde</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cumulativeBalance as $balance)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($balance['date'])->format('d/m/Y') }}</td>
                        <td class="{{ $balance['balance'] >= 0 ? 'positive' : 'negative' }}">
                            {{ number_format($balance['balance'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html> 