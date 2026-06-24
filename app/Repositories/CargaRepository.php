<?php

namespace App\Repositories;

use App\Models\Carga;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CargaRepository
{
    /**
     * Construye la query base de cargas con joins de cntr y asign.
     */
    private function baseQuery()
    {
        return Carga::whereNull('carga.deleted_at')
        ->join('cntr', 'cntr.booking', '=', 'carga.booking')
        ->leftJoin('asign', function ($j) {
            $j->on('asign.cntr_number', '=', 'cntr.cntr_number')
              ->whereNull('asign.deleted_at');   // keep the deleted_at filter ON the join, not in WHERE
        })
        ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
        ->whereNull('cntr.deleted_at');
    }

    /**
     * Aplica filtros según el permiso del usuario.
     */
    private function applyPermissionFilter($query, User $user)
    {
        if ($user->permiso === 'Traffic' || $user->permiso === 'Master') {
            $query->where('carga.empresa', '=', $user->empresa);
        } elseif ($user->permiso === 'Transport') {
            $query->where('carga.empresa', '=', $user->empresa);
        } elseif ($user->permiso === 'ClienteEmpresa') {
            $query->where(function ($q) use ($user) {
                $q->where('carga.cliente_id', $user->id)
                  ->orWhere(function ($sub) use ($user) {
                      $sub->whereNull('carga.cliente_id');
                      if (!empty($user->cliente_id)) {
                          $sub->where(function ($inner) use ($user) {
                              $inner->where('carga.trader', $user->cliente_id)
                                    ->orWhere('carga.shipper', $user->cliente_id)
                                    ->orWhere('carga.importador', $user->cliente_id);
                          });
                      }
                  });
            });
        } else {
            $query->where('carga.empresa', '=', $user->empresa)
                  ->where('carga.user', '=', $user->username);
        }

        return $query;
    }

    /**
     * Cargas de esta semana (lunes a domingo) activas.
     */
    public function getThisWeek(User $user)
    {
        $start = Carbon::parse('last monday')->startOfDay();
        $end   = Carbon::parse('next Sunday')->endOfDay();

        $query = $this->baseQuery()
            ->whereBetween('carga.load_date', [$start, $end])
            ->where('carga.status', '!=', 'TERMINADA')
            ->orderBy('carga.load_date', 'ASC');

        return $this->applyPermissionFilter($query, $user)->get();
    }

    /**
     * Cargas anteriores a esta semana, aún activas.
     */
    public function getLastWeek(User $user)
    {
        $start = Carbon::parse('last monday')->startOfDay();

        $query = $this->baseQuery()
            ->where('carga.load_date', '<', $start)
            ->where('carga.status', '!=', 'TERMINADA')
            ->orderBy('carga.load_date', 'ASC');

        return $this->applyPermissionFilter($query, $user)->get();
    }

    /**
     * Cargas posteriores a esta semana, aún activas.
     */
    public function getNextWeek(User $user)
    {
        $end = Carbon::parse('next Sunday')->endOfDay();

        $query = $this->baseQuery()
            ->where('carga.load_date', '>', $end)
            ->where('carga.status', '!=', 'TERMINADA')
            ->orderBy('carga.load_date', 'ASC');

        return $this->applyPermissionFilter($query, $user)->get();
    }

    /**
     * Cargas asociadas a un número de contenedor específico.
     */
    public function getByCntr(string $cntrNumber)
    {
        return Carga::whereNull('carga.deleted_at')
            ->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->join('customer_load_places', 'customer_load_places.description', '=', 'carga.load_place')
            ->join('customer_unload_places', 'customer_unload_places.description', '=', 'carga.unload_place')
            ->leftJoin('aduanas as adu_exp',  'adu_exp.description',  '=', 'carga.custom_place')
            ->leftJoin('aduanas as adu_impo', 'adu_impo.description', '=', 'carga.custom_place_impo')
            ->select(
                'carga.id',
                'carga.booking',
                'carga.load_date',
                'carga.ref_customer',
                'carga.cma_t_o',
                'cntr.id_cntr',
                'cntr.cntr_number',
                'asign.driver',
                'asign.transport',
                'customer_load_places.description as clp_description',
                'customer_load_places.latitud as clp_latitud',
                'customer_load_places.longitud as clp_longitud',
                'customer_unload_places.description as cup_description',
                'customer_unload_places.latitud as cup_latitud',
                'customer_unload_places.longitud as cup_longitud',
                DB::raw('COALESCE(adu_impo.description, adu_exp.description) as aduana_description'),
                DB::raw('COALESCE(adu_impo.lat, adu_exp.lat) as aduana_lat'),
                DB::raw('COALESCE(adu_impo.lon, adu_exp.lon) as aduana_lon')
            )
            ->whereNull('cntr.deleted_at')
            ->whereNull('asign.deleted_at')
            ->where('cntr.cntr_number', $cntrNumber)
            ->orderBy('carga.load_date', 'ASC')
            ->get();
    }
}
