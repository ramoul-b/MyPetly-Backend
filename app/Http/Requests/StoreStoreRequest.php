<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'            => 'required|exists:users,id|unique:stores,user_id',
            'name'               => 'required|array',
            'name.*'             => 'required|string|max:255',
            'description'        => 'nullable|array',
            'description.*'      => 'nullable|string',
            'address'            => 'nullable|string|max:255',
            'phone'              => 'nullable|string|max:20',
            'email'              => 'nullable|email',
            'status'             => 'nullable|string',
        ];
    }
}
