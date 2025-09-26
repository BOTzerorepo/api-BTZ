<?php

namespace App\Http\Controllers;


use App\Models\akerTruck;
use App\Models\asign;
use App\Models\Carga;
use App\Models\cntr;
use App\Models\itinerario;
use App\Models\logapi;
use App\Models\position;
use App\Models\pruebasModel;
use App\Models\statu;
use App\Models\Transport;
use App\Models\truck;
use App\Mail\PuntoInteresEntrada;
use App\Mail\PuntoInteresSalida;
use App\Models\GeoActionLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use LDAP\Result;
use Mockery\Undefined;


use function PHPUnit\Framework\returnSelf;

// wget -O /dev/null "https://rail.com.ar/api/servicioSatelital"
// tiempo */2 * * * *

class ServiceSatelital extends Controller
{
    public function reviewDomains()
    {

        $trucks = truck::all();
        foreach ($trucks as $truck) {

            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json'
            ];

            // TEST: E6HW19 - PRODUCCION: C2QC20


            $body = '{
                    "patentes":["' . $truck->domain . '"],
                    "cercania":true,
                    "domicilio":false,
                    "apiCode":"E6HW19",
                    "phone":"2612128105"
                    }';


            $request = new Psr7Request('GET', 'https://app.akercontrol.com/ws/v2/servicios', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $data = json_decode($respuesta, true);

            if (isset($data['data'])) {

                if (isset($data['data'][$truck->domain]['ult_reporte']) && $data['data'][$truck->domain]['ult_reporte'] != null) {

                    $datos = $data['data'];
                    $details = reset($datos);
                    // Hacer algo con el primer elemento aquí

                    $truck = truck::where('domain', $details['patente'])->first();

                    if ($truck) {
                        // Si se encuentra un camión con el dominio, actualizar el estado a 1
                        $truck->alta_aker = 1;
                        $truck->id_satelital = $details['id'];
                        $truck->save();
                    }
                } else {
                    $truck = truck::where('domain', $truck->domain)->first();
                    $truck->alta_aker = 0;
                    $truck->id_satelital = null;
                    $truck->save();
                }
            } else {

                $truck = truck::where('domain', $truck->domain)->first();
                $truck->alta_aker = 0;
                $truck->id_satelital = null;
                $truck->save();
            }
        }
    }
    public function issetDominio($domain)
    {

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json'
        ];

        // TEST: E6HW19 - PRODUCCION: C2QC20


        $body = '{
                    "patentes":["' . $domain . '"],
                    "cercania":true,
                    "domicilio":false,
                    "apiCode":"E6HW19",
                    "phone":"2612128105"
                    }';


        $request = new Psr7Request('GET', 'https://app.akercontrol.com/ws/v2/servicios', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $respuesta = $res->getBody();
        $data = json_decode($respuesta, true);

        if (isset($data['data'])) {

            if (isset($data['data'][$domain]['ult_reporte']) && $data['data'][$domain]['ult_reporte'] != null) {

                $datos = $data['data'];
                $details = reset($datos);
                // Hacer algo con el primer elemento aquí

                $truck = truck::where('domain', $details['patente'])->first();

                if ($truck) {
                    // Si se encuentra un camión con el dominio, actualizar el estado a 1
                    $truck->alta_aker = 1;
                    $truck->id_satelital = $details['id'];
                    $truck->save();
                    return $truck;
                } else {
                    // Si no se encuentra un camión con el dominio, devolver un mensaje de error
                    return 'No se encontró un camión con el dominio especificado';
                }
            } else {
                $truck = truck::where('domain', $domain)->first();
                $truck->alta_aker = 0;
                $truck->id_satelital = null;
                $truck->save();

                return $truck;
            }
        } else {

            $truck = truck::where('domain', $domain)->first();
            $truck->alta_aker = 0;
            $truck->id_satelital = null;
            $truck->save();

            return $truck;
        }
    }
    public function serviceSatelital()
    {
        set_time_limit(120);
        Log::info('Comenzo Satelital');

        // === Configurables ===
        $AKerApiUrl     = 'https://app.akercontrol.com/ws/v2/servicios';
        $AKerApiCode    = 'E6HW19';
        $AKerPhone      = '2612128105';

        // Umbrales en metros
        $THRESHOLD_CARGA_IN     = 200;
        $THRESHOLD_ADUANA_IN    = 200;
        $THRESHOLD_DESCARGA_IN  = 200;

        $THRESHOLD_CARGA_OUT    = 400; // fuera de rango carga
        $THRESHOLD_ADUANA_OUT   = 200; // fuera de rango aduana

        $STATUS_FOR_POINT = [
            'CARGA'    => ['YENDO A CARGAR', 'CARGANDO','ASIGNADA'],
            'ADUANA'   => ['YENDO A ADUANA', 'EN ADUANA'],
            'DESCARGA' => ['YENDO A DESCARGAR', 'DESCARGANDO', 'EN DESTINO'],
        ];
        $canTrigger = function (?string $desc, string $point) use ($STATUS_FOR_POINT): bool {
            return $desc !== null && in_array($desc, $STATUS_FOR_POINT[$point] ?? [], true);
        };

        $appUrl = rtrim(env('APP_URL'), '/');

        // === Cliente HTTP ===
        $http = new Client([
            'timeout'         => 7,
            'connect_timeout' => 3,
            'http_errors'     => false,
        ]);

        // === 1) Viajes activos con camión satelital ===
        $camiones = DB::table('trucks')
            ->join('asign', 'trucks.domain', '=', 'asign.truck')
            ->join('cntr', function ($join) {
                $join->on('cntr.cntr_number', '=', 'asign.cntr_number')
                    ->where('cntr.main_status', '!=', 'TERMINADA');
            })
            ->join('carga', 'carga.booking', '=', 'cntr.booking')
            ->leftJoin('aduanas', 'aduanas.description', '=', 'carga.custom_place')
            ->join('customer_load_places', 'customer_load_places.description', '=', 'carga.load_place')
            ->join('customer_unload_places', 'customer_unload_places.description', '=', 'carga.unload_place')
            ->whereNull('carga.deleted_at')
            ->where('trucks.alta_aker', '!=', 0)
            ->get([
                'cntr.id_cntr as IdTrip',
                'carga.id as idCarga',
                'trucks.id',
                'trucks.id_satelital',
                'trucks.domain',

                'customer_load_places.description as LugarCarga',
                'customer_load_places.latitud as CargaLat',
                'customer_load_places.longitud as CargaLng',

                'aduanas.description as LugarAduana',
                'aduanas.lat as aduanaLat',
                'aduanas.lon as aduanaLon',

                'customer_unload_places.description as lugarDescarga',
                'customer_unload_places.latitud as descargaLat',
                'customer_unload_places.longitud as descargaLon',
            ])
            ->unique(fn($r) => $r->IdTrip . '|' . $r->domain)
            ->values();


        if ($camiones->isEmpty()) {
            Log::info('serviceSatelital: No hay camiones activos con alta_aker != 0.');
            return;
        }

        foreach ($camiones as $camion) {
            Log::info('ingresó a la llamada de aker: ' . $camion->domain);

            // === 2) Llamada a AKER ===
            $payload = [
                'patentes' => [$camion->domain],
                'cercania' => true,
                'domicilio' => false,
                'apiCode'  => $AKerApiCode,
                'phone'    => $AKerPhone,
            ];


            try {
                $res = $http->post($AKerApiUrl, ['json' => $payload, 'headers' => ['Accept' => 'application/json']]);

                $statusCode = $res->getStatusCode();
                if ($statusCode < 200 || $statusCode >= 300) {
                    Log::warning("serviceSatelital: HTTP {$statusCode} consultando Aker para {$camion->domain}");
                    continue;
                }

                $r = json_decode((string)$res->getBody(), true);
                if (!is_array($r) || !array_key_exists('data', $r) || !array_key_exists($camion->domain, $r['data'])) {
                    Log::warning("serviceSatelital: respuesta inválida para {$camion->domain}");
                    continue;
                }

                $datos = $r['data'][$camion->domain] ?? null;
                if (!is_array($datos)) {
                    Log::warning("serviceSatelital: datos inválidos para {$camion->domain}");
                    continue;
                }

                $posicionLat = $this->toFloat($datos['ult_latitud'] ?? null);
                $posicionLon = $this->toFloat($datos['ult_longitud'] ?? null);

                if ($posicionLat === null || $posicionLon === null) {
                    Log::warning("serviceSatelital: coordenadas vacías para {$camion->domain}");
                    continue;
                }

                // === 3) Persistir posición ===
                try {
                    Log::info('Guardo Posicion ');
                    $positionDB = new Position();
                    $positionDB->dominio = $camion->domain;
                    $positionDB->lat     = $posicionLat;
                    $positionDB->lng     = $posicionLon;
                    $positionDB->asigned = 1;
                    $positionDB->save();
                } catch (\Throwable $e) {
                    Log::error("serviceSatelital: error guardando Position para {$camion->domain}: " . $e->getMessage());
                }

                $IdTrip = $camion->IdTrip;
                Log::info('Camion llevando carga coon ID: ' . $IdTrip);



                // === 4) Calcular distancias ===
                $distCarga    = $this->distIfCoords($posicionLat, $posicionLon, $this->toFloat($camion->CargaLat),    $this->toFloat($camion->CargaLng));
                Log::info('Está a : ' . $distCarga . 'del Lugar de Carga');


                $distAduana   = $this->distIfCoords($posicionLat, $posicionLon, $this->toFloat($camion->aduanaLat),   $this->toFloat($camion->aduanaLon));
                Log::info('Está a : ' . $distAduana . 'del Lugar de Aduana');

                $distDescarga = $this->distIfCoords($posicionLat, $posicionLon, $this->toFloat($camion->descargaLat), $this->toFloat($camion->descargaLon));
                Log::info('Está a : ' . $distDescarga . 'del Lugar de Descarga');
                
                // === 5) Obtener cntr y último status (ANTES de usar $cntr/$description) ===
                $cntr = DB::table('cntr')
                    ->select('cntr_number', 'booking', 'confirmacion')
                    ->where('id_cntr', $IdTrip)
                    ->first();


                if (!$cntr) {
                    Log::warning("serviceSatelital: no se encontró cntr para IdTrip={$IdTrip}");
                    continue;
                }

                $lastStatus  = DB::table('status')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->orderByDesc('id')
                    ->first();

                Log::info('Ultimo estado del contenedor: ' . ($lastStatus->main_status ?? 'N/A'));

                $description = $lastStatus->main_status ?? null;


                // === 6) Acciones ENTER (dentro de rango) ===
                Log::info('Empezó a probar las distacias con los lugares');
                // helpers de estado por punto
                $isInsideCarga    = ($distCarga    !== null && $distCarga    <= $THRESHOLD_CARGA_IN);
                Log::info('Está dentro del umbral de carga? ' . ($isInsideCarga ? 'SI' : 'NO'));
                $isInsideAduana   = ($distAduana   !== null && $distAduana   <= $THRESHOLD_ADUANA_IN);
                Log::info('Está dentro del umbral de carga? ' . ($isInsideAduana ? 'SI' : 'NO'));
                $isInsideDescarga = ($distDescarga !== null && $distDescarga <= $THRESHOLD_DESCARGA_IN);
                Log::info('Está dentro del umbral de carga? ' . ($isInsideDescarga ? 'SI' : 'NO'));

                // último log por punto
                $lastCarga    = GeoActionLog::where('trip_id', $IdTrip)->where('point_type', 'CARGA')->orderByDesc('id')->first();
                Log::info('Último log de carga: ' . ($lastCarga->action_type ?? 'N/A'));
                $lastAduana   = GeoActionLog::where('trip_id', $IdTrip)->where('point_type', 'ADUANA')->orderByDesc('id')->first();
                Log::info('Último log de aduana: ' . ($lastAduana->action_type ?? 'N/A'));
                $lastDescarga = GeoActionLog::where('trip_id', $IdTrip)->where('point_type', 'DESCARGA')->orderByDesc('id')->first();
                Log::info('Último log de descarga: ' . ($lastDescarga->action_type ?? 'N/A'));

                // ======== CARGA ========
                if ($isInsideCarga && (($lastCarga->action_type ?? null) !== 'ENTER') && $canTrigger($description, 'CARGA')) {
                    // ENTER CARGA (solo al cruzar el umbral hacia adentro)
                    Log::info('Está entrando al lugar de carga');
                    $this->logGeoAction([
                        'trip_id' => $IdTrip,
                        'cntr_number' => $cntr->cntr_number,
                        'domain' => $camion->domain,
                        'action_type' => 'ENTER',
                        'point_type' => 'CARGA',
                        'distance_m' => $distCarga,
                        'threshold_m' => $THRESHOLD_CARGA_IN,
                        'event_lat' => $this->toFloat($camion->CargaLat),
                        'event_lng' => $this->toFloat($camion->CargaLng),
                        'position_lat' => $posicionLat,
                        'position_lng' => $posicionLon,
                        'status_at_moment' => $description,
                        'meta' => ['source' => 'aker'],
                    ]);
                    $this->fireEndpoint($http, "{$appUrl}/api/accionLugarDeCarga/{$IdTrip}", "accionLugarDeCarga", $IdTrip);
                }
                // EXIT CARGA: solo si veníamos de ENTER y ahora estamos fuera (usar umbral OUT para histéresis)
                if ((!$isInsideCarga && ($distCarga !== null && $distCarga > $THRESHOLD_CARGA_OUT))
                    && (($lastCarga->action_type ?? null) === 'ENTER')
                    && in_array($description, ['YENDO A CARGAR', 'CARGANDO'], true)
                ) {
                    Log::info('Está saliendo del lugar de carga');

                    $this->logGeoAction([
                        'trip_id' => $IdTrip,
                        'cntr_number' => $cntr->cntr_number,
                        'domain' => $camion->domain,
                        'action_type' => 'EXIT',
                        'point_type' => 'CARGA',
                        'distance_m' => $distCarga,
                        'threshold_m' => $THRESHOLD_CARGA_OUT,
                        'event_lat' => $this->toFloat($camion->CargaLat),
                        'event_lng' => $this->toFloat($camion->CargaLng),
                        'position_lat' => $posicionLat,
                        'position_lng' => $posicionLon,
                        'status_at_moment' => $description,
                        'meta' => ['source' => 'aker'],
                    ]);
                    $this->fireEndpoint($http, "{$appUrl}/api/accionFueraLugarDeCarga/{$IdTrip}", "accionFueraLugarDeCarga", $IdTrip);
                }

                // ======== ADUANA ========
                if ($isInsideAduana && (($lastAduana->action_type ?? null) !== 'ENTER') && $canTrigger($description, 'ADUANA')) {
                    Log::info('Está entrando al lugar de aduana');
                    $this->logGeoAction([
                        'trip_id' => $IdTrip,
                        'cntr_number' => $cntr->cntr_number,
                        'domain' => $camion->domain,
                        'action_type' => 'ENTER',
                        'point_type' => 'ADUANA',
                        'distance_m' => $distAduana,
                        'threshold_m' => $THRESHOLD_ADUANA_IN,
                        'event_lat' => $this->toFloat($camion->aduanaLat),
                        'event_lng' => $this->toFloat($camion->aduanaLon),
                        'position_lat' => $posicionLat,
                        'position_lng' => $posicionLon,
                        'status_at_moment' => $description,
                        'meta' => ['source' => 'aker'],
                    ]);
                    $this->fireEndpoint($http, "{$appUrl}/api/accionLugarAduana/{$IdTrip}", "accionLugarAduana", $IdTrip);
                }
                if ((!$isInsideAduana && ($distAduana !== null && $distAduana > $THRESHOLD_ADUANA_OUT))
                    && (($lastAduana->action_type ?? null) === 'ENTER')
                    && $description === 'EN ADUANA'
                ) {

                    Log::info('Está saliendo del lugar de aduana');
                    $this->logGeoAction([
                        'trip_id' => $IdTrip,
                        'cntr_number' => $cntr->cntr_number,
                        'domain' => $camion->domain,
                        'action_type' => 'EXIT',
                        'point_type' => 'ADUANA',
                        'distance_m' => $distAduana,
                        'threshold_m' => $THRESHOLD_ADUANA_OUT,
                        'event_lat' => $this->toFloat($camion->aduanaLat),
                        'event_lng' => $this->toFloat($camion->aduanaLon),
                        'position_lat' => $posicionLat,
                        'position_lng' => $posicionLon,
                        'status_at_moment' => $description,
                        'meta' => ['source' => 'aker'],
                    ]);
                    $this->fireEndpoint($http, "{$appUrl}/api/accionFueraLugarAduana/{$IdTrip}", "accionFueraLugarAduana", $IdTrip);
                }




                // ======== DESCARGA ========
                if ($isInsideDescarga && (($lastDescarga->action_type ?? null) !== 'ENTER') && $canTrigger($description, 'DESCARGA')) {
                    Log::info('Está entrando al lugar de descarga');
                    $this->logGeoAction([
                        'trip_id' => $IdTrip,
                        'cntr_number' => $cntr->cntr_number,
                        'domain' => $camion->domain,
                        'action_type' => 'ENTER',
                        'point_type' => 'DESCARGA',
                        'distance_m' => $distDescarga,
                        'threshold_m' => $THRESHOLD_DESCARGA_IN,
                        'event_lat' => $this->toFloat($camion->descargaLat),
                        'event_lng' => $this->toFloat($camion->descargaLon),
                        'position_lat' => $posicionLat,
                        'position_lng' => $posicionLon,
                        'status_at_moment' => $description,
                        'meta' => ['source' => 'aker'],
                    ]);
                    $this->fireEndpoint($http, "{$appUrl}/api/accionLugarDescarga/{$IdTrip}", "accionLugarDescarga", $IdTrip);
                }
                if ((!$isInsideDescarga && ($distDescarga !== null && $distDescarga > $THRESHOLD_DESCARGA_IN))
                    && (($lastDescarga->action_type ?? null) === 'ENTER')
                ) {
                    Log::info('Está saliendo del lugar de descarga');
                    // si querés manejar EXIT Descarga, agregalo similar a los otros
                }
            } catch (GuzzleException $e) {
                Log::error("serviceSatelital: error HTTP para {$camion->domain}: " . $e->getMessage());
                continue;
            } catch (\Throwable $e) {
                Log::error("serviceSatelital: error inesperado para {$camion->domain}: " . $e->getMessage());
                continue;
            }

            // usleep(120000); // opcional anti ráfaga
        }
    }
    private function toFloat($value): ?float
    {
        if ($value === null || $value === '') return null;
        if (is_string($value)) {
            $value = str_replace([' ', ','], ['', '.'], $value);
        }
        return is_numeric($value) ? (float)$value : null;
    }

    /**
     * Distancia en metros si ambas coordenadas existen; null si falta alguna.
     */
    private function distIfCoords(?float $lat1, ?float $lon1, ?float $lat2, ?float $lon2): ?float
    {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) return null;
        return $this->calcularDistancia($lat1, $lon1, $lat2, $lon2);
    }

    /**
     * Haversine (metros). Si ya tenés calcularDistancia, podés borrar este método.
     */

    public function flota()
    {

        $curl = curl_init();

        // TEST: E6HW19 - PRODUCCION: C2QC20
        if (env('APP_ENV') === 'production') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.akercontrol.com/ws/flota/2612128105/E6HW19',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
        } else {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.akercontrol.com/ws/flota/2612128105/E6HW19',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
        }

        $response = curl_exec($curl);
        $json = json_decode($response);
        $datos = $json->data;

        $camiones = [];

        foreach ($datos as $dato) {

            if (!empty($dato->patente)) { // Verificar si 'patente' no es nulo
                /*   $todosMisCamiones = DB::table('trucks')
                    ->join('transports', 'trucks.transport_id', '=', 'transports.id')
                    ->where('trucks.domain', '=', $dato->patente)
                    ->get(); */

                $trucks = DB::table('trucks')
                    ->leftJoin('asign', function ($join) {
                        $join->on('trucks.domain', '=', 'asign.truck');
                    })
                    ->leftJoin('transports', 'trucks.transport_id', '=', 'transports.id')
                    ->leftJoin('drivers', 'asign.driver', '=', 'drivers.nombre')
                    ->leftJoin('cntr', 'asign.cntr_number', '=', 'cntr.cntr_number')
                    ->leftJoin('carga', 'cntr.booking', '=', 'carga.booking')
                    ->leftJoin('customer_load_places', 'carga.load_place', '=', 'customer_load_places.description')
                    ->leftJoin('customer_unload_places', 'carga.unload_place', '=', 'customer_unload_places.description')
                    ->leftJoin('aduanas', 'carga.custom_place', '=', 'aduanas.description')
                    ->select(
                        'cntr.cntr_number as contenedor',
                        'cntr.cntr_type as tipoContenedor',
                        'cntr.retiro_place',
                        'cntr.main_status',
                        'cntr.status_cntr',
                        'carga.id as cargaId',
                        'carga.booking',
                        'carga.commodity',
                        'carga.load_place',
                        'customer_load_places.latitud as LoadPlaceLat',
                        'customer_load_places.longitud as LoadPlaceLng',
                        'carga.load_date',
                        'carga.unload_place',
                        'customer_unload_places.latitud as UnloadPlaceLat',
                        'customer_unload_places.longitud as UnloadPlaceLng',
                        'carga.custom_place',
                        'aduanas.lat as aduanaLat',
                        'aduanas.lon as aduanaLng',
                        'carga.ref_customer',
                        'carga.type as cargaType',
                        'carga.cut_off_fis as unload_date',
                        'asign.driver',
                        'drivers.documento',
                        'drivers.vto_carnet',
                        'drivers.WhatsApp',
                        'asign.agent_port',
                        'trucks.*',
                        'asign.truck_semi',
                        'transports.*'
                    )
                    ->where('trucks.domain', '=', $dato->patente)
                    ->whereNotIn('cntr.main_status', ['TERMINADA', 'NO ASIGNED'])
                    ->get();


                if ($trucks->isNotEmpty()) { // Verificar si se encontraron camiones
                    $camion = $trucks->first();

                    $truck['model'] = $camion->model;
                    $truck['domain'] = $camion->domain;
                    $truck['year'] = $camion->year;
                    $truck['vto_poliza'] = $camion->vto_poliza;
                    $truck['razon_social'] = $camion->razon_social;
                    $truck['logo'] = $camion->logo;
                    $truck['vto_permiso'] = $camion->vto_permiso;
                    $truck['titulo'] = $dato->nombre;
                    $truck['ult_latitud'] = $dato->ult_latitud;
                    $truck['ult_longitud'] = $dato->ult_longitud;
                    $truck['ult_velocidad'] = $dato->ult_velocidad;
                    $truck['ult_fecha'] = $dato->ult_fecha;
                    $truck['ult_reporte'] = $dato->ult_reporte;
                    $truck['ult_direccion'] = $dato->ult_direccion;
                    $truck['direccion'] = $dato->ult_direccion;

                    // Detalles del contenedor
                    $truck['cntr'] = array(
                        'contenedor' => $camion->contenedor,
                        'type' => $camion->tipoContenedor,
                        'main_status' => $camion->main_status,
                        'status_detail' => $camion->status_cntr,
                    );

                    // Detalles generales
                    $truck['general'] = array(
                        'booking' => $camion->booking,
                        'type' => $camion->cargaType,
                        'retiro_place' => $camion->retiro_place,
                        'commodity' => $camion->commodity,
                        'ref_customer' => $camion->ref_customer,
                        'agent_port' => $camion->agent_port,
                        'id_carga' => $camion->cargaId,
                        'url_carga' => env('FRONT_URL') . '/includes/view_carga_user.php?id=' . $camion->cargaId,

                    );

                    // Detalles de origen
                    $truck['origen'] = array(
                        'description' => $camion->load_place,
                        'lat' => $camion->LoadPlaceLat,
                        'lng' => $camion->LoadPlaceLng,
                        'load_date' => $camion->load_date,
                    );

                    // Detalles de destino
                    $truck['destino'] = array(
                        'description' => $camion->unload_place,
                        'lat' => $camion->UnloadPlaceLat,
                        'lng' => $camion->UnloadPlaceLng,
                        'load_date' => $camion->unload_date,
                    );

                    // Detalles de aduana
                    $truck['aduana'] = array(
                        'description' => $camion->custom_place,
                        'lat' => $camion->aduanaLat,
                        'lng' => $camion->aduanaLng,
                        'load_date' => $camion->load_date,
                    );

                    // Detalles del conductor
                    $truck['driver'] = array(
                        'nombre' => $camion->driver,
                        'documento' => $camion->documento,
                        'carnet' => $camion->vto_carnet,
                        'whatsapp' => $camion->WhatsApp,
                    );

                    array_push($camiones, $truck);
                }
            }
        }
        return $camiones;
    }
    public function flotaTransport($id)
    {

        $transport = Transport::find($id);
        $curl = curl_init();

        // TEST: E6HW19 - PRODUCCION: C2QC20
        if (env('APP_ENV') === 'production') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.akercontrol.com/ws/flota/2612128105/E6HW19',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
        } else {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.akercontrol.com/ws/flota/2612128105/E6HW19',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
        }

        $response = curl_exec($curl);
        $json = json_decode($response);
        $datos = $json->data;

        $camiones = [];

        foreach ($datos as $dato) {

            if (!empty($dato->patente)) { // Verificar si 'patente' no es nulo
                /*   $todosMisCamiones = DB::table('trucks')
                    ->join('transports', 'trucks.transport_id', '=', 'transports.id')
                    ->where('trucks.domain', '=', $dato->patente)
                    ->get(); */

                $trucks = DB::table('trucks')
                    ->leftJoin('asign', function ($join) {
                        $join->on('trucks.domain', '=', 'asign.truck');
                    })
                    ->leftJoin('transports', 'trucks.transport_id', '=', 'transports.id')
                    ->leftJoin('drivers', 'asign.driver', '=', 'drivers.nombre')
                    ->leftJoin('cntr', 'asign.cntr_number', '=', 'cntr.cntr_number')
                    ->leftJoin('carga', 'cntr.booking', '=', 'carga.booking')
                    ->leftJoin('customer_load_places', 'carga.load_place', '=', 'customer_load_places.description')
                    ->leftJoin('customer_unload_places', 'carga.unload_place', '=', 'customer_unload_places.description')
                    ->leftJoin('aduanas', 'carga.custom_place', '=', 'aduanas.description')
                    ->select(
                        'cntr.cntr_number as contenedor',
                        'cntr.cntr_type as tipoContenedor',
                        'cntr.retiro_place',
                        'cntr.main_status',
                        'cntr.status_cntr',
                        'carga.id as cargaId',
                        'carga.booking',
                        'carga.commodity',
                        'carga.load_place',
                        'customer_load_places.latitud as LoadPlaceLat',
                        'customer_load_places.longitud as LoadPlaceLng',
                        'carga.load_date',
                        'carga.unload_place',
                        'customer_unload_places.latitud as UnloadPlaceLat',
                        'customer_unload_places.longitud as UnloadPlaceLng',
                        'carga.custom_place',
                        'aduanas.lat as aduanaLat',
                        'aduanas.lon as aduanaLng',
                        'carga.ref_customer',
                        'carga.type as cargaType',
                        'carga.cut_off_fis as unload_date',
                        'asign.driver',
                        'drivers.documento',
                        'drivers.vto_carnet',
                        'drivers.WhatsApp',
                        'asign.agent_port',
                        'trucks.*',
                        'asign.truck_semi',
                        'transports.*'
                    )
                    ->where('trucks.domain', '=', $dato->patente)
                    ->where('asign.transport', '=', $transport->razon_social)
                    ->whereNotIn('cntr.main_status', ['TERMINADA', 'NO ASIGNED'])
                    ->get();


                if ($trucks->isNotEmpty()) { // Verificar si se encontraron camiones
                    $camion = $trucks->first();

                    $truck['model'] = $camion->model;
                    $truck['domain'] = $camion->domain;
                    $truck['year'] = $camion->year;
                    $truck['vto_poliza'] = $camion->vto_poliza;
                    $truck['razon_social'] = $camion->razon_social;
                    $truck['logo'] = $camion->logo;
                    $truck['vto_permiso'] = $camion->vto_permiso;
                    $truck['titulo'] = $dato->nombre;
                    $truck['ult_latitud'] = $dato->ult_latitud;
                    $truck['ult_longitud'] = $dato->ult_longitud;
                    $truck['ult_velocidad'] = $dato->ult_velocidad;
                    $truck['ult_fecha'] = $dato->ult_fecha;
                    $truck['ult_reporte'] = $dato->ult_reporte;
                    $truck['ult_direccion'] = $dato->ult_direccion;
                    $truck['direccion'] = $dato->ult_direccion;

                    // Detalles del contenedor
                    $truck['cntr'] = array(
                        'contenedor' => $camion->contenedor,
                        'type' => $camion->tipoContenedor,
                        'main_status' => $camion->main_status,
                        'status_detail' => $camion->status_cntr,
                    );

                    // Detalles generales
                    $truck['general'] = array(
                        'booking' => $camion->booking,
                        'type' => $camion->cargaType,
                        'retiro_place' => $camion->retiro_place,
                        'commodity' => $camion->commodity,
                        'ref_customer' => $camion->ref_customer,
                        'agent_port' => $camion->agent_port,
                        'id_carga' => $camion->cargaId,
                        'url_carga' => env('FRONT_URL') . '/includes/view_carga_user.php?id=' . $camion->cargaId,

                    );

                    // Detalles de origen
                    $truck['origen'] = array(
                        'description' => $camion->load_place,
                        'lat' => $camion->LoadPlaceLat,
                        'lng' => $camion->LoadPlaceLng,
                        'load_date' => $camion->load_date,
                    );

                    // Detalles de destino
                    $truck['destino'] = array(
                        'description' => $camion->unload_place,
                        'lat' => $camion->UnloadPlaceLat,
                        'lng' => $camion->UnloadPlaceLng,
                        'load_date' => $camion->unload_date,
                    );

                    // Detalles de aduana
                    $truck['aduana'] = array(
                        'description' => $camion->custom_place,
                        'lat' => $camion->aduanaLat,
                        'lng' => $camion->aduanaLng,
                        'load_date' => $camion->load_date,
                    );

                    // Detalles del conductor
                    $truck['driver'] = array(
                        'nombre' => $camion->driver,
                        'documento' => $camion->documento,
                        'carnet' => $camion->vto_carnet,
                        'whatsapp' => $camion->WhatsApp,
                    );

                    array_push($camiones, $truck);
                }
            }
        }
        return $camiones;
    }
    public function flotaClient($id)
    {

        $transport = Transport::find($id);
        $curl = curl_init();

        // TEST: E6HW19 - PRODUCCION: C2QC20
        if (env('APP_ENV') === 'production') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.akercontrol.com/ws/flota/2612128105/E6HW19',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
        } else {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.akercontrol.com/ws/flota/2612128105/E6HW19',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
        }

        $response = curl_exec($curl);
        $json = json_decode($response);
        $datos = $json->data;

        $camiones = [];

        foreach ($datos as $dato) {

            if (!empty($dato->patente)) { // Verificar si 'patente' no es nulo
                /*   $todosMisCamiones = DB::table('trucks')
                    ->join('transports', 'trucks.transport_id', '=', 'transports.id')
                    ->where('trucks.domain', '=', $dato->patente)
                    ->get(); */

                $trucks = DB::table('trucks')
                    ->leftJoin('asign', function ($join) {
                        $join->on('trucks.domain', '=', 'asign.truck');
                    })
                    ->leftJoin('transports', 'trucks.transport_id', '=', 'transports.id')
                    ->leftJoin('drivers', 'asign.driver', '=', 'drivers.nombre')
                    ->leftJoin('cntr', 'asign.cntr_number', '=', 'cntr.cntr_number')
                    ->leftJoin('carga', 'cntr.booking', '=', 'carga.booking')
                    ->leftJoin('customer_load_places', 'carga.load_place', '=', 'customer_load_places.description')
                    ->leftJoin('customer_unload_places', 'carga.unload_place', '=', 'customer_unload_places.description')
                    ->leftJoin('aduanas', 'carga.custom_place', '=', 'aduanas.description')
                    ->select(
                        'cntr.cntr_number as contenedor',
                        'cntr.cntr_type as tipoContenedor',
                        'cntr.retiro_place',
                        'cntr.main_status',
                        'cntr.status_cntr',
                        'carga.id as cargaId',
                        'carga.booking',
                        'carga.commodity',
                        'carga.load_place',
                        'customer_load_places.latitud as LoadPlaceLat',
                        'customer_load_places.longitud as LoadPlaceLng',
                        'carga.load_date',
                        'carga.unload_place',
                        'customer_unload_places.latitud as UnloadPlaceLat',
                        'customer_unload_places.longitud as UnloadPlaceLng',
                        'carga.custom_place',
                        'aduanas.lat as aduanaLat',
                        'aduanas.lon as aduanaLng',
                        'carga.ref_customer',
                        'carga.type as cargaType',
                        'carga.cut_off_fis as unload_date',
                        'asign.driver',
                        'drivers.documento',
                        'drivers.vto_carnet',
                        'drivers.WhatsApp',
                        'asign.agent_port',
                        'trucks.*',
                        'asign.truck_semi',
                        'transports.*'
                    )
                    ->where('trucks.domain', '=', $dato->patente)
                    ->where('asign.transport', '=', $transport->razon_social)
                    ->whereNotIn('cntr.main_status', ['TERMINADA', 'NO ASIGNED'])
                    ->get();


                if ($trucks->isNotEmpty()) { // Verificar si se encontraron camiones
                    $camion = $trucks->first();

                    $truck['model'] = $camion->model;
                    $truck['domain'] = $camion->domain;
                    $truck['year'] = $camion->year;
                    $truck['vto_poliza'] = $camion->vto_poliza;
                    $truck['razon_social'] = $camion->razon_social;
                    $truck['logo'] = $camion->logo;
                    $truck['vto_permiso'] = $camion->vto_permiso;
                    $truck['titulo'] = $dato->nombre;
                    $truck['ult_latitud'] = $dato->ult_latitud;
                    $truck['ult_longitud'] = $dato->ult_longitud;
                    $truck['ult_velocidad'] = $dato->ult_velocidad;
                    $truck['ult_fecha'] = $dato->ult_fecha;
                    $truck['ult_reporte'] = $dato->ult_reporte;
                    $truck['ult_direccion'] = $dato->ult_direccion;
                    $truck['direccion'] = $dato->ult_direccion;

                    // Detalles del contenedor
                    $truck['cntr'] = array(
                        'contenedor' => $camion->contenedor,
                        'type' => $camion->tipoContenedor,
                        'main_status' => $camion->main_status,
                        'status_detail' => $camion->status_cntr,
                    );

                    // Detalles generales
                    $truck['general'] = array(
                        'booking' => $camion->booking,
                        'type' => $camion->cargaType,
                        'retiro_place' => $camion->retiro_place,
                        'commodity' => $camion->commodity,
                        'ref_customer' => $camion->ref_customer,
                        'agent_port' => $camion->agent_port,
                        'id_carga' => $camion->cargaId,
                        'url_carga' => env('FRONT_URL') . '/includes/view_carga_user.php?id=' . $camion->cargaId,

                    );

                    // Detalles de origen
                    $truck['origen'] = array(
                        'description' => $camion->load_place,
                        'lat' => $camion->LoadPlaceLat,
                        'lng' => $camion->LoadPlaceLng,
                        'load_date' => $camion->load_date,
                    );

                    // Detalles de destino
                    $truck['destino'] = array(
                        'description' => $camion->unload_place,
                        'lat' => $camion->UnloadPlaceLat,
                        'lng' => $camion->UnloadPlaceLng,
                        'load_date' => $camion->unload_date,
                    );

                    // Detalles de aduana
                    $truck['aduana'] = array(
                        'description' => $camion->custom_place,
                        'lat' => $camion->aduanaLat,
                        'lng' => $camion->aduanaLng,
                        'load_date' => $camion->load_date,
                    );

                    // Detalles del conductor
                    $truck['driver'] = array(
                        'nombre' => $camion->driver,
                        'documento' => $camion->documento,
                        'carnet' => $camion->vto_carnet,
                        'whatsapp' => $camion->WhatsApp,
                    );

                    array_push($camiones, $truck);
                }
            }
        }
        return $camiones;
    }

    public function flotaId($domain)
    {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json'
        ];

        // TEST: E6HW19 - PRODUCCION: C2QC20

        $camiones = [];
        $body = '{
                    "patentes":["' . $domain . '"],
                    "cercania":true,
                    "domicilio":false,
                    "apiCode":"E6HW19",
                    "phone":"2612128105"
                    }';


        $request = new Psr7Request('GET', 'https://app.akercontrol.com/ws/v2/servicios', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $respuesta = $res->getBody();
        $data = json_decode($respuesta, true);

        if (isset($data['data'])) {

            $dato = $data['data'][$domain];

            $unidad = DB::table('trucks')
                ->leftJoin('asign', function ($join) {
                    $join->on('trucks.domain', '=', 'asign.truck');
                })
                ->leftJoin('transports', 'trucks.transport_id', '=', 'transports.id')
                ->leftJoin('drivers', 'asign.driver', '=', 'drivers.nombre')
                ->leftJoin('cntr', 'asign.cntr_number', '=', 'cntr.cntr_number')
                ->leftJoin('carga', 'cntr.booking', '=', 'carga.booking')
                ->leftJoin('customer_load_places', 'carga.load_place', '=', 'customer_load_places.description')
                ->leftJoin('customer_unload_places', 'carga.unload_place', '=', 'customer_unload_places.description')
                ->leftJoin('aduanas', function ($j) {
                    $j->on('aduanas.description', '=', 'carga.custom_place')
                        ->orOn('aduanas.description', '=', 'carga.custom_place_impo');
                })
                ->select(
                    'cntr.cntr_number as contenedor',
                    'cntr.cntr_type as tipoContenedor',
                    'cntr.retiro_place',
                    'cntr.main_status',
                    'cntr.status_cntr',
                    'carga.id as cargaId',
                    'carga.booking',
                    'carga.commodity',
                    'carga.load_place',
                    'customer_load_places.latitud as LoadPlaceLat',
                    'customer_load_places.longitud as LoadPlaceLng',
                    'carga.load_date',
                    'carga.unload_place',
                    'customer_unload_places.latitud as UnloadPlaceLat',
                    'customer_unload_places.longitud as UnloadPlaceLng',
                    'carga.custom_place',
                    'aduanas.lat as aduanaLat',
                    'aduanas.lon as aduanaLng',
                    'carga.ref_customer',
                    'carga.type as cargaType',
                    'carga.cut_off_fis as unload_date',
                    'asign.driver',
                    'drivers.documento',
                    'drivers.vto_carnet',
                    'drivers.WhatsApp',
                    'asign.agent_port',
                    'trucks.*',
                    'asign.truck_semi',
                    'transports.*'
                )
                ->where('asign.truck', '=', $domain)
                ->whereNotIn('cntr.main_status', ['TERMINADA', 'NO ASIGNED'])
                ->whereNotNull('trucks.domain') // Aseguramos que la unión principal se mantenga
                ->orderBy('carga.created_at', 'desc')
                ->get();



            if ($unidad->isNotEmpty()) { // Verificar si se encontraron camiones

                $camion = $unidad[0];



                $truck['model'] = $camion->model;
                $truck['domain'] = $camion->domain;
                $truck['year'] = $camion->year;
                $truck['vto_poliza'] = $camion->vto_poliza;
                $truck['razon_social'] = $camion->razon_social;
                $truck['logo'] = $camion->logo;
                $truck['vto_permiso'] = $camion->vto_permiso;
                $truck['titulo'] = $dato['nombre'];
                $truck['ult_latitud'] = $dato['ult_latitud'];
                $truck['ult_longitud'] = $dato['ult_longitud'];
                $truck['ult_velocidad'] = $dato['ult_velocidad'];
                $truck['ult_reporte'] = $dato['ult_reporte'];
                $truck['ult_direccion'] = $dato['ult_direccion'];
                $truck['direccion'] = $dato['ult_direccion'];

                // Detalles del contenedor
                $truck['cntr'] = array(
                    'contenedor' => $camion->contenedor,
                    'type' => $camion->tipoContenedor,
                    'main_status' => $camion->main_status,
                    'status_detail' => $camion->status_cntr,
                );

                // Detalles generales
                $truck['general'] = array(
                    'booking' => $camion->booking,
                    'type' => $camion->cargaType,
                    'retiro_place' => $camion->retiro_place,
                    'commodity' => $camion->commodity,
                    'ref_customer' => $camion->ref_customer,
                    'agent_port' => $camion->agent_port,
                    'id_carga' => $camion->cargaId,
                    'url_carga' => env('FRONT_URL') . '/includes/view_carga_user.php?id=' . $camion->cargaId,

                );

                // Detalles de origen
                $truck['origen'] = array(
                    'description' => $camion->load_place,
                    'lat' => $camion->LoadPlaceLat,
                    'lng' => $camion->LoadPlaceLng,
                    'load_date' => $camion->load_date,
                );

                // Detalles de destino
                $truck['destino'] = array(
                    'description' => $camion->unload_place,
                    'lat' => $camion->UnloadPlaceLat,
                    'lng' => $camion->UnloadPlaceLng,
                    'load_date' => $camion->unload_date,
                );

                // Detalles de aduana
                $truck['aduana'] = array(
                    'description' => $camion->custom_place,
                    'lat' => $camion->aduanaLat,
                    'lng' => $camion->aduanaLng,
                    'load_date' => $camion->load_date,
                );

                // Detalles del conductor
                $truck['driver'] = array(
                    'nombre' => $camion->driver,
                    'documento' => $camion->documento,
                    'carnet' => $camion->vto_carnet,
                    'whatsapp' => $camion->WhatsApp,
                );
            }
            array_push($camiones, $truck);
        }

        return $camiones;
    }

    //Migrar a GO
    public function revisarCoordenadas()
    {
        set_time_limit(120);
        Log::info('revisarCoordenadas: start-----------------------');

        $AKerApiUrl  = 'https://app.akercontrol.com/ws/v2/servicios';
        $AKerApiCode = 'E6HW19';
        $AKerPhone   = '2612128105';

        // Histéresis: si el POI no define OUT en DB, usar factor para salida (p. ej., 1.5x o +50 m)
        $POI_EXIT_FACTOR = 1.5;

        $http = new Client([
            'timeout'         => 7,
            'connect_timeout' => 3,
            'http_errors'     => false,
        ]);

        $detalleComparaciones = [];

        // 1) (truck, contenedor) únicos con POIs y carga activa
        $items = DB::table('asign as a')
            ->join('cntr as c', 'a.cntr_number', '=', 'c.cntr_number')
            ->join('cntr_interest_point as cip', 'c.id_cntr', '=', 'cip.cntr_id_cntr')
            ->whereNull('a.deleted_at')
            ->whereNotNull('a.truck')
            ->where('c.main_status', '!=', 'TERMINADA')
            ->select([
                'a.truck as domain',
                'c.id_cntr as cntr_id',
                'c.cntr_number',
                'c.main_status',
            ])
            ->distinct()
            ->get();

        if ($items->isEmpty()) {
            Log::info('revisarCoordenadas: no hay items activos con POIs');
            return $detalleComparaciones;
        }

        foreach ($items as $item) {
            
        Log::info('Domain: '. json_encode($item->domain) .' - '. json_encode($item->cntr_number));

            $domain     = $item->domain;
            $cntrId     = $item->cntr_id;
            $cntrNumber = $item->cntr_number;
            $statusNow  = $item->main_status;

            // 2) Posición Aker
            $payload = [
                'patentes' => [$domain],
                'cercania' => true,
                'domicilio' => false,
                'apiCode'  => $AKerApiCode,
                'phone'    => $AKerPhone,
            ];

            try {
                $res = $http->post($AKerApiUrl, ['json' => $payload, 'headers' => ['Accept' => 'application/json']]);
                if ($res->getStatusCode() < 200 || $res->getStatusCode() >= 300) {
                    Log::warning("revisarCoordenadas: HTTP {$res->getStatusCode()} consultando Aker para {$domain}");
                    continue;
                }

                $body = json_decode((string)$res->getBody(), true);
                if (!is_array($body) || !isset($body['data'][$domain])) {
                    Log::warning("revisarCoordenadas: respuesta inválida Aker para {$domain}");
                    continue;
                }

                $lat = $this->toFloat($body['data'][$domain]['ult_latitud'] ?? null);
                $lng = $this->toFloat($body['data'][$domain]['ult_longitud'] ?? null);
                if ($lat === null || $lng === null) {
                    Log::warning("revisarCoordenadas: coordenadas vacías para {$domain}");
                    continue;
                }

                // 3) POIs del contenedor en orden (SIN radius_out)
                $pois = DB::table('cntr_interest_point as cip')
                    ->join('interest_points as ip', 'cip.interest_point_id', '=', 'ip.id')
                    ->where('cip.cntr_id_cntr', $cntrId)
                    ->orderBy('cip.order', 'asc')
                    ->get([
                        'cip.id as cip_id',
                        'cip.order',
                        'cip.activo',
                        'ip.id as ip_id',
                        'ip.description as ip_desc',
                        'ip.type as ip_type',
                        'ip.latitude as ip_lat',
                        'ip.longitude as ip_lng',
                        'ip.radius as ip_radius_in', // radio de entrada
                        'ip.status_transition as ip_status',
                    ]);

                if ($pois->isEmpty()) continue;

                // Activo = último con activo != 0
                $activePoi = $pois->filter(fn($p) => (int)$p->activo !== 0)
                    ->sortByDesc('order')
                    ->first();

                Log::info('Active POI: '. json_encode($activePoi));

                $dist = function ($aLat, $aLng, $bLat, $bLng) {
                    return $this->distIfCoords($aLat, $aLng, $this->toFloat($bLat), $this->toFloat($bLng));
                };
                Log::info('Dist: '. json_encode($dist));


                // 4) EXIT del activo (histéresis OUT calculado) si status coincide
                if ($activePoi) {
                    Log::info('Active POI inside: '. json_encode($activePoi));

                    $dActive   = $dist($lat, $lng, $activePoi->ip_lat, $activePoi->ip_lng);
                    $radiusIn  = max(0.0, (float)$activePoi->ip_radius_in);
                    $radiusOut = $this->resolveRadiusOut($radiusIn, null, $POI_EXIT_FACTOR); // no hay radius_out en DB

                    if ($dActive !== null && $dActive > $radiusOut && $statusNow === $activePoi->ip_status) {
                        Log::info('Active POI outside: '. json_encode($activePoi));
                        if ((int)$activePoi->activo === 1) {
                            if (strtolower((string)$activePoi->ip_type) !== 'proceso') {
                                $this->ejecutarAccionSalida($activePoi->ip_id, $cntrId);
                            }
                            DB::table('cntr_interest_point')->where('id', $activePoi->cip_id)->update(['activo' => 2]);

                            $this->logGeoAction([
                                'trip_id'          => (int)$cntrId,
                                'cntr_number'      => $cntrNumber,
                                'domain'           => $domain,
                                'action_type'      => 'EXIT',
                                'point_type'       => 'POI',
                                'distance_m'       => $dActive,
                                'threshold_m'      => (int)$radiusOut,
                                'event_lat'        => $this->toFloat($activePoi->ip_lat),
                                'event_lng'        => $this->toFloat($activePoi->ip_lng),
                                'position_lat'     => $lat,
                                'position_lng'     => $lng,
                                'status_at_moment' => $statusNow,
                                'meta' => [
                                    'poi_id'   => (int)$activePoi->ip_id,
                                    'poi_desc' => $activePoi->ip_desc,
                                    'order'    => (int)$activePoi->order,
                                    'kind'     => $activePoi->ip_type,
                                ],
                            ]);

                            $detalleComparaciones[] = [
                                'cntr_id'          => $cntrId,
                                'truck_domain'     => $domain,
                                'punto_de_interes' => $activePoi->ip_desc,
                                'distancia'        => $dActive,
                                'accion'           => 'salida',
                            ];

                            $activePoi = null; // dejó de estar activo
                        }
                    }
                }

                // 5) ENTER inicial (order=1) si no hay activo
                if (!$activePoi) {
                    Log::info('No active POI, checking first POI for ENTER: '. json_encode($activePoi));
                    $firstPoi = $pois->firstWhere('order', 1);
                    if ($firstPoi) {
                        $dFirst   = $dist($lat, $lng, $firstPoi->ip_lat, $firstPoi->ip_lng);
                        $radiusIn = max(0.0, (float)$firstPoi->ip_radius_in);

                        if ($dFirst !== null && $dFirst <= $radiusIn && $statusNow === $firstPoi->ip_status) {
                            if ((int)$firstPoi->activo === 0) {
                                $this->ejecutarAccionEntrada($firstPoi->ip_id, $cntrId);
                                DB::table('cntr_interest_point')->where('id', $firstPoi->cip_id)->update(['activo' => 1]);

                                $this->logGeoAction([
                                    'trip_id'          => (int)$cntrId,
                                    'cntr_number'      => $cntrNumber,
                                    'domain'           => $domain,
                                    'action_type'      => 'ENTER',
                                    'point_type'       => 'POI',
                                    'distance_m'       => $dFirst,
                                    'threshold_m'      => (int)$radiusIn,
                                    'event_lat'        => $this->toFloat($firstPoi->ip_lat),
                                    'event_lng'        => $this->toFloat($firstPoi->ip_lng),
                                    'position_lat'     => $lat,
                                    'position_lng'     => $lng,
                                    'status_at_moment' => $statusNow,
                                    'meta' => [
                                        'poi_id'   => (int)$firstPoi->ip_id,
                                        'poi_desc' => $firstPoi->ip_desc,
                                        'order'    => (int)$firstPoi->order,
                                        'kind'     => $firstPoi->ip_type,
                                    ],
                                ]);

                                $detalleComparaciones[] = [
                                    'cntr_id'          => $cntrId,
                                    'truck_domain'     => $domain,
                                    'punto_de_interes' => $firstPoi->ip_desc,
                                    'distancia'        => $dFirst,
                                    'accion'           => 'entrada',
                                ];

                                $activePoi = $firstPoi;
                            }
                        }
                    }
                }

                // 6) Transición activo → siguiente
                if ($activePoi) {
                    Log::info('Active POI for transition to next: '. json_encode($activePoi));
                    $idx = $pois->search(fn($p) => (int)$p->cip_id === (int)$activePoi->cip_id);
                    $nextPoi = $pois->get($idx + 1);
                    if ($nextPoi) {
                        $dNext    = $dist($lat, $lng, $nextPoi->ip_lat, $nextPoi->ip_lng);
                        $radiusIn = max(0.0, (float)$nextPoi->ip_radius_in);

                        if ($dNext !== null && $dNext <= $radiusIn && $statusNow === $nextPoi->ip_status) {
                            DB::transaction(function () use ($activePoi, $nextPoi, $cntrId, $cntrNumber, $domain, $lat, $lng, $dNext, $radiusIn, $statusNow, &$detalleComparaciones) {
                                // EXIT del activo si aún no está en 2
                                if ((int)$activePoi->activo !== 2) {
                                    if (strtolower((string)$activePoi->ip_type) !== 'proceso') {
                                        $this->ejecutarAccionSalida($activePoi->ip_id, $cntrId);
                                    }
                                    DB::table('cntr_interest_point')->where('id', $activePoi->cip_id)->update(['activo' => 2]);

                                    $this->logGeoAction([
                                        'trip_id'          => (int)$cntrId,
                                        'cntr_number'      => $cntrNumber,
                                        'domain'           => $domain,
                                        'action_type'      => 'EXIT',
                                        'point_type'       => 'POI',
                                        'distance_m'       => null,
                                        'threshold_m'      => null,
                                        'event_lat'        => $this->toFloat($activePoi->ip_lat),
                                        'event_lng'        => $this->toFloat($activePoi->ip_lng),
                                        'position_lat'     => $lat,
                                        'position_lng'     => $lng,
                                        'status_at_moment' => $statusNow,
                                        'meta' => [
                                            'poi_id'   => (int)$activePoi->ip_id,
                                            'poi_desc' => $activePoi->ip_desc,
                                            'order'    => (int)$activePoi->order,
                                            'kind'     => $activePoi->ip_type,
                                        ],
                                    ]);

                                    $detalleComparaciones[] = [
                                        'cntr_id'          => $cntrId,
                                        'truck_domain'     => $domain,
                                        'punto_de_interes' => $activePoi->ip_desc,
                                        'distancia'        => null,
                                        'accion'           => 'salida',
                                    ];
                                }

                                // ENTER del siguiente si estaba en 0
                                if ((int)$nextPoi->activo === 0) {
                                    $this->ejecutarAccionEntrada($nextPoi->ip_id, $cntrId);
                                    DB::table('cntr_interest_point')->where('id', $nextPoi->cip_id)->update(['activo' => 1]);

                                    $this->logGeoAction([
                                        'trip_id'          => (int)$cntrId,
                                        'cntr_number'      => $cntrNumber,
                                        'domain'           => $domain,
                                        'action_type'      => 'ENTER',
                                        'point_type'       => 'POI',
                                        'distance_m'       => $dNext,
                                        'threshold_m'      => (int)$radiusIn,
                                        'event_lat'        => $this->toFloat($nextPoi->ip_lat),
                                        'event_lng'        => $this->toFloat($nextPoi->ip_lng),
                                        'position_lat'     => $lat,
                                        'position_lng'     => $lng,
                                        'status_at_moment' => $statusNow,
                                        'meta' => [
                                            'poi_id'   => (int)$nextPoi->ip_id,
                                            'poi_desc' => $nextPoi->ip_desc,
                                            'order'    => (int)$nextPoi->order,
                                            'kind'     => $nextPoi->ip_type,
                                        ],
                                    ]);

                                    $detalleComparaciones[] = [
                                        'cntr_id'          => $cntrId,
                                        'truck_domain'     => $domain,
                                        'punto_de_interes' => $nextPoi->ip_desc,
                                        'distancia'        => $dNext,
                                        'accion'           => 'entrada',
                                    ];
                                }
                            });
                        }
                    }
                }
            } catch (GuzzleException $e) {
                Log::error("revisarCoordenadas: error HTTP {$domain}: " . $e->getMessage());
                continue;
            } catch (\Throwable $e) {
                Log::error("revisarCoordenadas: error inesperado {$domain}: " . $e->getMessage());
                continue;
            }
        }
        Log::info('Detalle comparación: ' . json_encode($detalleComparaciones));

        return $detalleComparaciones;
    }
    private function resolveRadiusOut(float $radiusIn, ?float $radiusOut, float $factor = 1.5): float
    {
        $radiusIn  = max(0.0, $radiusIn);
        $radiusOut = $radiusOut !== null ? (float)$radiusOut : 0.0;
        return $radiusOut > 0.0 ? $radiusOut : max($radiusIn * $factor, $radiusIn + 50.0);
    }

    public function calcularDistancia($latitud1, $longitud1, $latitud2, $longitud2)
    {
        // Normaliza: acepta strings con coma/punto, espacios, etc.
        [$lat1, $lon1, $lat2, $lon2] = array_map(function ($v) {
            if ($v === null || $v === '') return NAN;
            if (is_string($v)) $v = str_replace([' ', ','], ['', '.'], $v);
            return is_numeric($v) ? (float)$v : NAN;
        }, [$latitud1, $longitud1, $latitud2, $longitud2]);

        // Si algo es inválido, mantenemos compatibilidad devolviendo 0.0 m
        // (si preferís, puedo hacer que retorne null y ajustamos los call sites).
        if (is_nan($lat1) || is_nan($lon1) || is_nan($lat2) || is_nan($lon2)) {
            Log::warning('calcularDistancia: coordenadas inválidas', [
                'lat1' => $latitud1,
                'lon1' => $longitud1,
                'lat2' => $latitud2,
                'lon2' => $longitud2
            ]);
            return 0.0;
        }

        // Haversine directo en METROS (evita ida y vuelta a km)
        $R = 6371000.0; // radio de la Tierra en metros
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

        return $R * $c; // metros
    }

    public function ejecutarAccionEntrada($puntoActivoId, $contenedorId)
    {
        // Obtener datos del contenedor desde la tabla 'cntr'

        $contenedor = DB::table('cntr')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->join('carga', 'cntr.booking', '=', 'carga.booking')
            ->where('cntr.id_cntr', $contenedorId)
            ->select('cntr.*', 'asign.*', 'carga.*')
            ->first();
        $punto = DB::table('interest_points')->where('id', $puntoActivoId)->first();
        if ($contenedor->cma_t_o != null) {

            $base    = rtrim(env('API_CMA_BOTZERO'), '/');
            $client = new Client();
            $headers = ['Content-Type' => 'application/json'];
            $request = new Psr7Request('GET', "{$base}/cma/estArrAtCusLoc/{$contenedor->cma_t_o}/{$contenedor->cntr_number}/{$punto->latitude}/{$punto->longitude}", $headers);
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);
            Log::info('Respuesta CMA - Est Arr At Cus Loc: ' . $respuesta);

            // ---------- POST a n8n ----------
            try {
                $payload = [
                    'function'   => __FUNCTION__, // te manda el nombre de la función actual
                    'contenedor' => $contenedor->cntr_number,
                    'cma_t_o'    => $contenedor->cma_t_o,
                    'lat'        => $punto->latitude,
                    'lon'        => $punto->longitude,
                    'respuesta'  => $r, // lo que devolvió CMA
                ];

                $postRes = $client->post('https://n8n.rail.ar/webhook/reporte-cma', [
                    'headers' => $headers,
                    'json'    => $payload,
                ]);

                Log::info('Posteado a n8n: ' . $postRes->getBody());
            } catch (\Exception $e) {
                Log::error('Error enviando a n8n: ' . $e->getMessage());
            }
        }

        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');
        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
        $carga = Carga::whereNull('deleted_at')->where('booking', '=', $contenedor->booking)->first();
        $cliente = DB::table('users')
            ->where('cliente_id', '=', $carga->client_id)
            ->first();



        if ($sbx[0]->sandbox == 0) {

            if (!$cliente) {
                // Logueás un warning para debug
                Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                // Podés definir un mail fallback para no perder la notificación
                $clienteEmail = 'soporte@botzero.com.ar';
            } else {
                $clienteEmail = $cliente->email;
            }

            $customer = DB::table('users')
                ->where('username', '=', $carga->user)
                ->value('email');
            $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);
            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new PuntoInteresEntrada($contenedor, $punto));
        } else {

            if (!$cliente) {
                // Logueás un warning para debug
                Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                // Podés definir un mail fallback para no perder la notificación
                $clienteEmail = 'soporte@botzero.com.ar';
            } else {
                $clienteEmail = $cliente->email;
            }

            $customer = DB::table('users')
                ->where('username', '=', $carga->user)
                ->value('email');
            $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);
            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new PuntoInteresEntrada($contenedor, $punto));
        }
        //ENTRADA
        if ($punto->accion_correo_customer_entrada) {
            if ($sbx[0]->sandbox == 0) {
                $customer = DB::table('users')->where('username', $contenedor->user)->first();
                Mail::to($customer->email)->send(new PuntoInteresEntrada($contenedor, $punto));
            }
        }
        if ($punto->accion_correo_cliente_entrada) {
            /* Enviar correo al cliente
            $customer = DB::table('users')->where('username', $contenedor->user_cntr)->first();
            Mail::to( $customer)->send(new MailPuntoDeInteres($contenedor, $punto ));*/
        }
        if ($punto->accion_notificacion_customer_entrada) {
            /* Enviar correo al cliente
            $customer = DB::table('users')->where('username', $contenedor->user_cntr)->first();
            Mail::to( $customer)->send(new MailPuntoDeInteres($contenedor, $punto ));*/
        }
        if ($punto->accion_notificacion_customer_entrada) {
            /* Enviar correo al cliente
            $customer = DB::table('users')->where('username', $contenedor->user_cntr)->first();
            Mail::to( $customer)->send(new MailPuntoDeInteres($contenedor, $punto ));*/
        }
    }
    public function ejecutarAccionSalida($puntoActivoId, $contenedorId)
    {
        // Obtener datos del contenedor desde la tabla 'cntr'
        $contenedor = DB::table('cntr')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->join('carga', 'cntr.booking', '=', 'carga.booking')
            ->where('cntr.id_cntr', $contenedorId)
            ->select('cntr.*', 'asign.*', 'carga.*')
            ->first();

        $punto = DB::table('interest_points')->where('id', $puntoActivoId)->first();

        if ($contenedor->cma_t_o != null) {

            $base    = rtrim(env('API_CMA_BOTZERO'), '/');
            $client = new Client();
            $headers = ['Content-Type' => 'application/json'];
            $request = new Psr7Request('GET', "{$base}/cma/estArrAtCusLoc/{$contenedor->cma_t_o}/{$contenedor->cntr_number}/{$punto->latitude}/{$punto->longitude}", $headers);
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);
            //Log::info('Respuesta CMA - Est Arr At Cus Loc: ' . $r);


            // ---------- POST a n8n ----------
            try {
                $payload = [
                    'function'   => __FUNCTION__, // te manda el nombre de la función actual
                    'contenedor' => $contenedor->cntr_number,
                    'cma_t_o'    => $contenedor->cma_t_o,
                    'lat'        => $punto->latitude,
                    'lon'        => $punto->longitude,
                    'respuesta'  => $r, // lo que devolvió CMA
                ];

                $postRes = $client->post('https://n8n.rail.ar/webhook/reporte-cma', [
                    'headers' => $headers,
                    'json'    => $payload,
                ]);

                Log::info('Posteado a n8n: ' . $postRes->getBody());
            } catch (\Exception $e) {
                Log::error('Error enviando a n8n: ' . $e->getMessage());
            }
        }

        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');
        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
        $carga = Carga::whereNull('deleted_at')->where('booking', '=', $contenedor->booking)->first();
        $cliente = DB::table('users')
            ->where('cliente_id', '=', $carga->client_id)
            ->first();
        if ($sbx[0]->sandbox == 0) {
            if (!$cliente) {
                // Logueás un warning para debug
                Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                // Podés definir un mail fallback para no perder la notificación
                $clienteEmail = 'soporte@botzero.com.ar';
            } else {
                $clienteEmail = $cliente->email;
            }
            $customer = DB::table('users')
                ->where('username', '=', $carga->user)
                ->value('email');
            $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);
            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new PuntoInteresSalida($contenedor, $punto));
        } else {
            if (!$cliente) {
                // Logueás un warning para debug
                Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                // Podés definir un mail fallback para no perder la notificación
                $clienteEmail = 'soporte@botzero.com.ar';
            } else {
                $clienteEmail = $cliente->email;
            }
            $customer = DB::table('users')
                ->where('username', '=', $carga->user)
                ->value('email');
            $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);
            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new PuntoInteresSalida($contenedor, $punto));
        }
        //SALIDA
        if ($punto->accion_correo_customer_salida) {
            /* Enviar correo al cliente
            $customer = DB::table('users')->where('username', $contenedor->user_cntr)->first();
            Mail::to($customer)->send(new PuntoInteresSalida($contenedor, $punto));*/
        }
        if ($punto->accion_correo_cliente_salida) {
            /* Enviar correo al cliente
            $customer = DB::table('users')->where('username', $contenedor->user_cntr)->first();
            Mail::to( $customer)->send(new MailPuntoDeInteres($contenedor, $punto ));*/
        }
        if ($punto->accion_notificacion_customer_salida) {
            /* Enviar correo al cliente
            $customer = DB::table('users')->where('username', $contenedor->user_cntr)->first();
            Mail::to( $customer)->send(new MailPuntoDeInteres($contenedor, $punto ));*/
        }
        if ($punto->accion_notificacion_cliente_salida) {
            /* Enviar correo al cliente
            $customer = DB::table('users')->where('username', $contenedor->user_cntr)->first();
            Mail::to( $customer)->send(new MailPuntoDeInteres($contenedor, $punto ));*/
        }
    }

    private function logGeoAction(array $args): void
    {
        // args esperados:
        // trip_id, cntr_number, domain,
        // action_type ('ENTER'|'EXIT'),
        // point_type ('CARGA'|'ADUANA'|'DESCARGA'),
        // distance_m, threshold_m,
        // event_lat, event_lng,
        // position_lat, position_lng,
        // status_at_moment (opcional), aker_time (opcional), meta (opcional)

        try {
            GeoActionLog::create([
                'trip_id'          => $args['trip_id'],
                'cntr_number'      => $args['cntr_number'],
                'domain'           => $args['domain'],
                'action_type'      => $args['action_type'],
                'point_type'       => $args['point_type'],
                'distance_m'       => $args['distance_m'] ?? null,
                'threshold_m'      => $args['threshold_m'] ?? null,
                'event_lat'        => $args['event_lat'] ?? null,
                'event_lng'        => $args['event_lng'] ?? null,
                'position_lat'     => $args['position_lat'] ?? null,
                'position_lng'     => $args['position_lng'] ?? null,
                'status_at_moment' => $args['status_at_moment'] ?? null,
                'aker_time'        => $args['aker_time'] ?? null,
                'meta'             => $args['meta'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('logGeoAction error: ' . $e->getMessage(), ['args' => $args]);
        }
    }
    private function fireEndpoint(Client $http, string $url, string $tag, int $IdTrip): void
{
    try {
        $res = $http->get($url, ['http_errors' => false, 'timeout' => 10, 'connect_timeout' => 5]);
        $code = $res->getStatusCode();
        if ($code < 200 || $code >= 300) {
            Log::warning("{$tag}: HTTP {$code} para IdTrip={$IdTrip} url={$url}");
        } else {
            Log::info("{$tag}: OK para IdTrip={$IdTrip}");
        }
    } catch (\Throwable $e) {
        Log::error("{$tag}: error para IdTrip={$IdTrip}: ".$e->getMessage());
    }
}
}
