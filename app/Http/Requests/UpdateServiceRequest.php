<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'  => 'sometimes|exists:categories,id',
            'provider_id'  => 'sometimes|exists:providers,id',
            'name'         => 'sometimes|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'sometimes|numeric|min:0',
            'active'       => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'La catégorie choisie n’existe pas.',
            'provider_id.exists' => 'Le prestataire choisi n’existe pas.',
        ];
    }
}
