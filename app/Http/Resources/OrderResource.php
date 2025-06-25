<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'store_id' => $this->store_id,
            'status' => $this->status,
            'total' => $this->total,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'store' => new StoreResource($this->whenLoaded('store')),
            'created_at' => $this->created_at,
        ];
    }
}
