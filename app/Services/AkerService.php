<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AkerService
{
    public function getPosition(string $domain): ?array
    {
        // Cachea 60s para no golpear AKER en cada loop
        return Cache::remember("aker:pos:$domain", 60, function () use ($domain) {
            $payload = [
                "patentes" => [$domain],
                "cercania" => true,
                "domicilio" => false,
                "apiCode"  => config('services.aker.code'),
                "phone"    => config('services.aker.phone'),
            ];
            Log::info("Consultando AKER para dominio $domain", $payload);
            $res = Http::acceptJson()->post(env('AKER_API_URL'), $payload);
            if (!$res->successful()) return null;

            $data = $res->json("data.$domain");
            if (!$data || empty($data['ult_latitud']) || empty($data['ult_longitud'])) return null;

            return [
                'lat'  => (float)$data['ult_latitud'],
                'lng'  => (float)$data['ult_longitud'],
                'vel'  => $data['ult_velocidad'] ?? null,
                'time' => $data['ult_reporte'] ?? null,
            ];
        });
    }
}
