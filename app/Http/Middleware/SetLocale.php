<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Vérifie la langue dans les paramètres de requête ou dans les en-têtes HTTP
        $locale = $request->query('lang', $request->header('Accept-Language', config('app.locale')));

        // Si la langue est supportée, on la définit
        if (in_array($locale, ['en', 'fr', 'it'])) {
            App::setLocale($locale);
        } else {
            // Sinon, on utilise la langue par défaut définie dans config/app.php
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}


