<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionTypeRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_credit' => 'required|boolean',
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
            'name.required' => 'Le nom du type de transaction est obligatoire.',
            'name.string' => 'Le nom du type de transaction doit être une chaîne de caractères.',
            'name.max' => 'Le nom du type de transaction ne doit pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'is_credit.required' => 'Le type de transaction (crédit/débit) est obligatoire.',
            'is_credit.boolean' => 'Le type de transaction doit être vrai ou faux.'
        ];
    }
}
