<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'store_id'               => $this->store_id,
            'currency'               => $this->currency,
            'timezone'               => $this->timezone,
            'locale'                 => $this->locale,
            'inventory_tracking'     => (bool) $this->inventory_tracking,
            'notifications_enabled'  => (bool) $this->notifications_enabled,
            'low_stock_threshold'    => $this->low_stock_threshold,
            'metadata'               => $this->metadata,
            'created_at'             => optional($this->created_at)->toIso8601String(),
            'updated_at'             => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
