<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class TripRepository
{
    /**
     * Devuelve viajes activos con sus POIs ordenados.
     * Agrupado por trip_id. Cada item: domain, cntr_number, poi_id, latitude, longitude, radius, poi_state (0/1/2), poi_order.
     */
    public function activeTripsWithPois()
    {
        return DB::table('asign as a')
            ->join('cntr as c', 'a.cntr_number', '=', 'c.cntr_number')
            ->join('cntr_interest_point as cip', 'c.id_cntr', '=', 'cip.cntr_id_cntr')
            ->join('interest_points as ip', 'cip.interest_point_id', '=', 'ip.id')
            ->whereNull('a.deleted_at')
            ->whereNotNull('a.truck')
            ->where('c.main_status', '!=', 'TERMINADA')
            ->select([
                'a.truck as domain',
                'c.id_cntr as trip_id',
                'c.cntr_number',
                'c.main_status',
                'ip.id as poi_id',
                'ip.description as poi_desc',
                'ip.type as poi_type',
                'ip.latitude as lat',
                'ip.longitude as lng',
                'ip.radius as radius_in',
                'cip.activo as poi_state', // 0:pend, 1:inside, 2:done/out
                'cip.order as poi_order',
            ])
            ->orderBy('cip.order')
            ->get()
            ->groupBy('trip_id');
    }
}
