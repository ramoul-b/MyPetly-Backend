<?php


// app/Http/Resources/ServiceResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'price'        => $this->price,
            'active'       => (bool) $this->active,
            'category'     => [
                'id'          => $this->category->id,
                'name'       => $this->category->name,
                'icon'       => $this->category->icon,
                'color'     => $this->category->color,
            ],
            'provider'     => [
                'id'             => $this->provider->id,
                'name'           => $this->provider->name,
                'photo'          => $this->provider->photo,
                'specialization' => $this->provider->specialization,
                'rating'         => $this->provider->rating,
            ],
            'created_at'   => $this->created_at->format('Y-m-d H:i'),
            'updated_at'   => $this->updated_at,
        ];
    }
}