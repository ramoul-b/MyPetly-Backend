<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:permissions,name',
            'slug' => 'required|string|max:255|unique:permissions,slug',
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
            'name.required' => __('validation.required', ['attribute' => 'name']),
            'name.unique' => __('validation.unique', ['attribute' => 'name']),
            'slug.required' => __('validation.required', ['attribute' => 'slug']),
            'slug.unique' => __('validation.unique', ['attribute' => 'slug']),
        ];
    }
}
