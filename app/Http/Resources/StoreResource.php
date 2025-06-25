<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'provider_id' => $this->provider_id,
            'name'        => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'address'     => $this->address,
            'phone'       => $this->phone,
            'email'       => $this->email,
            'created_at'  => $this->created_at->format('Y-m-d H:i'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
