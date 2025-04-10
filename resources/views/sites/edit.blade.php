@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Modifier le site</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('sites.update', $site) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $site->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_category_id" class="form-label">Catégorie</label>
                            <select class="form-select @error('site_category_id') is-invalid @enderror" 
                                id="site_category_id" name="site_category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ (old('site_category_id', $site->site_category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('site_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="person_id" class="form-label">Responsable</label>
                            <select class="form-select @error('person_id') is-invalid @enderror" 
                                id="person_id" name="person_id" required>
                                <option value="">Sélectionner un responsable</option>
                                @foreach($people as $person)
                                    <option value="{{ $person->id }}" 
                                        {{ (old('person_id', $site->person_id) == $person->id) ? 'selected' : '' }}>
                                        {{ $person->first_name }} {{ $person->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('person_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                id="address" name="address" rows="3">{{ old('address', $site->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sites.index') }}" class="btn btn-secondary">Retour</a>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 