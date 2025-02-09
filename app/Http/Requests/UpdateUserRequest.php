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
            'email' => 'nullable|email|unique:users,email,' . $this->user()->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'name.string' => __('validation.name_string'),
            'email.email' => __('validation.email_invalid'),
            'email.unique' => __('validation.email_unique'),
            'phone.max' => __('validation.phone_max'),
            'address.max' => __('validation.address_max'),
            'photo.image' => __('validation.photo_image'),
            'photo.mimes' => __('validation.photo_mimes'),
            'photo.max' => __('validation.photo_max'),
        ];
    }

}
