<?php
// app/Http/Requests/StoreServiceRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adapte selon ta logique d'auth
    }

    public function rules(): array
{
    return [
        'category_id'  => 'required|exists:categories,id',
        'provider_id'  => 'required|exists:providers,id',
        'name'         => 'required|array', // Tableau pour les langues
        'name.*'       => 'required|string|max:255', // Chaque langue doit être une string
        'description'  => 'nullable|array',
        'description.*'=> 'nullable|string',
        'icon'         => 'nullable|string|max:255',
        'color'        => 'nullable|string|max:7',
        'price'        => 'required|numeric|min:0',
        'active'       => 'boolean',
    ];
}

    
    public function messages(): array
    {
        return [
            'category_id.required'  => 'La catégorie est obligatoire.',
            'category_id.exists'    => 'Cette catégorie n’existe pas.',
            'provider_id.required'  => 'Le prestataire est requis.',
            'provider_id.exists'    => 'Ce prestataire n’existe pas.',
            'name.required'         => 'Le nom du service est obligatoire.',
            'price.required'        => 'Le prix est obligatoire.',
            'price.numeric'         => 'Le prix doit être numérique.',
            'active.boolean'        => 'Le champ actif doit être vrai ou faux.',
        ];
    }
}
