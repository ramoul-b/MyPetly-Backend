<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sex' => $this->sex,
            'color' => $this->color,
            'weight' => $this->weight,
            'height' => $this->height,
            'identification_number' => $this->identification_number,
            'species' => $this->species,
            'breed' => $this->breed,
            'birthdate' => $this->birthdate,
            'photo_url' => $this->photo ? asset('storage/' . $this->photo) : null,
            'status' => $this->status,
        ];
    }
}
