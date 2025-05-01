<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->getTranslations('name'),
            'description'  => $this->getTranslations('description'),
            'price'        => $this->price,
            'active'       => (bool) $this->active,

            'category'     => $this->whenLoaded('category', function () {
                return $this->category ? [
                    'id'    => $this->category->id,
                    'name'  => $this->category->getTranslations('name'),
                    'icon'  => $this->category->icon,
                    'color' => $this->category->color,
                ] : null;
            }),

            'provider'     => $this->whenLoaded('provider', function () {
                return $this->provider ? [
                    'id'             => $this->provider->id,
                    'name'           => $this->provider->getTranslations('name'),
                    'photo'          => $this->provider->photo,
                    'specialization' => $this->provider->specialization,
                    'rating'         => $this->provider->rating,
                ] : null;
            }),

            'created_at'   => $this->created_at?->format('Y-m-d H:i'),
            'updated_at'   => $this->updated_at,
        ];
    }
}
