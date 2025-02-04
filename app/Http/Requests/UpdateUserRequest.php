<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Changez ceci si vous souhaitez restreindre l'accès.
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->route('id'),
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'name.string' => __('The name must be a valid string.'),
            'email.email' => __('Please provide a valid email address.'),
            'email.unique' => __('This email is already taken.'),
            'password.min' => __('The password must be at least 8 characters.'),
            'password.confirmed' => __('The password confirmation does not match.'),
            'roles.*.exists' => __('The selected role does not exist.'),
        ];
    }
}
