<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $storeId = $this->route('store');

        return [
            'provider_id'   => 'sometimes|exists:providers,id|unique:stores,provider_id,'.$storeId,
            'name'          => 'sometimes|array',
            'name.*'        => 'sometimes|string|max:255',
            'description'   => 'nullable|array',
            'description.*' => 'nullable|string',
            'address'       => 'sometimes|string|max:255',
            'phone'         => 'sometimes|string|max:20',
            'email'         => 'sometimes|email',
        ];
    }
}
