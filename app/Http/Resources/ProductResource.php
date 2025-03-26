<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }    
}
