<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Animal;
use App\Models\Service;
use App\Models\Provider;
use App\Models\Booking;
use App\Models\Collar;
use App\Models\Category;
use App\Policies\AnimalPolicy;
use App\Policies\ServicePolicy;
use App\Policies\ProviderPolicy;
use App\Policies\BookingPolicy;
use App\Policies\CollarPolicy;
use App\Policies\CategoryPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Animal::class => AnimalPolicy::class,
        Service::class => ServicePolicy::class,
        Provider::class => ProviderPolicy::class,
        Booking::class => BookingPolicy::class,
        Collar::class => CollarPolicy::class,
        Category::class => CategoryPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
