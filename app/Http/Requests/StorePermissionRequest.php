<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la permission est requis.',
            'name.unique' => 'Cette permission existe déjà.',
        ];
    }
}
