<?php

namespace App\Services;

use App\Models\StoreSetting;
use Illuminate\Database\Eloquent\Collection;

class StoreSettingService
{
    public function list(): Collection
    {
        return StoreSetting::with('store')->get();
    }

    public function create(array $data): StoreSetting
    {
        return StoreSetting::create($data);
    }

    public function getByStore(int $storeId): StoreSetting
    {
        return StoreSetting::firstOrCreate(['store_id' => $storeId]);
    }

    public function update(StoreSetting $setting, array $data): StoreSetting
    {
        $setting->update($data);

        return $setting->fresh();
    }

    public function delete(StoreSetting $setting): void
    {
        $setting->delete();
    }
}
