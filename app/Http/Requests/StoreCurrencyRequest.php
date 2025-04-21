<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:currencies',
            'code' => 'required|string|max:3|unique:currencies',
            'symbol' => 'required|string|max:10'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la devise est requis.',
            'name.string' => 'Le nom de la devise doit être une chaîne de caractères.',
            'name.max' => 'Le nom de la devise ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Le nom de la devise existe déjà.',

            'code.required' => 'Le code de la devise est requis.',
            'code.string' => 'Le code de la devise doit être une chaîne de caractères.',
            'code.max' => 'Le code de la devise ne doit pas dépasser 3 caractères.',
            'code.unique' => 'Le code de la devise existe déjà.',

            'symbol.required' => 'Le symbole de la devise est requis.',
            'symbol.string' => 'Le symbole de la devise doit être une chaîne de caractères.',
            'symbol.max' => 'Le symbole de la devise ne doit pas dépasser 10 caractères.'
        ];
    }
}
