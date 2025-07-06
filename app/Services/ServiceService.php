<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    public function getAll(): Collection
    {
        return Service::with(['category', 'provider'])->get();
    }

    public function find(int $id): Service
    {
        return Service::with(['category', 'provider'])->findOrFail($id);
    }

    public function create(array $data): Service
    {
        $service = new Service();
        $service->category_id = $data['category_id'];
        $service->provider_id = $data['provider_id'];
        $service->price = $data['price'];
        $service->active = $data['active'] ?? true;
        $service->icon = $data['icon'] ?? null;
        $service->color = $data['color'] ?? null;

        // Gérer les traductions
        $service->setTranslations('name', $data['name']);
        $service->setTranslations('description', $data['description'] ?? []);

        $service->save();
        return $service;
    }

    public function update(Service $service, array $data): Service
    {
        if (isset($data['category_id'])) {
            $service->category_id = $data['category_id'];
        }
        if (isset($data['provider_id'])) {
            $service->provider_id = $data['provider_id'];
        }
        if (isset($data['price'])) {
            $service->price = $data['price'];
        }
        if (isset($data['active'])) {
            $service->active = $data['active'];
        }
        if (isset($data['icon'])) {
            $service->icon = $data['icon'];
        }
        if (array_key_exists('color', $data)) {
            $service->color = $data['color'];
        }

        // Mise à jour des traductions
        if (isset($data['name'])) {
            $service->setTranslations('name', $data['name']);
        }
        if (isset($data['description'])) {
            $service->setTranslations('description', $data['description']);
        }

        $service->save();
        return $service;
    }

    public function delete(Service $service): void
    {
        $service->delete();
    }

    public function activate(Service $service): Service
    {
        $service->update(['active' => true]);
        return $service;
    }

    public function deactivate(Service $service): Service
    {
        $service->update(['active' => false]);
        return $service;
    }
}
