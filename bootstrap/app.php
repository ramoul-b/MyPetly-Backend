<?php

use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Pour déclarer un “middleware nommé”
        // On passe un tableau associatif :
        // 'alias' => ClasseMiddleware::class

        $middleware->append(HandleCors::class);

        $middleware->alias([
            'auth' => Authenticate::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        // Exemple si tu veux ajouter d’autres alias :
        // $middleware->alias([
        //     'auth' => \App\Http\Middleware\Authenticate::class,
        //     'role' => \App\Http\Middleware\CheckRole::class,
        // ]);
        
        // Pour un middleware “global” qui s’applique à TOUTES les routes :
        // $middleware->global([
        //     \App\Http\Middleware\SomeGlobalMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Configuration custom pour la gestion des exceptions, si besoin
    })
    ->create();
