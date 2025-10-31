<?php

namespace App\Services;

use App\Models\GeoActionLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GeoActionService
{
    public function logEnter(array $ctx): GeoActionLog
    {
        Log::info('Geo ENTER', $ctx);
        return GeoActionLog::create($this->pack($ctx, 'ENTER'));
    }

    public function logExit(array $ctx): GeoActionLog
    {
        Log::info('Geo EXIT', $ctx);
        return GeoActionLog::create($this->pack($ctx, 'EXIT'));
    }

    public function markPoiState(int $tripId, int $poiId, int $state): void
    {
        DB::table('cntr_interest_point')
            ->where('cntr_id_cntr', $tripId)
            ->where('interest_point_id', $poiId)
            ->update(['activo' => $state, 'updated_at' => now()]);
    }

    private function pack(array $ctx, string $action): array
    {
        return [
            'trip_id'          => $ctx['trip_id'],
            'cntr_number'      => $ctx['cntr_number'],
            'domain'           => $ctx['domain'],
            'action_type'      => $action,
            'point_type'       => $ctx['point_type'] ?? 'POI',
            'distance_m'       => $ctx['distance_m'] ?? null,
            'threshold_m'      => $ctx['threshold_m'] ?? null,
            'event_lat'        => $ctx['event_lat'] ?? null,
            'event_lng'        => $ctx['event_lng'] ?? null,
            'position_lat'     => $ctx['position_lat'] ?? null,
            'position_lng'     => $ctx['position_lng'] ?? null,
            'status_at_moment' => $ctx['status_at_moment'] ?? null,
            'aker_time'        => $ctx['aker_time'] ?? null,
            'meta'             => $ctx['meta'] ?? null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
    }
}