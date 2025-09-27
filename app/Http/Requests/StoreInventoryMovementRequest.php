<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id'      => 'required|exists:stores,id',
            'product_id'    => 'required|exists:products,id',
            'user_id'       => 'nullable|exists:users,id',
            'movement_type' => 'required|in:in,out',
            'quantity'      => 'required|integer|min:1',
            'reference'     => 'nullable|string|max:255',
            'notes'         => 'nullable|string',
            'occurred_at'   => 'nullable|date',
        ];
    }
}
