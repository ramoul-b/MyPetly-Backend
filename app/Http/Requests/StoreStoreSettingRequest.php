<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id'              => 'required|exists:stores,id',
            'currency'              => 'required|string|max:10',
            'timezone'              => 'required|string|max:255',
            'locale'                => 'required|string|max:10',
            'inventory_tracking'    => 'required|boolean',
            'notifications_enabled' => 'required|boolean',
            'low_stock_threshold'   => 'required|integer|min:0',
            'metadata'              => 'nullable|array',
        ];
    }
}
