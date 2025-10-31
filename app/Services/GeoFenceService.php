<?php

namespace App\Services;

class GeoFenceService
{
    public function distance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLng/2)**2;
        return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
    }

    public function inside(float $uLat, float $uLng, float $pLat, float $pLng, float $radius): bool
    {
        return $this->distance($uLat, $uLng, $pLat, $pLng) <= $radius;
    }
}
