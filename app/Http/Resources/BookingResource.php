<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'service' => new ServiceResource($this->whenLoaded('service')),
            'user'    => new UserResource($this->whenLoaded('user')),
            'animal' => [
                'id' => $this->animal->id ?? null,
                'name' => $this->animal->name ?? null,
            ],
            'provider'=> new ProviderResource($this->whenLoaded('provider')),
            'appointment_date' => $this->appointment_date,
            'time'             => $this->time,
            'currency'         => $this->currency,
            'status'           => $this->status,
            'payment_status'   => $this->payment_status,
            'notes'            => $this->notes,
            'created_at'       => $this->created_at,
        ];
    }
}
