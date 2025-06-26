<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;

class StoreService
{
    public function getAll(): Collection
    {
        return Store::all();
    }

    public function find(int $id): Store
    {
        return Store::findOrFail($id);
    }

    public function create(array $data): Store
    {
        $store = new Store();
        $store->user_id = $data['user_id'];
        $store->setTranslations('name', $data['name']);
        if (isset($data['description'])) {
            $store->setTranslations('description', $data['description']);
        }
        $store->address = $data['address'] ?? null;
        $store->phone   = $data['phone'] ?? null;
        $store->email   = $data['email'] ?? null;
        $store->status  = $data['status'] ?? 'active';
        $store->save();
        return $store;
    }

    public function update(Store $store, array $data): Store
    {
        if (isset($data['user_id'])) {
            $store->user_id = $data['user_id'];
        }
        if (isset($data['name'])) {
            $store->setTranslations('name', $data['name']);
        }
        if (isset($data['description'])) {
            $store->setTranslations('description', $data['description']);
        }
        if (isset($data['address'])) {
            $store->address = $data['address'];
        }
        if (isset($data['phone'])) {
            $store->phone = $data['phone'];
        }
        if (isset($data['email'])) {
            $store->email = $data['email'];
        }
        if (isset($data['status'])) {
            $store->status = $data['status'];
        }
        $store->save();
        return $store;
    }

    public function delete(Store $store): void
    {
        $store->delete();
    }
}
