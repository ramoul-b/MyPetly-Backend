<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProviderServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_id' => 'sometimes|exists:providers,id',
            'service_id'  => 'sometimes|exists:services,id',
            'price'       => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'duration'    => 'nullable|integer|min:1',
            'available'   => 'boolean',
        ];
    }
}
