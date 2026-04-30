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

    /**
     * Resumen de gestión satelital y CMA API para el dashboard.
     */
    public function satelitalSummary()
    {
        // 1. Viajes reportando (últimos 50 activos o recientes)
        // Ordenamos por last_report desc (los más recientes arriba)
        $reportingTrips = \Illuminate\Support\Facades\DB::table('geo_action_logs')
            ->select('trip_id', 'cntr_number', 'domain', 
                \Illuminate\Support\Facades\DB::raw('MIN(created_at) as first_report'), 
                \Illuminate\Support\Facades\DB::raw('MAX(created_at) as last_report'))
            ->groupBy('trip_id', 'cntr_number', 'domain')
            ->orderBy('last_report', 'desc')
            ->limit(50)
            ->get();

        // 2. Unidades con T.O. de CMA (Activos y Terminados) + Flags
        // NUEVA DEFINICIÓN DE ACTIVO: truck asignado a una carga con aker activo (1)
        $cmaUnits = \Illuminate\Support\Facades\DB::table('cntr')
            ->join('carga', 'cntr.booking', '=', 'carga.booking')
            ->leftJoin('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->leftJoin('trucks', 'asign.truck', '=', 'trucks.domain')
            ->whereNotNull('carga.cma_t_o')
            ->select(
                'cntr.cntr_number', 
                'carga.cma_t_o', 
                'cntr.main_status', 
                'cntr.flag_cma', 
                'cntr.updated_at',
                'trucks.alta_aker',
                \Illuminate\Support\Facades\DB::raw("CASE WHEN trucks.alta_aker = 1 AND cntr.main_status != 'TERMINADA' THEN 'ACTIVO' ELSE 'INACTIVO' END as aker_status")
            )
            ->orderByRaw("CASE WHEN trucks.alta_aker = 1 AND cntr.main_status != 'TERMINADA' THEN 0 ELSE 1 END")
            ->orderBy('cntr.updated_at', 'desc')
            ->get();

        // 3. Estadísticas de CMA (últimos 7 días)
        $cmaEventsStats = \Illuminate\Support\Facades\DB::table('cma_logs_events')
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->get();

        $cmaCoordsStats = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->get();

        // 4. Resumen de Acciones Geo (ENTER/EXIT)
        $actionsSummary = \Illuminate\Support\Facades\DB::table('geo_action_logs')
            ->select('action_type', 'point_type', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('action_type', 'point_type')
            ->get();

        // 5. Últimos eventos de CMA con detalle
        $recentCmaEvents = \Illuminate\Support\Facades\DB::table('cma_logs_events')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return $this->success([
            'reporting_trips' => $reportingTrips,
            'cma_units' => $cmaUnits,
            'cma_stats' => [
                'events' => $cmaEventsStats,
                'coords' => $cmaCoordsStats,
            ],
            'actions_summary' => $actionsSummary,
            'recent_cma' => $recentCmaEvents,
        ]);
    }

    /**
     * Detalle cronológico de un viaje (coordenadas y eventos).
     */
    public function satelitalTripDetail($cntrNumber)
    {
        $events = \Illuminate\Support\Facades\DB::table('cma_logs_events')
            ->where('equipment_reference', $cntrNumber)
            ->select('created_at', 'event_type as type', 'status_cma as detail', \Illuminate\Support\Facades\DB::raw("'EVENT' as category"))
            ->get();

        $coords = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')
            ->where('equipmentReference', $cntrNumber)
            ->select('created_at', \Illuminate\Support\Facades\DB::raw("'COORD' as type"), \Illuminate\Support\Facades\DB::raw("CONCAT(lat, ', ', longitude) as detail"), \Illuminate\Support\Facades\DB::raw("'COORD' as category"))
            ->get();

        \Illuminate\Support\Facades\Log::debug("SatelitalTripDetail for $cntrNumber: Events=" . $events->count() . ", Coords=" . $coords->count());

        $history = $events->concat($coords)->sortByDesc('created_at')->values();

        return $this->success($history);
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

    public function apiBTZLogs(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $type = $request->query('type', 'geo'); // 'geo' or 'system'

        if ($type === 'geo') {
            $logs = DB::table('geo_action_logs')
                ->orderByDesc('id')
                ->limit($limit)
                ->get();
        } else {
            $logs = DB::table('logapi')
                ->orderByDesc('id')
                ->limit($limit)
                ->get();
        }

        return response()->json([
            'ok' => true,
            'data' => $logs
        ]);
    }

    public function getLogFile(Request $request)
    {
        $lines = (int) $request->query('lines', 1000);
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            return response()->json(['ok' => false, 'message' => 'Log file not found'], 404);
        }

        // Leer las últimas líneas usando tail si estamos en linux/mac
        $output = shell_exec("tail -n $lines " . escapeshellarg($logPath));
        
        return response()->json([
            'ok' => true,
            'filename' => 'laravel.log',
            'size' => filesize($logPath),
            'content' => $output
        ]);
    }
}
