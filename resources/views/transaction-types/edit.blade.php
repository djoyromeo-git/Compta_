@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Modifier le type de transaction</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('transaction-types.update', $transaction_type) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', $transaction_type->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="is_credit" class="form-label">Type d'opération</label>
                            <select class="form-select @error('is_credit') is-invalid @enderror"
                                id="is_credit" name="is_credit" required>
                                <option value="0" {{ old('is_credit', $transaction_type->is_credit) == 0 ? 'selected' : '' }}>Débit</option>
                                <option value="1" {{ old('is_credit', $transaction_type->is_credit) == 1 ? 'selected' : '' }}>Crédit</option>
                            </select>
                            @error('is_credit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('transaction-types.index') }}" class="btn btn-secondary">Retour</a>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
