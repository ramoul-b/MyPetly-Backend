<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $accept = $request->header('Accept');

        if ($accept && $accept !== '*/*' && ! str_contains($accept, 'application/json')) {
            return response()->json([
                'message' => 'Not Acceptable.',
            ], SymfonyResponse::HTTP_NOT_ACCEPTABLE);
        }

        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
