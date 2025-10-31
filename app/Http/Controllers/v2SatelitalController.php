<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessGeofencingJob;
use App\Models\GeoActionLog;
use App\Models\position;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class v2SatelitalController extends Controller
{
    public function serviceSatelital()
{
    set_time_limit(120);
    Log::info('serviceSatelital: START');

    $AKerApiUrl  = config('services.aker.url', env('AKER_API_URL'));
    $AKerApiCode = env('AKER_API_CODE', 'E6HW19');
    $AKerPhone   = env('AKER_PHONE', '2612128105');

    // Umbrales de geofencing (metros)
    $TH_IN  = 200;
    $TH_OUT = 400; // histéresis para evitar trigger repetido

    $http = new Client();

    // 1) Obtener viajes activos con camión satelital habilitado
    $viajes = DB::table('trucks')
        ->join('asign', 'trucks.domain', '=', 'asign.truck')
        ->join('cntr', function($j){
            $j->on('cntr.cntr_number','=','asign.cntr_number')
              ->where('cntr.main_status','!=','TERMINADA');
        })
        ->join('carga','carga.booking','=','cntr.booking')
        ->leftJoin('aduanas as a_impo','a_impo.description','=','carga.custom_place_impo')
        ->leftJoin('aduanas as a_exp','a_exp.description','=','carga.custom_place')
        ->join('customer_load_places as L','L.description','=','carga.load_place')
        ->join('customer_unload_places as U','U.description','=','carga.unload_place')
        ->where('trucks.alta_aker',1)
        ->get([
            'cntr.id_cntr AS trip_id',
            'cntr.cntr_number',
            'trucks.domain',

            'L.latitud AS carga_lat', 'L.longitud AS carga_lng',
            DB::raw('COALESCE(a_impo.lat,a_exp.lat) AS aduana_lat'),
            DB::raw('COALESCE(a_impo.lon,a_exp.lon) AS aduana_lng'),
            'U.latitud AS descarga_lat', 'U.longitud AS descarga_lng',
        ]);

    if ($viajes->isEmpty()) {
        Log::info('serviceSatelital: no hay viajes activos');
        return;
    }

    foreach($viajes as $v) {

        

        // 2) Obtener coordenada actual desde AKER
        $payload = [
            'patentes' => [$v->domain],
            'cercania' => true,
            'domicilio'=> false,
            'apiCode'  => $AKerApiCode,
            'phone'    => $AKerPhone
        ];

        try {
            $res = $http->post($AKerApiUrl, ['json'=>$payload]);
            $body = json_decode($res->getBody(), true);

            if (!isset($body['data'][$v->domain]['ult_latitud'])) continue;

            $lat = $this->toFloat($body['data'][$v->domain]['ult_latitud']);
            $lng = $this->toFloat($body['data'][$v->domain]['ult_longitud']);

            if (!$lat || !$lng) continue;

        } catch (\Throwable $e) {
            Log::warning("No coords for {$v->domain}: ".$e->getMessage());
            continue;
        }

        // Guardar posición
        position::create([
            'dominio'=>$v->domain,
            'lat'=>$lat,
            'lng'=>$lng,
            'asigned'=>1
        ]);

        // Calcular distancias
        $dCarga    = $this->distIfCoords($lat,$lng,$v->carga_lat,$v->carga_lng);
        $dAduana   = $this->distIfCoords($lat,$lng,$v->aduana_lat,$v->aduana_lng);
        $dDescarga = $this->distIfCoords($lat,$lng,$v->descarga_lat,$v->descarga_lng);

        // Obtener último estado operativo
        $lastStatus = DB::table('status')
            ->where('cntr_number',$v->cntr_number)
            ->orderByDesc('id')
            ->value('main_status');

        // 3) Evaluar puntos
        $this->checkPoint($v,'CARGA',$dCarga,$TH_IN,$TH_OUT,$lat,$lng,$lastStatus);
        $this->checkPoint($v,'ADUANA',$dAduana,$TH_IN,$TH_OUT,$lat,$lng,$lastStatus);
        $this->checkPoint($v,'DESCARGA',$dDescarga,$TH_IN,$TH_OUT,$lat,$lng,$lastStatus);
    }

    Log::info('serviceSatelital: END');
}
private function checkPoint($v,$tipo,$dist,$TH_IN,$TH_OUT,$lat,$lng,$status)
{
    if ($dist === null) return;

    $last = GeoActionLog::where('trip_id',$v->trip_id)
            ->where('point_type',$tipo)
            ->orderByDesc('id')->first();

    $entering = ($dist <= $TH_IN) && (!$last || $last->action_type !== 'ENTER');
    $exiting  = ($dist >  $TH_OUT) && ($last && $last->action_type === 'ENTER');

    if ($entering) {
        Log::info("ENTER {$tipo} - {$v->cntr_number}");
        $this->logGeoAction([
            'trip_id'=>$v->trip_id,'cntr_number'=>$v->cntr_number,'domain'=>$v->domain,
            'action_type'=>'ENTER','point_type'=>$tipo,
            'distance_m'=>$dist,'threshold_m'=>$TH_IN,
            'event_lat'=>null,'event_lng'=>null,
            'position_lat'=>$lat,'position_lng'=>$lng,
            'status_at_moment'=>$status
        ]);
        if($tipo === 'DESCARGA') {
            $this->fireEndpoint(new Client(), env('APP_URL')."/api/accionLugarDescarga/{$v->trip_id}", "accionLugarDescarga", $v->trip_id);
            return;
        }
        //$this->fireEndpoint(new Client(), env('APP_URL')."/api/accionLugarDe{$tipo}/{$v->trip_id}", "accionLugarDe{$tipo}", $v->trip_id);
    }

    if ($exiting) {
        Log::info("EXIT {$tipo} - {$v->cntr_number}");
        $this->logGeoAction([
            'trip_id'=>$v->trip_id,'cntr_number'=>$v->cntr_number,'domain'=>$v->domain,
            'action_type'=>'EXIT','point_type'=>$tipo,
            'distance_m'=>$dist,'threshold_m'=>$TH_OUT,
            'event_lat'=>null,'event_lng'=>null,
            'position_lat'=>$lat,'position_lng'=>$lng,
            'status_at_moment'=>$status
        ]);
        $this->fireEndpoint(new Client(), env('APP_URL')."/api/accionFueraLugarDe{$tipo}/{$v->trip_id}", "accionFueraLugarDe{$tipo}", $v->trip_id);
    }
}
/**
 * Convierte coordenadas string a float seguro
 */
private function toFloat($value)
{
    if (!$value) return null;
    return floatval(str_replace(',', '.', $value));
}

/**
 * Calcula distancia en metros entre dos coordenadas (o null si falta dato)
 */
private function distIfCoords($lat1, $lng1, $lat2, $lng2)
{
    if (!$lat1 || !$lng1 || !$lat2 || !$lng2) return null;

    $R = 6371000; // radio de la Tierra en metros
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLng/2)**2;
    return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
}
public function runGeofencing()
{
    ProcessGeofencingJob::dispatch()->onQueue('geofencing');

    return response()->json([
        'status' => 'OK',
        'message' => 'Geofencing ejecutándose en 2° plano'
    ]);
}

