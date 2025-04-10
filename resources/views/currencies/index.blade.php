@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Devises</h3>
                    <a href="{{ route('currencies.create') }}" class="btn btn-primary">Ajouter une devise</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Code</th>
                                    <th>Symbole</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currencies as $currency)
                                    <tr>
                                        <td>{{ $currency->name }}</td>
                                        <td>{{ $currency->code }}</td>
                                        <td>{{ $currency->symbol }}</td>
                                        <td>
                                            <a href="{{ route('currencies.edit', $currency) }}" class="btn btn-sm btn-info">
                                                Modifier
                                            </a>
                                            <form action="{{ route('currencies.destroy', $currency) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette devise ?')">
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