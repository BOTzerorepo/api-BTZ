<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, string $message = 'Operación exitosa', int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    protected function created($data = null, string $message = 'Recurso creado exitosamente'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message, string $code = 'ERROR', int $status = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'code'    => $code,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    protected function notFound(string $message = 'Recurso no encontrado'): JsonResponse
    {
        return $this->error($message, 'NOT_FOUND', 404);
    }

    protected function unauthorized(string $message = 'No autorizado'): JsonResponse
    {
        return $this->error($message, 'UNAUTHORIZED', 401);
    }

    protected function forbidden(string $message = 'Acceso denegado'): JsonResponse
    {
        return $this->error($message, 'FORBIDDEN', 403);
    }

    protected function serverError(string $message = 'Error interno del servidor'): JsonResponse
    {
        return $this->error($message, 'SERVER_ERROR', 500);
    }
}
