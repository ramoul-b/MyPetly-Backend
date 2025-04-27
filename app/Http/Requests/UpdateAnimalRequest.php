<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnimalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name'                  => 'sometimes|required|string|max:100',
            'species'               => 'sometimes|required|string|max:50',
            'breed'                 => 'sometimes|nullable|string|max:100',
            'color'                 => 'sometimes|nullable|string|max:50',
            'weight'                => 'sometimes|nullable|numeric|min:0',
            'height'                => 'sometimes|nullable|numeric|min:0',
            'birth_date'            => 'sometimes|nullable|date',
            'sex'                => 'sometimes|required|in:male,female',
            'identification_number' => "sometimes|nullable|string|max:100|unique:animals,identification_number,{$id}",
            'status'                => 'sometimes|in:active,lost',
            'collar_type'           => 'sometimes|nullable|in:GPS,NFC,none',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                  => 'Le nom est obligatoire.',
            'species.required'               => 'L\'espèce est obligatoire.',
            'gender.required'                => 'Le genre est obligatoire.',
            'gender.in'                      => 'Le genre doit être soit male soit female.',
            'identification_number.unique'   => 'Le numéro d\'identification est déjà utilisé.',
            'birth_date.date'                 => 'La date de naissance doit être une date valide.',
            'weight.numeric'                  => 'Le poids doit être un nombre.',
            'weight.min'                      => 'Le poids doit être positif.',
            'height.numeric'                  => 'La taille doit être un nombre.',
            'height.min'                      => 'La taille doit être positive.',
            'status.in'                       => 'Le statut doit être "active" ou "lost".',
            'collar_type.in'                  => 'Le type de collier doit être GPS, NFC ou none.',
        ];
    }
}
