<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardCmaController extends Controller
{
    public function index()
    {$events = DB::table('cma_logs_events')
        ->select(
            'equipment_reference',
            'carrier_booking_reference',
            'event_created_at AS ts',
            DB::raw("'event' AS type"),
            'event_type',
            'transport_event_type_code',
            'equipment_event_type_code',
            DB::raw("NULL AS lat"),
            DB::raw("NULL AS longitude")
        );
    
    $coords = DB::table('cma_logs_coordinate')
        ->select(
            'equipmentReference AS equipment_reference',
            'carrierBookingReference AS carrier_booking_reference',
            'hora AS ts',
            DB::raw("'coord' AS type"),
            DB::raw("NULL AS event_type"),
            DB::raw("NULL AS transport_event_type_code"),
            DB::raw("NULL AS equipment_event_type_code"),
            'lat',
            'longitude'
        );
    
    $union = $events->unionAll($coords)
        ->orderBy('equipment_reference')
        ->orderBy('ts')
        ->get()
        ->groupBy('equipment_reference');
    

        // Determinar si la carga terminó
        $inProcess = collect();
        $finished  = collect();

        foreach ($union as $container => $records) {
            $finishedEvent = $records->first(function($r){
                return $r->type === 'event' && in_array($r->event_type, [
                    'DISCHARGE','GATE_OUT','DELIVERED','UNLOAD'
                ]);
            });

            if ($finishedEvent) $finished[$container] = $records;
            else $inProcess[$container] = $records;
        }

        return view('dashboard.cma', compact('inProcess','finished'));
    }

    public function indexapi()

    {
        $events = DB::table('cma_logs_events')
        ->select(
            'equipment_reference',
            'carrier_booking_reference',
            'event_created_at AS ts',
            DB::raw("'event' AS type"),
            'event_type',
            'transport_event_type_code',
            'equipment_event_type_code',
            DB::raw("NULL AS lat"),
            DB::raw("NULL AS longitude")
        );
    
        $coords = DB::table('cma_logs_coordinate')
            ->select(
                'equipmentReference AS equipment_reference',
                'carrierBookingReference AS carrier_booking_reference',
                'hora AS ts',
                DB::raw("'coord' AS type"),
                DB::raw("NULL AS event_type"),
                DB::raw("NULL AS transport_event_type_code"),
                DB::raw("NULL AS equipment_event_type_code"),
                'lat',
                'longitude'
            );
    
        $union = $events->unionAll($coords)
            ->orderBy('equipment_reference')
            ->orderBy('ts')
            ->get()
            ->groupBy('equipment_reference');
    

        // Determinar si la carga terminó
        $inProcess = collect();
        $finished  = collect();

        foreach ($union as $container => $records) {
            $finishedEvent = $records->first(function($r){
                return $r->type === 'event' && in_array($r->event_type, [
                    'DISCHARGE','GATE_OUT','DELIVERED','UNLOAD'
                ]);
            });

            if ($finishedEvent) $finished[$container] = $records;
            else $inProcess[$container] = $records;
        }

        return response()->json([
            'inProcess' => $inProcess,
            'finished' => $finished
        ]);
    }
}
