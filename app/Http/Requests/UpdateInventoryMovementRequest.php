<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'movement_type' => 'sometimes|in:in,out',
            'quantity'      => 'sometimes|integer|min:1',
            'reference'     => 'nullable|string|max:255',
            'notes'         => 'nullable|string',
            'occurred_at'   => 'nullable|date',
            'store_id'      => 'prohibited',
            'product_id'    => 'prohibited',
            'user_id'       => 'nullable|exists:users,id',
        ];
    }
}
