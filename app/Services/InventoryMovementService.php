<?php

namespace App\Services;

use App\Models\InventoryMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InventoryMovementService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = InventoryMovement::query()->with(['store', 'product', 'user']);

        if (!empty($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        return $query->orderByDesc('occurred_at')->paginate($filters['per_page'] ?? 15);
    }

    public function find(int $id): InventoryMovement
    {
        return InventoryMovement::with(['store', 'product', 'user'])->findOrFail($id);
    }

    public function create(array $data): InventoryMovement
    {
        $movement = InventoryMovement::create($data);

        $movement->product->increment('stock', $data['movement_type'] === 'in' ? $data['quantity'] : -$data['quantity']);

        return $movement->fresh(['store', 'product', 'user']);
    }

    public function update(InventoryMovement $movement, array $data): InventoryMovement
    {
        $originalQuantity = $movement->quantity;
        $originalType = $movement->movement_type;

        $movement->update($data);

        $movement->product->increment('stock', $this->calculateAdjustment($originalType, $originalQuantity, $movement->movement_type, $movement->quantity));

        return $movement->fresh(['store', 'product', 'user']);
    }

    public function delete(InventoryMovement $movement): void
    {
        $movement->product->increment('stock', $movement->movement_type === 'in' ? -$movement->quantity : $movement->quantity);
        $movement->delete();
    }

    public function getRecentForStore(int $storeId, int $limit = 10): Collection
    {
        return InventoryMovement::query()
            ->where('store_id', $storeId)
            ->orderByDesc('occurred_at')
            ->limit($limit)
            ->get();
    }

    private function calculateAdjustment(string $oldType, int $oldQuantity, string $newType, int $newQuantity): int
    {
        $adjustment = 0;

        $adjustment += $oldType === 'in' ? -$oldQuantity : $oldQuantity;
        $adjustment += $newType === 'in' ? $newQuantity : -$newQuantity;

        return $adjustment;
    }
}
