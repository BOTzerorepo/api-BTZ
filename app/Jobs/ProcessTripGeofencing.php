<?php

namespace App\Jobs;

use App\Repositories\TripRepository;
use App\Services\AkerService;
use App\Services\GeoFenceService;
use App\Services\GeoActionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessTripGeofencing implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $tripId) {}

    public function handle(
        TripRepository $repo,
        AkerService $gps,
        GeoFenceService $geo,
        GeoActionService $actions
    ): void {
        $group = $repo->activeTripsWithPois()->get($this->tripId);
        if (!$group || $group->isEmpty()) return;

        $domain = $group->first()->domain;
        $cntr   = $group->first()->cntr_number;
        $status = $group->first()->main_status;

        $pos = $gps->getPosition($domain);
        if (!$pos) return;

        foreach ($group as $poi) {
            // lock de 120s por ENTER/EXIT del mismo POI
            $lockKey = "lock:geo:{$this->tripId}:{$poi->poi_id}";
            if (!Cache::lock($lockKey, 120)->get()) {
                continue; // otro worker evaluando ese POI
            }

            try {
                $inside = $geo->inside($pos['lat'], $pos['lng'], (float)$poi->lat, (float)$poi->lng, (float)$poi->radius_in);
                $was = (int)$poi->poi_state; // 0=pendiente,1=inside,2=done/out

                // ENTER
                if ($inside && $was !== 1) {
                    $actions->logEnter([
                        'trip_id'      => $this->tripId,
                        'cntr_number'  => $cntr,
                        'domain'       => $domain,
                        'distance_m'   => $geo->distance($pos['lat'], $pos['lng'], (float)$poi->lat, (float)$poi->lng),
                        'threshold_m'  => (int)$poi->radius_in,
                        'event_lat'    => (float)$poi->lat,
                        'event_lng'    => (float)$poi->lng,
                        'position_lat' => $pos['lat'],
                        'position_lng' => $pos['lng'],
                        'meta'         => ['poi_id'=>$poi->poi_id, 'poi_desc'=>$poi->poi_desc, 'order'=>$poi->poi_order, 'kind'=>$poi->poi_type],
                    ]);
                    $actions->markPoiState($this->tripId, $poi->poi_id, 1);

                    // dispara mail/webhook asíncrono
                    dispatch(new \App\Jobs\SendGeoEventNotifications(
                        tripId: $this->tripId,
                        cntrNumber: $cntr,
                        domain: $domain,
                        poiId: $poi->poi_id,
                        action: 'ENTER'
                    ))->onQueue('notifications');
                }

                // EXIT (histéresis simple: > radius_in * 1.5)
                $out = $geo->distance($pos['lat'], $pos['lng'], (float)$poi->lat, (float)$poi->lng) > max($poi->radius_in * 1.5, $poi->radius_in + 50);
                if ($out && $was === 1) {
                    $actions->logExit([
                        'trip_id'      => $this->tripId,
                        'cntr_number'  => $cntr,
                        'domain'       => $domain,
                        'distance_m'   => $geo->distance($pos['lat'], $pos['lng'], (float)$poi->lat, (float)$poi->lng),
                        'threshold_m'  => (int)($poi->radius_in * 1.5),
                        'event_lat'    => (float)$poi->lat,
                        'event_lng'    => (float)$poi->lng,
                        'position_lat' => $pos['lat'],
                        'position_lng' => $pos['lng'],
                        'meta'         => ['poi_id'=>$poi->poi_id, 'poi_desc'=>$poi->poi_desc, 'order'=>$poi->poi_order, 'kind'=>$poi->poi_type],
                    ]);
                    $actions->markPoiState($this->tripId, $poi->poi_id, 2);

                    dispatch(new \App\Jobs\SendGeoEventNotifications(
                        tripId: $this->tripId,
                        cntrNumber: $cntr,
                        domain: $domain,
                        poiId: $poi->poi_id,
                        action: 'EXIT'
                    ))->onQueue('notifications');
                }
            } finally {
                // Liberar lock
                Cache::lock($lockKey)->release();
            }
        }
    }
}
