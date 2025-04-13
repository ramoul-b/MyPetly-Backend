<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::user()->hasPermissionTo($permission)) {
            return response()->json([
                'message' => 'Vous n\'avez pas la permission d\'effectuer cette action'
            ], 403);
        }

        return $next($request);
    }
}
