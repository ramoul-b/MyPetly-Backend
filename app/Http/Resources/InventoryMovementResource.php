<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'store_id'      => $this->store_id,
            'product_id'    => $this->product_id,
            'user_id'       => $this->user_id,
            'movement_type' => $this->movement_type,
            'quantity'      => $this->quantity,
            'reference'     => $this->reference,
            'notes'         => $this->notes,
            'occurred_at'   => optional($this->occurred_at)->toIso8601String(),
            'store'         => new StoreResource($this->whenLoaded('store')),
            'product'       => new ProductResource($this->whenLoaded('product')),
            'user'          => new UserResource($this->whenLoaded('user')),
            'created_at'    => optional($this->created_at)->toIso8601String(),
            'updated_at'    => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
