<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Autoriser tous les utilisateurs à faire cette requête
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ];
    }
}
