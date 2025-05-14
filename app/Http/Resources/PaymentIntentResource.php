<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentIntentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'client_secret' => $this->client_secret,
        ];
    }
}
