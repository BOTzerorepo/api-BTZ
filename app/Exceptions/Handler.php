<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($e);
        }

        return parent::render($request, $e);
    }

    private function handleApiException(Throwable $e)
    {
        // JWT
        if ($e instanceof TokenExpiredException) {
            return response()->json([
                'success' => false,
                'code'    => 'TOKEN_EXPIRED',
                'message' => 'El token ha expirado. Por favor, inicie sesión nuevamente.',
            ], 401);
        }

        if ($e instanceof TokenInvalidException) {
            return response()->json([
                'success' => false,
                'code'    => 'TOKEN_INVALID',
                'message' => 'El token es inválido.',
            ], 401);
        }

        if ($e instanceof JWTException) {
            return response()->json([
                'success' => false,
                'code'    => 'TOKEN_MISSING',
                'message' => 'Token no proporcionado.',
            ], 401);
        }

        // Autenticación
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'code'    => 'UNAUTHORIZED',
                'message' => 'No autenticado. Por favor, inicie sesión.',
            ], 401);
        }

        // Autorización
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'success' => false,
                'code'    => 'FORBIDDEN',
                'message' => 'No tiene permisos para realizar esta acción.',
            ], 403);
        }

        // Validación
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'code'    => 'VALIDATION_ERROR',
                'message' => 'Los datos enviados no son válidos.',
                'errors'  => $e->errors(),
            ], 422);
        }

        // Modelo no encontrado
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'code'    => 'NOT_FOUND',
                'message' => 'El recurso solicitado no existe.',
            ], 404);
        }

        // Ruta no encontrada
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'code'    => 'ROUTE_NOT_FOUND',
                'message' => 'La ruta solicitada no existe.',
            ], 404);
        }

        // Método no permitido
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'code'    => 'METHOD_NOT_ALLOWED',
                'message' => 'Método HTTP no permitido.',
            ], 405);
        }

        // Error genérico
        return response()->json([
            'success' => false,
            'code'    => 'SERVER_ERROR',
            'message' => 'Error interno del servidor.',
        ], 500);
    }
}
