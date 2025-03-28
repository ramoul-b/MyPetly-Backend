<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Autoriser tous les utilisateurs à faire cette requête
    }

    public function rules()
    {
        return [
            'name'         => 'required|array',
            'name.*'       => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Le nom de la catégorie est obligatoire.',
            'name.*.string'  => 'Le nom doit être une chaîne de caractères.',
            'icon.string'    => 'L’icône doit être une chaîne de caractères.',
            'type.string'    => 'Le type doit être une chaîne de caractères.',
            'color.string'   => 'La couleur doit être une chaîne de caractères.',
        ];
    }
}
