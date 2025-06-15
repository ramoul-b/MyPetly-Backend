<?php

namespace App\Services;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;

class ProviderService
{
    public function getAll(): Collection
    {
        return Provider::all();
    }

    public function find(int $id): Provider
    {
        return Provider::findOrFail($id);
    }

    public function create(array $data): Provider
    {
        $provider = new Provider();
        $provider->email = $data['email'];
        $provider->phone = $data['phone'] ?? null;
        $provider->address = $data['address'] ?? null;
        $provider->rating = $data['rating'] ?? 0;

        // ✅ Utilisation de setTranslations() pour gérer les champs multilingues
        $provider->setTranslations('name', $data['name']);
        $provider->setTranslations('specialization', $data['specialization'] ?? []);

        $provider->save();
        return $provider;
    }

    public function update(Provider $provider, array $data): Provider
    {
        if (isset($data['email'])) {
            $provider->email = $data['email'];
        }
        if (isset($data['phone'])) {
            $provider->phone = $data['phone'];
        }
        if (isset($data['address'])) {
            $provider->address = $data['address'];
        }
        if (isset($data['rating'])) {
            $provider->rating = $data['rating'];
        }

        // ✅ Mettre à jour les champs multilingues
        if (isset($data['name'])) {
            $provider->setTranslations('name', $data['name']);
        }
        if (isset($data['specialization'])) {
            $provider->setTranslations('specialization', $data['specialization']);
        }

        $provider->save();
        return $provider;
    }

    public function findByUserId(int $userId): Provider
    {
        return Provider::where('user_id', $userId)
            ->with('services')
            ->firstOrFail();
    }

    public function delete(Provider $provider): void
    {
        $provider->delete();
    }
}
