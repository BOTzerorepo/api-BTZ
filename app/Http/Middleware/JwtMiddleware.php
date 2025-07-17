<?php

namespace App\Http\Middleware;

use Closure;
use Doctrine\Common\Lexer\Token;
use Google\Service\BinaryAuthorization\Jwt;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Exception;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenExpiredException) {
                return response()->json(['error' => 'Token has expired'], 401);
            } elseif ($e instanceof TokenInvalidException) {
                return response()->json(['error' => 'Token is invalid'], 401);
            } elseif ($e instanceof JWTException) {
                return response()->json(['error' => 'Token absent'], 401);
            } else {
                return response()->json(['error' => 'Authorization error'], 500);
            }
        }
        
        return $next($request);
    }
}
