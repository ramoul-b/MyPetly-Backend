<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id'            => 'sometimes|exists:stores,id',
            'product_id'          => 'nullable|exists:products,id',
            'created_by'          => 'nullable|exists:users,id',
            'code'                => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($this->route('coupon')),
            ],
            'name'                => 'sometimes|array',
            'name.*'              => 'required_with:name|string|max:255',
            'description'         => 'nullable|array',
            'description.*'       => 'nullable|string',
            'discount_type'       => 'sometimes|in:percentage,fixed',
            'discount_value'      => 'sometimes|numeric|min:0',
            'minimum_order_total' => 'nullable|numeric|min:0',
            'usage_limit'         => 'nullable|integer|min:1',
            'used_count'          => 'nullable|integer|min:0',
            'starts_at'           => 'nullable|date',
            'expires_at'          => 'nullable|date|after_or_equal:starts_at',
            'is_active'           => 'sometimes|boolean',
        ];
    }
}
