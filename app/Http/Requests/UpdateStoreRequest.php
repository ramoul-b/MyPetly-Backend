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
            'user_id'       => 'sometimes|exists:users,id|unique:stores,user_id,'.$storeId,
            'name'          => 'sometimes|array',
            'name.*'        => 'sometimes|string|max:255',
            'description'   => 'nullable|array',
            'description.*' => 'nullable|string',
            'address'       => 'sometimes|string|max:255',
            'phone'         => 'sometimes|string|max:20',
            'email'         => 'sometimes|email',
            'status'        => 'sometimes|string',
        ];
    }
}
