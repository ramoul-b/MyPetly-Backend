<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return  [
            'id' => $this->id, //Gate::allows('AG_tests_showfieldid') ? $this->id : null,
            'name' => $this->name,
            'email' => $this->email,
            'photo_url' => $this->photo ? asset('storage/' . $this->photo) : null,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles'       => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name'),
        ];
    }
}
