<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeoController extends Controller
{
    public function listActions(Request $r)
    {
        $tripId = (int)$r->get('trip_id');
        $limit  = (int)($r->get('limit', 100));

        $q = DB::table('geo_action_logs')
            ->when($tripId, fn($qq)=>$qq->where('trip_id', $tripId))
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return response()->json($q);
    }

    public function activeState(Request $r)
    {
        $tripId = (int)$r->get('trip_id');

        $q = DB::table('cntr_interest_point as cip')
            ->join('interest_points as ip','cip.interest_point_id','=','ip.id')
            ->when($tripId, fn($qq)=>$qq->where('cip.cntr_id_cntr',$tripId))
            ->select([
                'cip.cntr_id_cntr as trip_id',
                'ip.id as poi_id',
                'ip.description',
                'ip.type',
                'cip.order',
                'cip.activo as state', // 0/1/2
            ])
            ->orderBy('cip.cntr_id_cntr')
            ->orderBy('cip.order')
            ->get();

        return response()->json($q);
    }
}
