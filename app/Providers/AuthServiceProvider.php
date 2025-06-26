<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Animal;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Booking;
use App\Models\Collar;
use App\Models\Category;
use App\Models\Review;
use App\Models\ProviderService;
use App\Models\Store;
use App\Models\Order;
use App\Models\CartItem;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\AnimalPolicy;
use App\Policies\ServicePolicy;
use App\Policies\ProviderPolicy;
use App\Policies\BookingPolicy;
use App\Policies\CollarPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\ProviderServicePolicy;
use App\Policies\StorePolicy;
use App\Policies\OrderPolicy;
use App\Policies\UserPolicy;
use App\Policies\CartItemPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Animal::class          => AnimalPolicy::class,
        Service::class         => ServicePolicy::class,
        Provider::class        => ProviderPolicy::class,
        Booking::class         => BookingPolicy::class,
        Collar::class          => CollarPolicy::class,
        Category::class        => CategoryPolicy::class,
        Role::class            => RolePolicy::class,
        Permission::class      => PermissionPolicy::class,
        Review::class          => ReviewPolicy::class,
        ProviderService::class => ProviderServicePolicy::class,
        Store::class           => StorePolicy::class,
        Order::class           => OrderPolicy::class,
        CartItem::class        => CartItemPolicy::class,
        User::class            => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
