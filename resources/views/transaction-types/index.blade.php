@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Types de transactions</h3>
                    <a href="{{ route('transaction-types.create') }}" class="btn btn-primary">Ajouter un type</a>
                </div>

                <div class="card-body">
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
                                    <th>Nom</th>
                                    <th>Type d'opération</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactionTypes as $type)
                                    <tr>
                                        <td>{{ $type->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $type->is_credit ? 'success' : 'danger' }}">
                                                {{ $type->is_credit ? 'Crédit' : 'Débit' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('transaction-types.edit', $type) }}" class="btn btn-sm btn-info">
                                                Modifier
                                            </a>
                                            <form action="{{ route('transaction-types.destroy', $type) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce type de transaction ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 