@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transactions</h5>
                    <div>
                        <a href="{{ route('reports.transactions', request()->query()) }}" class="btn btn-primary me-2">
                            <i class="fas fa-file-pdf"></i> Exporter en PDF
                        </a>
                        <a href="{{ route('transactions.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nouvelle transaction
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('transactions.index') }}" class="mb-4">
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

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Site</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $transaction->site->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->type->is_credit ? 'success' : 'danger' }}">
                                                {{ $transaction->type->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="{{ $transaction->type->is_credit ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($transaction->amount, 2, ',', ' ') }} {{ $transaction->currency->symbol }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($transaction->description, 50) }}</td>
                                        <td>
                                            <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-primary">
                                                Voir
                                            </a>
                                            <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-info">
                                                Modifier
                                            </a>
                                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Page navigation">
                            {{ $transactions->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 