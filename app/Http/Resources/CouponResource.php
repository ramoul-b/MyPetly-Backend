<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'store_id'            => $this->store_id,
            'product_id'          => $this->product_id,
            'code'                => $this->code,
            'name'                => $this->getTranslations('name'),
            'description'         => $this->getTranslations('description'),
            'discount_type'       => $this->discount_type,
            'discount_value'      => (float) $this->discount_value,
            'minimum_order_total' => $this->minimum_order_total ? (float) $this->minimum_order_total : null,
            'usage_limit'         => $this->usage_limit,
            'used_count'          => $this->used_count,
            'starts_at'           => optional($this->starts_at)->toIso8601String(),
            'expires_at'          => optional($this->expires_at)->toIso8601String(),
            'is_active'           => (bool) $this->is_active,
            'store'               => new StoreResource($this->whenLoaded('store')),
            'product'             => new ProductResource($this->whenLoaded('product')),
            'creator'             => new UserResource($this->whenLoaded('creator')),
            'created_at'          => optional($this->created_at)->toIso8601String(),
            'updated_at'          => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
