<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RailMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();

        if (!$user || !$user->hasRole('Rail')) {
            return response()->json([
                'success' => false,
                'code'    => 'UNAUTHORIZED',
                'message' => 'Acceso restringido a soporte Rail.',
            ], 401);
        }

        return $next($request);
    }
}
