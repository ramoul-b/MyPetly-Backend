<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'sometimes|array',
            'name.*'       => 'sometimes|string|max:255',
            'icon'         => 'nullable|string|max:255',
            'type'         => 'required|string|max:255',
            'color'        => 'nullable|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'name.*.string'  => 'Le nom doit être une chaîne de caractères.',
            'icon.string'    => 'L’icône doit être une chaîne de caractères.',
            'type.string'    => 'Le type doit être une chaîne de caractères.',
            'color.string'   => 'La couleur doit être une chaîne de caractères.',
        ];
    }
}
