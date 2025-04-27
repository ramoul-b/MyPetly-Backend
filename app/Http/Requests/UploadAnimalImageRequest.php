<?php

// app/Http/Requests/UploadAnimalImageRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAnimalImageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'image' => 'required|image|max:5120', // 5 Mo
        ];
    }
}
