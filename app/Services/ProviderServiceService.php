<?php
namespace App\Services;

use App\Models\ProviderService;
use Illuminate\Database\Eloquent\Collection;

class ProviderServiceService
{
    public function getAll(): Collection
    {
        return ProviderService::with(['provider', 'service'])->get();
    }

    public function find(int $id): ProviderService
    {
        return ProviderService::with(['provider', 'service'])->findOrFail($id);
    }

    public function create(array $data): ProviderService
    {
        return ProviderService::create($data);
    }

    public function update(ProviderService $providerService, array $data): ProviderService
    {
        $providerService->update($data);
        return $providerService;
    }

    public function delete(ProviderService $providerService): void
    {
        $providerService->delete();
    }

    public function getByProvider(int $providerId): Collection
{
    return ProviderService::where('provider_id', $providerId)->with(['service'])->get();
}

    public function getByService(int $serviceId): Collection
    {
        return ProviderService::where('service_id', $serviceId)->with(['provider'])->get();
    }
}
