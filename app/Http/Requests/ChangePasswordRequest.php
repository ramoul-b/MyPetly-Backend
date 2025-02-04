<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Autoriser tous les utilisateurs authentifiés
    }

    public function rules()
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => __('validation.current_password_required'),
            'new_password.required' => __('validation.new_password_required'),
            'new_password.min' => __('validation.new_password_min'),
            'new_password.confirmed' => __('validation.new_password_confirmed'),
        ];
    }
}
