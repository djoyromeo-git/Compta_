<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteRequest extends FormRequest
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
            'site_category_id' => 'required|exists:site_categories,id',
            'person_id' => 'required|exists:people,id',
            'address' => 'nullable|string'
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
            'name.required' => 'Le nom du site est obligatoire.',
            'name.string' => 'Le nom du site doit être une chaîne de caractères.',
            'name.max' => 'Le nom du site ne doit pas dépasser 255 caractères.',
            'site_category_id.required' => 'La catégorie du site est obligatoire.',
            'site_category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'person_id.required' => 'Le responsable du site est obligatoire.',
            'person_id.exists' => 'Le responsable sélectionné n\'existe pas.',
            'address.string' => 'L\'adresse doit être une chaîne de caractères.'
        ];
    }
}
