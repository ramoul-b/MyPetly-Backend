<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     *
     * @return bool
     */
    public function authorize()
    {
        // Autoriser cette requête pour tous les utilisateurs authentifiés (à ajuster si besoin)
        return auth()->check();
    }

    /**
     * Règles de validation applicables à la requête.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255|unique:permissions,name,' . $this->permission->id,
            'slug' => 'sometimes|string|max:255|unique:permissions,slug,' . $this->permission->id,
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.unique' => __('validation.unique', ['attribute' => 'name']),
            'slug.unique' => __('validation.unique', ['attribute' => 'slug']),
        ];
    }
}
