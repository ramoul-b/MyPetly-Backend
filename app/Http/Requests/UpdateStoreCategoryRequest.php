<?php

namespace App\Http\Requests;

use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('id');
        $storeId = Store::where('user_id', $this->user()->id)->value('id') ?? 0;

        return [
            'name' => ['sometimes', 'array'],
            'name.*' => ['required_with:name', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('store_categories', 'slug')
                    ->ignore($categoryId)
                    ->where(fn($query) => $query->where('store_id', $storeId)),
            ],
            'parent_id' => ['nullable', 'exists:store_categories,id'],
        ];
    }
}
