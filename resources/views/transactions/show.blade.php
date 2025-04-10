@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Détails de la transaction</h3>
                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-primary">Modifier</a>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="card-title">Informations de la transaction</h5>
                        <dl class="row">
                            <dt class="col-sm-4">Date</dt>
                            <dd class="col-sm-8">{{ $transaction->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-4">Site</dt>
                            <dd class="col-sm-8">{{ $transaction->site->name }}</dd>

                            <dt class="col-sm-4">Type</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-{{ $transaction->type->is_credit ? 'success' : 'danger' }}">
                                    {{ $transaction->type->name }}
                                </span>
                            </dd>

                            <dt class="col-sm-4">Montant</dt>
                            <dd class="col-sm-8">
                                <span class="{{ $transaction->type->is_credit ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ $transaction->currency->symbol }} {{ number_format($transaction->amount, 2, ',', ' ') }}
                                </span>
                            </dd>

                            <dt class="col-sm-4">Description</dt>
                            <dd class="col-sm-8">{{ $transaction->description ?: 'Aucune description' }}</dd>

                            <dt class="col-sm-4">Créé par</dt>
                            <dd class="col-sm-8">{{ $transaction->user->username }}</dd>
                        </dl>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Retour à la liste</a>
                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')">
                                Supprimer la transaction
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 