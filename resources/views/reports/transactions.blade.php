<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Transactions</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport des Transactions</h1>
        <p>Période : {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
        @if($selectedCurrency !== 'all')
            <p>Devise : {{ $selectedCurrency }}</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-item">
            <strong>Nombre total de transactions :</strong> {{ $totalTransactions }}
        </div>
        <div class="summary-item">
            <strong>Montant total :</strong> {{ number_format($totalAmount, 2) }}
        </div>
        <div class="summary-item">
            <strong>Total des crédits :</strong> <span class="positive">{{ number_format($creditAmount, 2) }}</span>
        </div>
        <div class="summary-item">
            <strong>Total des débits :</strong> <span class="negative">{{ number_format($debitAmount, 2) }}</span>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Répartition par type de transaction</h2>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                    <th>Montant total</th>
                    <th>% du total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactionsByType as $type => $data)
                    <tr>
                        <td>{{ $type }}</td>
                        <td>{{ $data['count'] }}</td>
                        <td>{{ number_format($data['total'], 2) }}</td>
                        <td>{{ number_format(($data['total'] / $totalAmount) * 100, 2) }}%</td>
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
                    <th>% du total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactionsBySite as $site => $data)
                    <tr>
                        <td>{{ $site }}</td>
                        <td>{{ $data['count'] }}</td>
                        <td>{{ number_format($data['total'], 2) }}</td>
                        <td>{{ number_format(($data['total'] / $totalAmount) * 100, 2) }}%</td>
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
                    <th>% du total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactionsByCurrency as $currency => $data)
                    <tr>
                        <td>{{ $currency }}</td>
                        <td>{{ $data['count'] }}</td>
                        <td>{{ number_format($data['total'], 2) }}</td>
                        <td>{{ number_format(($data['total'] / $totalAmount) * 100, 2) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Liste détaillée des transactions</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Site</th>
                    <th>Description</th>
                    <th>Montant</th>
                    <th>Devise</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->date->format('d/m/Y') }}</td>
                        <td>{{ $transaction->type->name }}</td>
                        <td>{{ $transaction->site->name }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td class="{{ $transaction->type->is_credit ? 'positive' : 'negative' }}">
                            {{ number_format($transaction->amount, 2) }}
                        </td>
                        <td>{{ $transaction->currency->code }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html> 