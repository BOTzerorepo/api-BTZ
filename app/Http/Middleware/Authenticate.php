<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate 
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (!$request->user()) {
            return $this->redirectTo($request);
        }

        return $next($request);
    }
}
