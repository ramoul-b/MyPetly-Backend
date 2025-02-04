<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollarResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nfc_id' => $this->nfc_id,
            'qr_code_url' => $this->qr_code_url,
            'animal_id' => $this->animal_id,
        ];
    }
}
