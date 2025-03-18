<?php

// app/Services/ServiceService.php
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
        return Service::create($data);
    }

    public function update(Service $service, array $data): Service
    {
        $service->update($data);
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
