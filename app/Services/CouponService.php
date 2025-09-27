<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CouponService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Coupon::query()->with(['store', 'product', 'creator']);

        if (!empty($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }

        if (!empty($filters['code'])) {
            $query->where('code', 'like', '%' . $filters['code'] . '%');
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 15);
    }

    public function find(int $id): Coupon
    {
        return Coupon::with(['store', 'product', 'creator'])->findOrFail($id);
    }

    public function create(array $data): Coupon
    {
        return Coupon::create($data);
    }

    public function update(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);

        return $coupon->fresh(['store', 'product', 'creator']);
    }

    public function delete(Coupon $coupon): void
    {
        $coupon->delete();
    }

    public function forStore(int $storeId): Collection
    {
        return Coupon::query()
            ->where('store_id', $storeId)
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->get();
    }
}
