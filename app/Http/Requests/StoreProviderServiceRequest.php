<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProviderServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_id' => 'required|exists:providers,id',
            'service_id'  => 'required|exists:services,id',
            'price'       => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'duration'    => 'nullable|integer|min:1',
            'available'   => 'boolean',
        ];
    }
}
