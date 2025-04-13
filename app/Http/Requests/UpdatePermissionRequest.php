<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $permissionId = $this->route('id');

        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permissionId,
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
