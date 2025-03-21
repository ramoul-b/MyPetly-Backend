<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->getTranslations('name'),
            'email'         => $this->email,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'description'   => $this->getTranslations('description'),
            'photo'         => $this->photo,
            'birth_year'    => $this->birth_year,
            'specialization'=> $this->getTranslations('specialization'),
            'education'     => $this->education,
            'experience'    => $this->experience,
            'personal_info' => $this->personal_info,
            'rating'        => $this->rating,
            'services'      => ServiceResource::collection($this->whenLoaded('services')),
            'created_at'    => $this->created_at->format('Y-m-d H:i'),
            'updated_at'    => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
