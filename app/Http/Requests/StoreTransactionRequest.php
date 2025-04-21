<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'site_id' => 'required|exists:sites,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'site_id.required' => 'Le site est obligatoire.',
            'site_id.exists' => 'Le site sélectionné n\'existe pas.',
            'transaction_type_id.required' => 'Le type de transaction est obligatoire.',
            'transaction_type_id.exists' => 'Le type de transaction sélectionné n\'existe pas.',
            'currency_id.required' => 'La devise est obligatoire.',
            'currency_id.exists' => 'La devise sélectionnée n\'existe pas.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'description.string' => 'La description doit être une chaîne de caractères.'
        ];
    }
}
