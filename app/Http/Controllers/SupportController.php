<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Carga;
use App\Models\cntr;
use App\Models\Transport;
use App\Models\Driver;
use App\Models\Fletero;
use App\Models\asign;
use App\Models\razonSocial;
use App\Models\InterestPoint;
use App\Models\logapi;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SupportController extends Controller
{
    use ApiResponse;

    // ─── Usuarios ────────────────────────────────────────────────────────────

    /**
     * Lista todos los usuarios, incluyendo los eliminados (soft-deleted).
     */
    public function users()
    {
        $users = User::withTrashed()
            ->with('roles')
            ->get()
            ->map(function ($user) {
                return [
                    'id'           => $user->id,
                    'username'     => $user->username,
                    'email'        => $user->email,
                    'empresa'      => $user->empresa,
                    'celular'      => $user->celular,
                    'permiso'      => $user->permiso,
                    'transport_id' => $user->transport_id,
                    'cliente_id'   => $user->cliente_id,
                    'role'         => $user->roles->first()?->name,
                    'deleted_at'   => $user->deleted_at,
                    'created_at'   => $user->created_at,
                ];
            });

        return $this->success($users);
    }

    /**
     * Editar un usuario (incluyendo uno soft-deleted).
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'username'     => 'sometimes|string|max:255',
            'email'        => 'sometimes|email|max:255',
            'celular'      => 'nullable|string|max:20',
            'empresa'      => 'nullable|string|max:255',
            'permiso'      => 'nullable|string|max:50',
            'transport_id' => 'nullable',
            'cliente_id'   => 'nullable',
        ]);

        $transport_id = $request->input('transport_id');
        if (is_array($transport_id)) {
            $validated['transport_id'] = implode(',', $transport_id);
        }

        $user->fill($validated);
        $user->save();

        return $this->success($user, 'Usuario actualizado correctamente.');
    }

    /**
     * Restaurar un usuario eliminado (soft-delete).
     */
    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (!$user->trashed()) {
            return $this->error('El usuario no está eliminado.', 'VALIDATION_ERROR', 422);
        }

        $user->restore();

        return $this->success($user, 'Usuario restaurado correctamente.');
    }

    /**
     * Eliminar definitivamente un usuario.
     */
    public function forceDeleteUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return $this->success(null, 'Usuario eliminado definitivamente.');
    }

    /**
     * Últimos logs de actividad de un usuario.
     */
    public function userLogs($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $logs = logapi::where('user', $user->username)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return $this->success([
            'user' => $user->username,
            'logs' => $logs,
        ]);
    }

    // ─── Impersonación ───────────────────────────────────────────────────────

    /**
     * Genera un JWT como si fuera el usuario indicado.
     * Solo accesible por Rail. El token tiene TTL reducido (2 horas).
     */
    public function impersonate($id)
    {
        $target = User::findOrFail($id);

        // No permitir impersonar a otro Rail
        if ($target->hasRole('Rail')) {
            return $this->error('No se puede impersonar a un usuario Rail.', 'UNAUTHORIZED', 403);
        }

        $token = JWTAuth::fromUser($target);
        $roleName = $target->roles->first()?->name;

        $permissions = [];
        if ($roleName) {
            $permissions = \Spatie\Permission\Models\Role::findByName($roleName, 'web')->permissions->pluck('name')->toArray();
        }

        return $this->success([
            'token'    => $token,
            'type'     => 'bearer',
            'username' => $target->username,
            'role'     => $target->roles->first()?->name,
            'company'  => $target->empresa,
            'permiso'  => $permissions
        ], "Impersonando a {$target->username}.");
    }

    // ─── Restore de otras entidades ──────────────────────────────────────────

    /**
     * Lista los registros eliminados de una entidad.
     * entity: carga | cntr | transport | driver | fletero | asign | razon_social | interest_point
     */
    public function trashed($entity)
    {
        $model = $this->resolveModel($entity);

        if (!$model) {
            return $this->error("Entidad '{$entity}' no reconocida.", 'NOT_FOUND', 404);
        }

        $records = $model::onlyTrashed()->get();

        return $this->success($records);
    }

    /**
     * Restaura un registro eliminado de cualquier entidad.
     */
    public function restore($entity, $id)
    {
        $model = $this->resolveModel($entity);

        if (!$model) {
            return $this->error("Entidad '{$entity}' no reconocida.", 'NOT_FOUND', 404);
        }

        $record = $model::withTrashed()->findOrFail($id);

        if (!$record->trashed()) {
            return $this->error('El registro no está eliminado.', 'VALIDATION_ERROR', 422);
        }

        $record->restore();

        return $this->success($record, 'Registro restaurado correctamente.');
    }

    /**
     * Elimina definitivamente un registro de cualquier entidad.
     */
    public function forceDelete($entity, $id)
    {
        $model = $this->resolveModel($entity);

        if (!$model) {
            return $this->error("Entidad '{$entity}' no reconocida.", 'NOT_FOUND', 404);
        }

        $record = $model::withTrashed()->findOrFail($id);
        $record->forceDelete();

        return $this->success(null, 'Registro eliminado definitivamente.');
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function resolveModel(string $entity): ?string
    {
        $map = [
            'carga'          => Carga::class,
            'cntr'           => cntr::class,
            'transport'      => Transport::class,
            'driver'         => Driver::class,
            'fletero'        => Fletero::class,
            'asign'          => asign::class,
            'razon_social'   => razonSocial::class,
            'interest_point' => InterestPoint::class,
        ];

        return $map[$entity] ?? null;
    }
}
