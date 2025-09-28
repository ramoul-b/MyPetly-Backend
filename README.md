<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
Déploiement GitHub Actions testé

## Setup

1. Run `composer install` to install PHP dependencies (including Faker).
2. Execute `php artisan migrate` to create database tables.
3. Seed the database with `php artisan db:seed` or
   `php artisan db:seed --class=MarketplaceSeeder`.

## Authentication

This project uses Laravel Sanctum for API authentication. By default, the
`SANCTUM_TOKEN_EXPIRATION` environment variable is set to `60 * 24`
minutes (24 hours), which feeds the `config/sanctum.php` `expiration`
option. Tokens automatically inherit this duration unless you override
the variable. Each time a user authenticates or refreshes their session,
previous personal access tokens are revoked before issuing a new one so
that only the latest credential remains valid.

If you change the token expiration or other Sanctum settings, run
`php artisan config:clear` during deployment to apply the updated
configuration.

## Marketplace APIs

The marketplace module exposes dedicated endpoints for coupons, inventory tracking and store configuration. All routes are available under the `/api/v1` prefix and protected by the `auth:sanctum` and `locale` middlewares unless stated otherwise.

### Coupons

- `GET /api/v1/coupons` — List coupons with optional filters (`store_id`, `code`, `is_active`).
- `POST /api/v1/coupons` — Create a coupon linked to a store and optionally to a product.
- `GET /api/v1/coupons/{id}` — Retrieve coupon details.
- `PUT /api/v1/coupons/{id}` — Update coupon metadata or activation settings.
- `DELETE /api/v1/coupons/{id}` — Remove a coupon.

### Inventory Movements

- `GET /api/v1/inventory-movements` — Paginated history of stock adjustments per store/product.
- `POST /api/v1/inventory-movements` — Register an incoming or outgoing stock movement and update product stock.
- `GET /api/v1/inventory-movements/{id}` — Inspect a specific movement.
- `PUT /api/v1/inventory-movements/{id}` — Amend the movement (type, quantity, notes) while keeping stock consistent.
- `DELETE /api/v1/inventory-movements/{id}` — Roll back the movement and restore product stock.

### Store Settings

- `GET /api/v1/store-settings` — List settings for all stores.
- `POST /api/v1/store-settings` — Create configuration for a store (currency, timezone, notifications, etc.).
- `GET /api/v1/store-settings/{id}` — Display a store setting with its related store.
- `PUT /api/v1/store-settings/{id}` — Update configuration values such as locale or low stock threshold.
- `DELETE /api/v1/store-settings/{id}` — Remove the configuration record.

### Administrative & Approval Workflows

- `PATCH /api/v1/providers/{provider}/status` — Update a provider's review
  status. Requires the `approve-providers` permission.
- `GET /api/v1/admin/dashboard/stats` — Retrieve aggregated metrics for the
  admin dashboard. Accessible to users with the `view_admin_dashboard`
  permission or the `admin`/`super-admin` roles.
