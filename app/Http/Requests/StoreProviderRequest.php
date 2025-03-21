<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|array',
            'name.*'          => 'required|string|max:255',
            'email'           => 'required|email|unique:providers,email',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:255',
            'description'     => 'nullable|array',
            'description.*'   => 'nullable|string',
            'photo'           => 'nullable|string|max:255',
            'birth_year'      => 'nullable|integer|min:1900|max:' . date('Y'),
            'specialization'  => 'nullable|array',
            'specialization.*'=> 'nullable|string|max:255',
            'education'       => 'nullable|string',
            'experience'      => 'nullable|string',
            'personal_info'   => 'nullable|string',
            'rating'          => 'numeric|min:0|max:5',
        ];
    }
}
