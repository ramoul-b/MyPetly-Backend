<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCollarRequest extends FormRequest
{
    public function rules()
    {
        return [
            'nfc_id' => 'required|unique:collars,nfc_id,' . $this->route('collar'),
            'qr_code_url' => 'nullable|url',
            'animal_id' => 'nullable|exists:animals,id',
        ];
    }
}
