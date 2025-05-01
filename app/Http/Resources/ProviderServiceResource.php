<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'price'      => $this->price,
            'description'=> $this->description,
            'duration'   => $this->duration,
            'available'  => $this->available,
            'provider'   => new ProviderResource($this->whenLoaded('provider')),
            'service'    => new ServiceResource($this->whenLoaded('service')),
        ];
    }
}
