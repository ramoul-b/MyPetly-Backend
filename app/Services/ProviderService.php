<?php

namespace App\Services;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $data['photo']->store('providers', 'public');
        }

        $provider = new Provider();
        $provider->email  = $data['email'];
        $provider->phone  = $data['phone'] ?? null;
        $provider->tax_code = $data['tax_code'] ?? null;
        $provider->address = $data['address'] ?? null;
        $provider->rating = $data['rating'] ?? 0;
        $provider->photo  = $data['photo'] ?? null;

        // ✅ Utilisation de setTranslations() pour gérer les champs multilingues
        $provider->setTranslations('name', $data['name']);
        $provider->setTranslations('specialization', $data['specialization'] ?? []);

        $provider->save();
        return $provider;
    }

    public function update(Provider $provider, array $data): Provider
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if ($provider->photo && Storage::disk('public')->exists($provider->photo)) {
                Storage::disk('public')->delete($provider->photo);
            }
            $provider->photo = $data['photo']->store('providers', 'public');
        }

        if (isset($data['email'])) {
            $provider->email = $data['email'];
        }
        if (isset($data['user_id'])) {
            $provider->user_id = $data['user_id'];
        }
        if (isset($data['phone'])) {
            $provider->phone = $data['phone'];
        }
        if (isset($data['tax_code'])) {
            $provider->tax_code = $data['tax_code'];
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

    public function updatePhoto(int $id, string $path): ?Provider
    {
        $provider = Provider::find($id);
        if (!$provider) {
            return null;
        }

        if ($provider->photo && Storage::disk('public')->exists($provider->photo)) {
            Storage::disk('public')->delete($provider->photo);
        }

        $provider->photo = $path;
        $provider->save();

        return $provider;
    }
}
