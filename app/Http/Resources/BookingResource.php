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
            'id'               => $this->id,
            'service'          => new ServiceResource($this->whenLoaded('service')),
            'provider'         => new ProviderResource($this->whenLoaded('provider')),
            'appointment_date' => $this->appointment_date 
                                    ? \Carbon\Carbon::parse($this->appointment_date)->format('Y-m-d') 
                                    : null,
            'time'             => $this->time,
            'currency'         => $this->currency,
            'status'           => $this->status,
            'notes'            => $this->notes,
            'created_at' => $this->created_at
                            ? $this->created_at->format('Y-m-d H:i')
                            : null,
        ];
    }
}
