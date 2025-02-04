<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCollarRequest extends FormRequest
{
    public function rules()
    {
        return [
            'nfc_id' => 'required|unique:collars,nfc_id',
            'qr_code_url' => 'nullable|url',
            'animal_id' => 'nullable|exists:animals,id',
        ];
    }

    public function messages()
    {
        return [
            'nfc_id.required' => __('validation.required', ['attribute' => 'NFC ID']),
            'nfc_id.unique' => __('validation.unique', ['attribute' => 'NFC ID']),
            'qr_code_url.url' => __('validation.url', ['attribute' => 'QR Code URL']),
            'animal_id.exists' => __('validation.exists', ['attribute' => 'Animal ID']),
        ];
    }
}
