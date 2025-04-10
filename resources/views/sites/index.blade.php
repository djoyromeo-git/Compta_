@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Sites</h3>
                    <a href="{{ route('sites.create') }}" class="btn btn-primary">Ajouter un site</a>
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
                                    <th>Catégorie</th>
                                    <th>Responsable</th>
                                    <th>Adresse</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sites as $site)
                                    <tr>
                                        <td>{{ $site->name }}</td>
                                        <td>{{ $site->category->name }}</td>
                                        <td>{{ $site->person->first_name }} {{ $site->person->last_name }}</td>
                                        <td>{{ $site->address }}</td>
                                        <td>
                                            <a href="{{ route('sites.show', $site) }}" class="btn btn-sm btn-primary">
                                                Voir
                                            </a>
                                            <a href="{{ route('sites.edit', $site) }}" class="btn btn-sm btn-info">
                                                Modifier
                                            </a>
                                            <form action="{{ route('sites.destroy', $site) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce site ?')">
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