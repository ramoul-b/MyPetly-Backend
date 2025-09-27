<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id'            => 'required|exists:stores,id',
            'product_id'          => 'nullable|exists:products,id',
            'created_by'          => 'nullable|exists:users,id',
            'code'                => 'required|string|max:50|unique:coupons,code',
            'name'                => 'required|array',
            'name.*'              => 'required|string|max:255',
            'description'         => 'nullable|array',
            'description.*'       => 'nullable|string',
            'discount_type'       => 'required|in:percentage,fixed',
            'discount_value'      => 'required|numeric|min:0',
            'minimum_order_total' => 'nullable|numeric|min:0',
            'usage_limit'         => 'nullable|integer|min:1',
            'starts_at'           => 'nullable|date',
            'expires_at'          => 'nullable|date|after_or_equal:starts_at',
            'is_active'           => 'sometimes|boolean',
        ];
    }
}
