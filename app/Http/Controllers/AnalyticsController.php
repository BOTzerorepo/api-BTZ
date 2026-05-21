<?php

namespace App\Http\Controllers;

use App\Models\Carga;
use App\Models\cntr;
use App\Models\User;
use App\Models\Transport;
use App\Models\asign;
use App\Models\Driver;
use App\Models\truck;
use App\Models\trailer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private array $filtros = [];

    public function dashboard(Request $request)
    {
        $meses = (int) $request->get('meses', 6);
        $desde = Carbon::now()->subMonths($meses)->startOfMonth();

        $this->filtros = array_filter([
            'empresa'       => $request->get('empresa'),
            'shipper'       => $request->get('shipper'),
            'mes'           => $request->get('mes'),
            'rol'           => $request->get('rol'),
            'estado_carga'  => $request->get('estado_carga'),
            'estado_cntr'   => $request->get('estado_cntr'),
            'provincia'     => $request->get('provincia'),
            'linea_naviera' => $request->get('linea_naviera'),
            'transporte'    => $request->get('transporte'),
        ]);

        return response()->json([
            'filtros_activos'   => $this->filtros,
            'resumen'           => $this->resumenGeneral(),
            'usuarios'          => $this->metricas_usuarios($desde),
            'cargas'            => $this->metricas_cargas($desde),
            'contenedores'      => $this->metricas_contenedores(),
            'transportes'       => $this->metricas_transportes($desde),
            'actividad_interna' => $this->actividad_interna($desde),
            'generado_en'       => now()->toIso8601String(),
        ]);
    }

    // ── Query base de usuarios con rol (reutilizable) ────────
    private function baseUsuarios()
    {
        $q = DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereNull('users.deleted_at');

        if (!empty($this->filtros['rol']))     $q->where('roles.name', $this->filtros['rol']);
        if (!empty($this->filtros['empresa'])) $q->where('users.empresa', $this->filtros['empresa']);

        return $q;
    }

    // ── Filtros de carga ─────────────────────────────────────
    private function applyCargaFilters($query)
    {
        if (!empty($this->filtros['empresa']))       $query->where('carga.empresa', $this->filtros['empresa']);
        if (!empty($this->filtros['shipper']))       $query->where('carga.shipper', $this->filtros['shipper']);
        if (!empty($this->filtros['estado_carga']))  $query->where('carga.status', $this->filtros['estado_carga']);
        if (!empty($this->filtros['linea_naviera'])) $query->where('carga.oceans_line', $this->filtros['linea_naviera']);
        if (!empty($this->filtros['mes'])) {
            [$y, $m] = explode('-', $this->filtros['mes']);
            $query->whereYear('carga.created_at', $y)->whereMonth('carga.created_at', $m);
        }
        return $query;
    }

    private function applyCntrFilters($query)
    {
        if (!empty($this->filtros['estado_cntr'])) $query->where('main_status', $this->filtros['estado_cntr']);
        if (!empty($this->filtros['shipper'])) {
            $bookings = Carga::whereNull('deleted_at')->where('shipper', $this->filtros['shipper'])->pluck('booking');
            $query->whereIn('booking', $bookings);
        }
        return $query;
    }

    private function applyTransportFilters($query)
    {
        if (!empty($this->filtros['provincia']))  $query->where('transports.provincia', $this->filtros['provincia']);
        if (!empty($this->filtros['transporte'])) $query->where('transports.razon_social', $this->filtros['transporte']);
        return $query;
    }

    // ── Resumen ──────────────────────────────────────────────
    private function resumenGeneral(): array
    {
        $cargaQ = $this->applyCargaFilters(Carga::whereNull('deleted_at')->from('carga'));
        $cntrQ  = $this->applyCntrFilters(cntr::whereNull('deleted_at'));

        $usrQ = $this->baseUsuarios()->select(DB::raw('COUNT(DISTINCT users.id) as cnt'));
        $totalUsuarios = $usrQ->first()->cnt ?? 0;

        return [
            'total_usuarios'       => $totalUsuarios,
            'total_cargas'         => (clone $cargaQ)->count(),
            'cargas_activas'       => (clone $cargaQ)->whereNotIn('carga.status', $this->estadosTerminados())->count(),
            'total_contenedores'   => (clone $cntrQ)->count(),
            'total_transportes'    => Transport::whereNull('deleted_at')->count(),
            'total_choferes'       => Driver::count(),
            'total_camiones'       => truck::count(),
            'total_semirremolques' => trailer::count(),
            'total_asignaciones'   => asign::count(),
        ];
    }

    private function estadosTerminados(): array
    {
        return ['TERMINADA'];
    }

    // ── Usuarios ─────────────────────────────────────────────
    private function metricas_usuarios(Carbon $desde): array
    {
        // Distribución por rol
        $porRol = $this->baseUsuarios()
            ->select('roles.name as rol', DB::raw('COUNT(DISTINCT users.id) as total'))
            ->groupBy('roles.name')
            ->get();

        // Altas por mes (sólo período)
        $altasMes = $this->baseUsuarios()
            ->where('users.created_at', '>=', $desde)
            ->select(
                DB::raw("DATE_FORMAT(users.created_at,'%Y-%m') as mes"),
                DB::raw('COUNT(DISTINCT users.id) as total')
            )
            ->groupBy('mes')->orderBy('mes')->get();

        // Usuarios por cliente — cliente_id guarda el nombre directamente (no es FK)
        $porCliente = $this->baseUsuarios()
            ->whereNotNull('users.cliente_id')
            ->where('users.cliente_id', '!=', '')
            ->select(
                'users.cliente_id as nombre_cliente',
                DB::raw('COUNT(DISTINCT users.id) as total')
            )
            ->groupBy('users.cliente_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Usuarios por transporte — transport_id es FK real a transports.id
        $porTransporte = $this->baseUsuarios()
            ->leftJoin('transports', 'users.transport_id', '=', 'transports.id')
            ->whereNotNull('users.transport_id')
            ->select(
                'users.transport_id',
                'transports.razon_social as nombre_transporte',
                DB::raw('COUNT(DISTINCT users.id) as total')
            )
            ->groupBy('users.transport_id', 'transports.razon_social')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Últimas altas — cliente_id es el nombre, transport_id es FK
        $recientes = $this->baseUsuarios()
            ->where('users.created_at', '>=', $desde)
            ->leftJoin('transports as t2', 'users.transport_id', '=', 't2.id')
            ->select(
                'users.id',
                'users.username',
                'users.email',
                'users.created_at',
                'users.last_login_at',
                'roles.name as rol',
                'users.cliente_id as nombre_cliente',
                'users.transport_id',
                't2.razon_social as nombre_transporte'
            )
            ->orderByDesc('users.created_at')
            ->limit(20)
            ->get();

        // Interacciones: solo via status.user_status (audits.user_id = NULL por config JWT)
        // También correlacionamos con audits via URL pattern /api/user/{id}
        $interacciones = DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin(
                DB::raw('(SELECT user_status, COUNT(*) as cnt FROM `status` GROUP BY user_status) as sts'),
                'users.username', '=', 'sts.user_status'
            )
            ->leftJoin(
                DB::raw('(SELECT CAST(REGEXP_REPLACE(url, \'^.*/user/([0-9]+).*$\', \'\\\\1\') AS UNSIGNED) as uid, COUNT(*) as cnt FROM audits WHERE url REGEXP \'/user/[0-9]+\' GROUP BY uid) as aud_url'),
                'users.id', '=', 'aud_url.uid'
            )
            ->whereNull('users.deleted_at');

        if (!empty($this->filtros['rol']))     $interacciones->where('roles.name', $this->filtros['rol']);
        if (!empty($this->filtros['empresa'])) $interacciones->where('users.empresa', $this->filtros['empresa']);

        $interacciones = $interacciones
            ->select(
                'users.id',
                'users.username',
                'users.email',
                'users.last_login_at',
                DB::raw('COALESCE(roles.name, "Sin rol") as rol'),
                DB::raw('COALESCE(aud_url.cnt, 0) as ediciones_perfil'),
                DB::raw('COALESCE(sts.cnt, 0) as cambios_estado'),
                DB::raw('COALESCE(aud_url.cnt, 0) + COALESCE(sts.cnt, 0) as total_interacciones')
            )
            ->orderByDesc('total_interacciones')
            ->limit(20)
            ->get();

        // Usuarios sin rol Spatie pero con permiso directo en users.permiso (inconsistencia de datos)
        $sinRol = DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('transports', 'users.transport_id', '=', 'transports.id')
            ->whereNull('users.deleted_at')
            ->whereNull('model_has_roles.role_id')
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('users.permiso')->where('users.permiso', '!=', '');
                })->orWhere(function ($q2) {
                    $q2->whereNotNull('users.cliente_id')->where('users.cliente_id', '!=', '');
                })->orWhereNotNull('users.transport_id');
            })
            ->select(
                'users.id',
                'users.username',
                'users.email',
                'users.created_at',
                'users.permiso',
                'users.cliente_id as nombre_cliente',
                'users.transport_id',
                'transports.razon_social as nombre_transporte'
            )
            ->orderByDesc('users.created_at')
            ->get();

        return [
            'por_rol'        => $porRol,
            'altas_por_mes'  => $altasMes,
            'por_cliente'    => $porCliente,
            'por_transporte' => $porTransporte,
            'recientes'      => $recientes,
            'interacciones'  => $interacciones,
            'sin_rol'        => $sinRol,
        ];
    }

    // ── Cargas ───────────────────────────────────────────────
    private function metricas_cargas(Carbon $desde): array
    {
        $base    = fn() => $this->applyCargaFilters(Carga::whereNull('deleted_at')->from('carga'));
        $baseAll = fn() => $this->applyCargaFilters(Carga::whereNull('deleted_at')->from('carga'));

        $isFiltroMes = !empty($this->filtros['mes']);
        if ($isFiltroMes) {
            $porMes = ($base)()
                ->select(DB::raw("DATE_FORMAT(carga.created_at,'%Y-%m-%d') as mes"), DB::raw('COUNT(*) as total'))
                ->groupBy('mes')->orderBy('mes')->get();
        } else {
            $porMes = ($base)()
                ->where('carga.created_at', '>=', $desde)
                ->select(DB::raw("DATE_FORMAT(carga.created_at,'%Y-%m') as mes"), DB::raw('COUNT(*) as total'))
                ->groupBy('mes')->orderBy('mes')->get();
        }

        // Estados activos (excluye terminadas)
        $porEstado = ($baseAll)()
            ->whereNotIn('carga.status', $this->estadosTerminados())
            ->whereNotNull('carga.status')
            ->select('carga.status', DB::raw('COUNT(*) as total'))
            ->groupBy('carga.status')->orderByDesc('total')->get();

        // Conteo de terminadas (para referencia)
        $totalTerminadas = ($baseAll)()
            ->whereIn('carga.status', $this->estadosTerminados())
            ->count();

        // Cargas por usuario Customer (quien las creó)
        $porUsuario = ($baseAll)()
            ->whereNotNull('carga.user')
            ->select('carga.user', DB::raw('COUNT(*) as total'))
            ->groupBy('carga.user')->orderByDesc('total')->limit(10)->get();

        $porShipper = ($baseAll)()
            ->whereNotNull('carga.shipper')
            ->select('carga.shipper', DB::raw('COUNT(*) as total'))
            ->groupBy('carga.shipper')->orderByDesc('total')->limit(10)->get();

        $porLinea = ($baseAll)()
            ->whereNotNull('carga.oceans_line')
            ->select('carga.oceans_line', DB::raw('COUNT(*) as total'))
            ->groupBy('carga.oceans_line')->orderByDesc('total')->limit(8)->get();

        return [
            'por_mes'          => $porMes,
            'detalle_dia'      => $isFiltroMes,
            'por_estado'       => $porEstado,
            'total_terminadas' => $totalTerminadas,
            'por_usuario'      => $porUsuario,
            'por_shipper'      => $porShipper,
            'por_linea'        => $porLinea,
        ];
    }

    // ── Contenedores ─────────────────────────────────────────
    private function metricas_contenedores(): array
    {
        $base = fn() => $this->applyCntrFilters(cntr::whereNull('deleted_at'));

        $porEstado = ($base)()
            ->whereNotIn('main_status', $this->estadosTerminados())
            ->whereNotNull('main_status')
            ->select('main_status', DB::raw('COUNT(*) as total'))
            ->groupBy('main_status')->orderByDesc('total')->get();

        $totalTerminados = ($base)()->whereIn('main_status', $this->estadosTerminados())->count();

        return [
            'por_tipo'         => ($base)()->select('cntr_type', DB::raw('COUNT(*) as total'))->groupBy('cntr_type')->orderByDesc('total')->get(),
            'por_estado'       => $porEstado,
            'total_terminados' => $totalTerminados,
            'con_feedback'     => ($base)()->whereNotNull('feedback_customer')->where('feedback_customer', '!=', '')->count(),
            'calificacion_avg' => round((float) ($base)()->whereNotNull('calificacion_carga')->avg('calificacion_carga'), 2),
        ];
    }

    // ── Transportes ──────────────────────────────────────────
    private function metricas_transportes(Carbon $desde): array
    {
        $base = function () {
            $q = DB::table('transports')->whereNull('transports.deleted_at');
            $this->applyTransportFilters($q);
            return $q;
        };

        $masActivos = $base()
            ->leftJoin('asign', 'transports.razon_social', '=', 'asign.transport')
            ->select(
                'transports.razon_social',
                'transports.provincia',
                DB::raw('COUNT(asign.id) as total_asignaciones')
            )
            ->groupBy('transports.razon_social', 'transports.provincia')
            ->orderByDesc('total_asignaciones')
            ->limit(10)->get();

        // Camiones por transporte con desglose alta_aker (total en sistema)
        // trucks.alta_aker es varchar '1' / '0' directamente en la tabla trucks
        $camionesAker = $base()
            ->leftJoin('trucks', 'transports.id', '=', 'trucks.transport_id')
            ->select(
                'transports.razon_social',
                'transports.provincia',
                DB::raw("COUNT(trucks.id) as total"),
                DB::raw("SUM(CASE WHEN trucks.alta_aker = '1' THEN 1 ELSE 0 END) as con_aker"),
                DB::raw("SUM(CASE WHEN trucks.alta_aker = '0' OR trucks.alta_aker IS NULL THEN 1 ELSE 0 END) as sin_aker")
            )
            ->groupBy('transports.razon_social', 'transports.provincia')
            ->orderByDesc('total')
            ->limit(10)->get();

        // Camiones activos en el período — distintos camiones que participaron en asignaciones
        // asign.truck = domain del camión, asign.transport = razon_social
        $camionesEnPeriodo = DB::table('asign')
            ->join('transports', 'asign.transport', '=', 'transports.razon_social')
            ->leftJoin('trucks', 'asign.truck', '=', 'trucks.domain')
            ->whereNull('asign.deleted_at')
            ->whereNotNull('asign.truck')
            ->where('asign.truck', '!=', '')
            ->where('asign.created_at', '>=', $desde);

        if (!empty($this->filtros['mes'])) {
            [$y, $m] = explode('-', $this->filtros['mes']);
            $camionesEnPeriodo->whereYear('asign.created_at', $y)->whereMonth('asign.created_at', $m);
        }
        if (!empty($this->filtros['provincia']))  $camionesEnPeriodo->where('transports.provincia', $this->filtros['provincia']);
        if (!empty($this->filtros['transporte'])) $camionesEnPeriodo->where('transports.razon_social', $this->filtros['transporte']);

        $camionesEnPeriodo = $camionesEnPeriodo
            ->select(
                'asign.transport as razon_social',
                'transports.provincia',
                DB::raw("COUNT(DISTINCT asign.truck) as camiones_activos"),
                DB::raw("COUNT(DISTINCT asign.booking) as cargas_cubiertas"),
                DB::raw("COUNT(DISTINCT CASE WHEN trucks.alta_aker = '1' THEN asign.truck END) as con_aker"),
                DB::raw("COUNT(DISTINCT CASE WHEN IFNULL(trucks.alta_aker,'0') != '1' THEN asign.truck END) as sin_aker")
            )
            ->groupBy('asign.transport', 'transports.provincia')
            ->orderByDesc('camiones_activos')
            ->limit(15)
            ->get();

        return [
            'por_provincia'       => $base()->whereNotNull('transports.provincia')
                ->select('transports.provincia', DB::raw('COUNT(*) as total'))
                ->groupBy('transports.provincia')->orderByDesc('total')->get(),
            'mas_activos'         => $masActivos,
            'camiones_aker'       => $camionesAker,
            'camiones_en_periodo' => $camionesEnPeriodo,
        ];
    }

    // ── Actividad interna ────────────────────────────────────
    private function actividad_interna(Carbon $desde): array
    {
        $statusQ = DB::table('status')->where('created_at', '>=', $desde);
        $auditsQ = DB::table('audits')->where('created_at', '>=', $desde);

        if (!empty($this->filtros['mes'])) {
            [$y, $m] = explode('-', $this->filtros['mes']);
            $statusQ->whereYear('created_at', $y)->whereMonth('created_at', $m);
            $auditsQ->whereYear('created_at', $y)->whereMonth('created_at', $m);
        }

        return [
            'cambios_estado_por_mes' => (clone $statusQ)
                ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m') as mes"), DB::raw('COUNT(*) as total'))
                ->groupBy('mes')->orderBy('mes')->get(),
            'usuarios_mas_activos' => (clone $statusQ)
                ->whereNotNull('user_status')
                ->select('user_status', DB::raw('COUNT(*) as total'))
                ->groupBy('user_status')->orderByDesc('total')->limit(10)->get(),
            'audits_resumen' => (clone $auditsQ)
                ->select('auditable_type', 'event', DB::raw('COUNT(*) as total'))
                ->groupBy('auditable_type', 'event')->orderByDesc('total')->get()
                ->map(fn($r) => tap($r, fn($r) => $r->auditable_type = class_basename($r->auditable_type))),

            // Endpoints más usados — extraídos del campo url de audits
            'endpoints_uso' => (clone $auditsQ)
                ->whereNotNull('url')
                ->selectRaw("
                    REGEXP_REPLACE(
                        REGEXP_REPLACE(
                            REGEXP_REPLACE(url, '^https?://[^/]+', ''),
                            '/[0-9]+', '/{id}'
                        ),
                        '\\\\?.*$', ''
                    ) as endpoint,
                    COUNT(*) as total
                ")
                ->groupByRaw("
                    REGEXP_REPLACE(
                        REGEXP_REPLACE(
                            REGEXP_REPLACE(url, '^https?://[^/]+', ''),
                            '/[0-9]+', '/{id}'
                        ),
                        '\\\\?.*$', ''
                    )
                ")
                ->orderByDesc('total')
                ->limit(20)
                ->get(),

            // Evolución de llamadas por mes (total de cambios auditados)
            'llamadas_por_mes' => (clone $auditsQ)
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total")
                ->groupBy('mes')->orderBy('mes')->get(),

            // Nota sobre audits sin user_id
            'nota_audits' => 'Los registros de auditoría no tienen user_id asociado (requiere configurar resolver JWT en OwenIt). La actividad de usuario se mide vía tabla status.',
        ];
    }
}
