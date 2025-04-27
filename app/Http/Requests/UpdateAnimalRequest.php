<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnimalRequest extends FormRequest
{
    public function rules() { return [ /* champs texte JSON */ ]; }
}

class UploadAnimalImageRequest extends FormRequest
{
    public function rules()
    {
        return ['image' => 'required|image|max:5120'];
    }
}