/**
 * Guarda log del evento ENTER/EXIT de geocerca
 */
private function logGeoAction(array $data)
{
    GeoActionLog::create([
        'trip_id'          => $data['trip_id'],
        'cntr_number'      => $data['cntr_number'],
        'domain'           => $data['domain'],
        'action_type'      => $data['action_type'],   // ENTER / EXIT
        'point_type'       => $data['point_type'],    // CARGA / ADUANA / DESCARGA / POI
        'distance_m'       => $data['distance_m'],
        'threshold_m'      => $data['threshold_m'],
        'event_lat'        => $data['event_lat'],
        'event_lng'        => $data['event_lng'],
        'position_lat'     => $data['position_lat'],
        'position_lng'     => $data['position_lng'],
        'status_at_moment' => $data['status_at_moment']
    ]);
}

/**
 * Ejecuta endpoint interno cuando ocurre evento (ENTER/EXIT)
 * Se hace async vía HTTP para no bloquear proceso
 */
private function fireEndpoint($http, $url, $label, $tripId)
{
    try {
        $http->post($url);
        Log::info("📡 $label ejecutado para viaje $tripId");
    } catch (\Throwable $e) {
        Log::warning("⚠️ $label FALLÓ para viaje $tripId: ".$e->getMessage());
    }
}



}
