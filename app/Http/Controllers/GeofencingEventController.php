<?php

namespace App\Http\Controllers;

use App\Models\GeofencingEvent;
use Illuminate\Http\Request;

class GeofencingEventController extends Controller
{
    public function index(Request $r)
    {
        $q = GeofencingEvent::query();

        // Filtros
        if ($r->has('start')) {
            $q->where('entered_at', '>=', $r->start);
        }
        if ($r->has('end')) {
            $q->where('entered_at', '<=', $r->end);
        }
        if ($r->zone) {
            $q->where('zone_type', $r->zone);
        }
        if ($r->cntr) {
            $q->where('cntr_number', 'LIKE', "%{$r->cntr}%");
        }
        if ($r->plate) {
            $q->where('truck_plate', 'LIKE', "%{$r->plate}%");
        }
        // Orden por defecto: más reciente primero
        $q->orderBy('entered_at', 'desc');

        return $q->get(); // devolvemos array directo, el dashboard lo consume así
    }
}

