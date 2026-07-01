<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ComercialMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();

        if (!$user || (!$user->hasRole('Comercial') && !$user->hasRole('AdminComercial'))) {
            return response()->json([
                'success' => false,
                'code'    => 'UNAUTHORIZED',
                'message' => 'Acceso restringido al módulo comercial.',
            ], 401);
        }

        return $next($request);
    }
}
