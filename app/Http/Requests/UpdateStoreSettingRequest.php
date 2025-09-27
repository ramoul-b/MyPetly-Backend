<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency'              => 'sometimes|string|max:10',
            'timezone'              => 'sometimes|string|max:255',
            'locale'                => 'sometimes|string|max:10',
            'inventory_tracking'    => 'sometimes|boolean',
            'notifications_enabled' => 'sometimes|boolean',
            'low_stock_threshold'   => 'sometimes|integer|min:0',
            'metadata'              => 'nullable|array',
        ];
    }
}
