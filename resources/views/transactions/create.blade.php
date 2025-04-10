@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Nouvelle transaction</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('transactions.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="site_id" class="form-label">Site</label>
                            <select class="form-select @error('site_id') is-invalid @enderror" 
                                id="site_id" name="site_id" required>
                                <option value="">Sélectionner un site</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="transaction_type_id" class="form-label">Type de transaction</label>
                            <select class="form-select @error('transaction_type_id') is-invalid @enderror" 
                                id="transaction_type_id" name="transaction_type_id" required>
                                <option value="">Sélectionner un type</option>
                                @foreach($transactionTypes as $type)
                                    <option value="{{ $type->id }}" 
                                        data-operation-type="{{ $type->is_credit ? 'credit' : 'debit' }}">
                                        {{ $type->name }} ({{ $type->is_credit ? 'Crédit' : 'Débit' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('transaction_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant</label>
                            <div class="input-group">
                                <select class="form-select @error('currency_id') is-invalid @enderror" 
                                    id="currency_id" name="currency_id" required style="max-width: 120px;">
                                    <option value="">Devise</option>
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->symbol }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="number" step="0.01" min="0.01" 
                                    class="form-control @error('amount') is-invalid @enderror" 
                                    id="amount" name="amount" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Retour</a>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('transaction_type_id');
    const amountInput = document.getElementById('amount');

    typeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const operationType = selectedOption.dataset.operationType;
        
        if (operationType === 'debit') {
            amountInput.classList.add('text-danger');
            amountInput.classList.remove('text-success');
        } else if (operationType === 'credit') {
            amountInput.classList.add('text-success');
            amountInput.classList.remove('text-danger');
        } else {
            amountInput.classList.remove('text-success', 'text-danger');
        }
    });
});
</script>
@endpush 