@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Détails du site</h3>
                    <a href="{{ route('sites.edit', $site) }}" class="btn btn-primary">Modifier</a>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="card-title">Informations générales</h5>
                        <dl class="row">
                            <dt class="col-sm-3">Nom</dt>
                            <dd class="col-sm-9">{{ $site->name }}</dd>

                            <dt class="col-sm-3">Catégorie</dt>
                            <dd class="col-sm-9">{{ $site->category->value }}</dd>

                            <dt class="col-sm-3">Responsable</dt>
                            <dd class="col-sm-9">{{ $site->person->firstname }} {{ $site->person->lastname }}</dd>

                            <dt class="col-sm-3">Adresse</dt>
                            <dd class="col-sm-9">{{ $site->address ?: 'Non spécifiée' }}</dd>
                        </dl>
                    </div>

                    <div class="mb-4">
                        <h5 class="card-title">Transactions récentes</h5>
                        @if($site->transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Montant</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($site->transactions->take(5) as $transaction)
                                            <tr>
                                                <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                                                <td>{{ $transaction->type->name }}</td>
                                                <td>{{ $transaction->currency->symbol }} {{ number_format($transaction->amount, 2) }}</td>
                                                <td>{{ Str::limit($transaction->description, 50) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Aucune transaction enregistrée pour ce site.</p>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('sites.index') }}" class="btn btn-secondary">Retour à la liste</a>
                        <form action="{{ route('sites.destroy', $site) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce site ?')">
                                Supprimer le site
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 