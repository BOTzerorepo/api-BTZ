<?php

// app/Http/Controllers/TrackingController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    public function index()
    {
        $rows = DB::table('cma_logs_events')
            ->select(
                'equipment_reference',
                'carrier_booking_reference',
                DB::raw('MAX(event_created_at) as last_ts')
            )
            ->groupBy('equipment_reference', 'carrier_booking_reference')
            ->orderByDesc('last_ts')
            ->get();

        return view('dashboard.viajes', compact('rows'));
    }
    public function show($equipmentReference)
    {
        $data = DB::table('cma_logs_events')
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
            )
            ->where('equipment_reference', $equipmentReference);

        $data = $data->unionAll(
            DB::table('cma_logs_coordinate')
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
                )
                ->where('equipmentReference', $equipmentReference)
        )
            ->orderBy('ts', 'ASC')
            ->get();

        return view('dashboard.traking', compact('data', 'equipmentReference'));
    }
    public function indexapi()
    {
        $rows = DB::table('cma_logs_events')
            ->select(
                'equipment_reference',
                'carrier_booking_reference',
                DB::raw('MAX(event_created_at) as last_ts')
            )
            ->groupBy('equipment_reference', 'carrier_booking_reference')
            ->orderByDesc('last_ts')
            ->get();

        return $rows;
    }
    public function showapi($equipmentReference)
    {
        $data = DB::table('cma_logs_events')
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
            )
            ->where('equipment_reference', $equipmentReference);

        $data = $data->unionAll(
            DB::table('cma_logs_coordinate')
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
                )
                ->where('equipmentReference', $equipmentReference)
        )
            ->orderBy('ts', 'ASC')
            ->get();

        return  $data;
    }
}
