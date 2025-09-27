<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Order;
use App\Models\Provider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AdminDashboardService
{
    public function getStats(array $filters = []): array
    {
        $from = $this->parseDate($filters['date_from'] ?? null, true);
        $to = $this->parseDate($filters['date_to'] ?? null, false);

        $orderQuery = Order::query();
        $this->applyDateRange($orderQuery, $from, $to);

        $totalRevenue = (float) (clone $orderQuery)->sum('total');
        $ordersVolume = (clone $orderQuery)->count();
        $pendingOrders = (clone $orderQuery)->where('status', 'pending')->count();
        $completedOrders = (clone $orderQuery)->where('status', 'completed')->count();

        $averageOrderValue = $ordersVolume > 0
            ? round($totalRevenue / $ordersVolume, 2)
            : 0.0;

        $bookingQuery = Booking::query();
        $this->applyDateRange($bookingQuery, $from, $to);

        $bookingsVolume = (clone $bookingQuery)->count();
        $pendingBookings = (clone $bookingQuery)->where('status', 'pending')->count();

        $pendingUsers = User::where('status', 'pending')->count();
        $pendingProviders = Provider::whereHas('user', function ($query) {
            $query->where('status', 'pending');
        })->count();

        return [
            'total_revenue' => $totalRevenue,
            'orders_volume' => $ordersVolume,
            'average_order_value' => $averageOrderValue,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'bookings_volume' => $bookingsVolume,
            'pending_bookings' => $pendingBookings,
            'pending_users' => $pendingUsers,
            'pending_providers' => $pendingProviders,
            'total_users' => User::count(),
            'total_providers' => Provider::count(),
        ];
    }

    private function applyDateRange(Builder $query, ?Carbon $from, ?Carbon $to): void
    {
        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }
    }

    private function parseDate(?string $value, bool $startOfDay): ?Carbon
    {
        if (!$value) {
            return null;
        }

        $date = Carbon::parse($value);

        return $startOfDay ? $date->startOfDay() : $date->endOfDay();
    }
}
