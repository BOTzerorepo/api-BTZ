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
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use LDAP\Result;
use Mockery\Undefined;

use function PHPUnit\Framework\returnSelf;

// wget -O /dev/null "https://rail.com.ar/api/servicioSatelital"
// tiempo */2 * * * *

class ServiceSatelital extends Controller
{
    public function servicePrueba()
    {
        return env('APP_URL') . env('APP_NAME');
    }
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

    //MIGRAR A GO
    public function serviceSatelital()
    {

        $todosMisCamiones = DB::table('trucks')
            ->join('asign', 'trucks.domain', '=', 'asign.truck')
            ->join('cntr', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->join('carga', 'carga.booking', '=', 'cntr.booking')
            ->leftJoin('aduanas', 'aduanas.description', '=', 'carga.custom_place')
            ->join('customer_load_places', 'customer_load_places.description', '=', 'carga.load_place')
            ->join('customer_unload_places', 'customer_unload_places.description', '=', 'carga.unload_place')
            ->select('cntr.id_cntr as IdTrip', 'carga.id as idCarga', 'trucks.id', 'trucks.id_satelital', 'trucks.domain', 'customer_load_places.description as LugarCarga', 'customer_load_places.latitud as CargaLat', 'customer_load_places.longitud as CargaLng', 'aduanas.description as LugarAduana', 'aduanas.lat as aduanaLat', 'aduanas.lon as aduanaLon', 'customer_unload_places.description as lugarDescarga', 'customer_unload_places.latitud as descargaLat', 'customer_unload_places.longitud as descargaLon')
            ->where('carga.deleted_at', '=', null)
            ->where('cntr.main_status', '!=', 'TERMINADA')
            ->where('trucks.alta_aker', '!=', 0)
            ->get();



        foreach ($todosMisCamiones as $camion) {

            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json'
            ];


            if (env('APP_ENV') === 'production') {
                $body = '{
                    "patentes":["' . $camion->domain . '"],
                    "cercania":true,
                    "domicilio":false,
                    "apiCode":"E6HW19",
                    "phone":"2612128105"
                    }';
            } else {
                $body = '{
                    "patentes":["' . $camion->domain . '"],
                    "cercania":true,
                    "domicilio":false,
                    "apiCode":"E6HW19",
                    "phone":"2612128105"
                    }';
            }

            $request = new Psr7Request('GET', 'https://app.akercontrol.com/ws/v2/servicios', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);
            $keys = array($r);

            /*$r = [ 
                'data' => [ 
                    $camion->domain => [
                        'ult_latitud' =>  -32.865946127108366,
                        'ult_longitud' => -70.14974261078306
                    ]       
                ] 
            ];*/


            if (array_key_exists('data', $r)) {

                $datos = $keys[0]['data'][$camion->domain];
                $posicionLat = $datos['ult_latitud'];
                $posicionLon = $datos['ult_longitud'];

                $positionDB = new position();
                $positionDB->dominio = $camion->domain;
                $positionDB->lat = $posicionLat;
                $positionDB->lng = $posicionLon;
                $positionDB->asigned = 1;

                $positionDB->save();

                $IdTrip = $camion->IdTrip;
                //return [$posicionLat, $posicionLon, $camion->CargaLat, $camion->CargaLng];
                // Calcular distancias usando la función reutilizable
                $d = $this->calcularDistancia($posicionLat, $posicionLon, $camion->CargaLat, $camion->CargaLng); // Distancia al lugar de Carga
                $d2 = $this->calcularDistancia($posicionLat, $posicionLon, $camion->aduanaLat, $camion->aduanaLon); // Distancia al lugar de Aduana
                $d3 = $this->calcularDistancia($posicionLat, $posicionLon, $camion->descargaLat, $camion->descargaLon); // Distancia al lugar de Descarga
                //return [$d, $d2, $d3];
                // Si está dentro del rango de 200 metros, realizar las acciones actuales
                if ($d <= 200) { // Dentro del rango de Carga
                    $clientCarga = new Client();
                    $requestCarga = new Psr7Request('GET', env('APP_URL') . '/api/accionLugarDeCarga/' . $IdTrip);
                    $resCarga = $clientCarga->sendAsync($requestCarga)->wait();
                }

                if ($d2 <= 200) { // Dentro del rango de Aduana
                    $clientAduana = new Client();
                    $requestAduana = new Psr7Request('GET', env('APP_URL') . '/api/accionLugarAduana/' . $IdTrip);
                    $resAduana = $clientAduana->sendAsync($requestAduana)->wait();
                }

                if ($d3 <= 200) { // Dentro del rango de Descarga
                    $clientDescarga = new Client();
                    $requestDescarga = new Psr7Request('GET', env('APP_URL') . '/api/accionLugarDescarga/' . $IdTrip);
                    $resDescarga = $clientDescarga->sendAsync($requestDescarga)->wait();
                }

                $qc = DB::table('cntr')->select('cntr_number', 'booking', 'confirmacion')->where('id_cntr', '=', $IdTrip)->get();
                $cntr = $qc[0];

                // cual es el ultimo status.
                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->main_status;

                // Si está fuera del rango de 1000 metros, realizar una acción
                if ($d > 200 && $description === "CARGANDO") { // Fuera del rango de Carga
                    $clientCarga = new Client();
                    $requestCarga = new Psr7Request('GET', env('APP_URL') . '/api/accionFueraLugarDeCarga/' . $IdTrip);
                    $resCarga = $clientCarga->sendAsync($requestCarga)->wait();
                }

                if ($d2 > 200 && $description === "EN ADUANA") { // Fuera del rango de Aduana
                    $clientAduana = new Client();
                    $requestAduana = new Psr7Request('GET', env('APP_URL') . '/api/accionFueraLugarAduana/' . $IdTrip);
                    $resAduana = $clientAduana->sendAsync($requestAduana)->wait();
                }
            }
        }
    }

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
                ->where('asign.truck', '=', $domain)
                ->whereNotNull('trucks.domain') // Aseguramos que la unión principal se mantenga
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
        $detalleComparaciones = [];

        $asignaciones = DB::table('asign as a')
            ->join('cntr as c', 'a.cntr_number', '=', 'c.cntr_number') // Unir con la tabla cntr
            ->join('cntr_interest_point as cip', 'c.id_cntr', '=', 'cip.cntr_id_cntr') // Unir con puntos de interés
            ->whereNull('a.deleted_at')
            ->whereNotNull('a.truck')
            ->whereIn('a.booking', DB::table('carga')
                ->where('status', '!=', 'TERMINADA')
                ->pluck('booking'))
            ->select('a.*', 'c.*') // Seleccionar columnas necesarias
            ->distinct() // Evitar duplicados
            ->get();
            
            
        foreach ($asignaciones as $asignacion) {
            // Obtener los datos del truck y el contenedor a partir de la asignación
            $truckDomain = $asignacion->truck;  // Dominio del truck
            $cntrNumber = $asignacion->cntr_number;    // Número del contenedor

            // Obtener los datos del contenedor desde la tabla cntr
            $contenedor = DB::table('cntr')->where('cntr_number', $cntrNumber)->first();

            if (!$contenedor || !$truckDomain) {
                continue; // Si no se encuentra el contenedor o el dominio del truck, se omite esta asignación
            }

            // Realizar una solicitud a la API para obtener las coordenadas del truck
            $client = new Client();
            $headers = ['Content-Type' => 'application/json'];
            $body = json_encode([
                "patentes" => [$truckDomain],
                "cercania" => true,
                "domicilio" => false,
                "apiCode" => "E6HW19",
                "phone" => "2612128105"
            ]);

            $request = new Psr7Request('GET', 'https://app.akercontrol.com/ws/v2/servicios', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);

            /*$r = [
                'data' => [ 
                    $truckDomain => [
                        'ult_latitud' => -32.843325941231974,
                        'ult_longitud' => -70.12031486495702
                    ]       
                ] 
            ];*/

            // Verificar si la solicitud fue exitosa y si hay coordenadas disponibles
            if (isset($r['data'])) {
                $datos = $r['data'][$truckDomain];  // Obtener las coordenadas del truck
                $latitud = $datos['ult_latitud'];
                $longitud = $datos['ult_longitud'];

                // Obtener los puntos de interés asociados al CNTR, ordenados por el campo "order"
                $puntosDeInteres = DB::table('cntr_interest_point')
                    ->join('interest_points', 'cntr_interest_point.interest_point_id', '=', 'interest_points.id')
                    ->where('cntr_interest_point.cntr_id_cntr', $contenedor->id_cntr)
                    ->select(
                        'cntr_interest_point.order',
                        'cntr_interest_point.cntr_id_cntr',
                        'cntr_interest_point.interest_point_id',
                        'cntr_interest_point.activo',
                        'cntr_interest_point.id as cntr_interest_point_id',
                        'interest_points.id as interest_point_id',
                        'interest_points.latitude',
                        'interest_points.type',
                        'interest_points.status_transition',
                        'interest_points.longitude',
                        'interest_points.radius',
                        'interest_points.description',
                        // Acciones al entrar
                        'interest_points.accion_correo_customer_entrada',
                        'interest_points.accion_correo_cliente_entrada',
                        'interest_points.accion_notificacion_customer_entrada',
                        'interest_points.accion_notificacion_cliente_entrada',
                        // Acciones al salir
                        'interest_points.accion_correo_customer_salida',
                        'interest_points.accion_correo_cliente_salida',
                        'interest_points.accion_notificacion_customer_salida',
                        'interest_points.accion_notificacion_cliente_salida'
                    )
                    ->orderBy('cntr_interest_point.order', 'asc') // Ordenar por el campo "order"
                    ->get();

                // Identificar el punto de interés activo
                // Filtrar puntos activos (activo no es 0) y ordenarlos por "order" en forma descendente
                $puntoActivo = $puntosDeInteres
                    ->filter(function ($punto) {
                        return $punto->activo !== 0; // Filtrar puntos activos
                    })
                    ->sortByDesc('order') // Ordenar por el campo "order" en forma descendente
                    ->first(); // Obtener el primer resultado de la lista filtrada y ordenada

                if ($puntoActivo) {
                    // Calcular la distancia con el punto activo
                    $distanciaPuntoActivo = $this->calcularDistancia($latitud, $longitud, $puntoActivo->latitude, $puntoActivo->longitude);

                    // Si la distancia es mayor al radio, significa que el camión ha salido
                    if ($distanciaPuntoActivo > $puntoActivo->radius && $contenedor->main_status === $puntoActivo->status_transition) {
                        // 1. Verificar si el estado es 1 (ya se envió el correo de entrada)
                        if ($puntoActivo->activo === 1) {
                            // 2. Realizar las acciones de salida del punto de interés activo
                            if ($puntoActivo->type != "proceso") {
                                $this->ejecutarAccionSalida($puntoActivo->interest_point_id, $contenedor->id_cntr);
                            }
                            // 3. Marcar el punto de interés activo como 2 (se envió correo de salida)
                            DB::table('cntr_interest_point')
                                ->where('id', $puntoActivo->cntr_interest_point_id)
                                ->update(['activo' => 2]);

                            // Guardar el detalle de la salida
                            $detalleComparacion = [
                                'cntr_id' => $contenedor->id_cntr,
                                'truck_domain' => $truckDomain,
                                'punto_de_interes_id' => $puntoActivo->description,
                                'distancia' => $distanciaPuntoActivo,
                                'accion' => 'salida'
                            ];
                            $detalleComparaciones[] = $detalleComparacion;

                            // Desactivar el punto activo
                            $puntoActivo = null;
                        }
                    }
                } else {
                    // Si no hay un punto activo, buscar el primer punto de interés
                    $puntoInteresInicial = $puntosDeInteres->firstWhere('order', 1);

                    if ($puntoInteresInicial) {
                        // Calcular la distancia al punto inicial
                        $distancia = $this->calcularDistancia($latitud, $longitud, $puntoInteresInicial->latitude, $puntoInteresInicial->longitude);

                        // Si está dentro del radio del primer punto, marcarlo como activo
                        if ($distancia <= $puntoInteresInicial->radius && $contenedor->main_status === $puntoInteresInicial->status_transition) {
                            // 1. Verificar si el estado es 0 (no se ha enviado ningún correo)
                            if ($puntoInteresInicial->activo === 0) {
                                // 2. Realizar las acciones de entrada del punto inicial
                                $this->ejecutarAccionEntrada($puntoInteresInicial->interest_point_id, $contenedor->id_cntr);

                                // 3. Marcar el punto de interés como 1 (se envió correo de entrada)
                                DB::table('cntr_interest_point')
                                    ->where('id', $puntoInteresInicial->cntr_interest_point_id)
                                    ->update(['activo' => 1]);

                                // Guardar el detalle de la entrada
                                $detalleComparacion = [
                                    'cntr_id' => $contenedor->id_cntr,
                                    'truck_domain' => $truckDomain,
                                    'punto_de_interes_id' => $puntoInteresInicial->description,
                                    'distancia' => $distancia,
                                    'accion' => 'entrada'
                                ];
                                $detalleComparaciones[] = $detalleComparacion;

                                // Marcar como punto activo
                                $puntoActivo = $puntoInteresInicial;
                            }
                        }
                    }
                }

                // Si hay un nuevo punto activo, buscar el siguiente punto en orden
                if ($puntoActivo) {

                    $indicePuntoActivo = $puntosDeInteres->search(function ($punto) use ($puntoActivo) {
                        return $punto->cntr_interest_point_id === $puntoActivo->cntr_interest_point_id;
                    });

                    // Obtener el siguiente punto de interés en la lista
                    $siguientePunto = $puntosDeInteres->get($indicePuntoActivo + 1);

                    if ($siguientePunto) {
                        // Calcular la distancia con el siguiente punto de interés
                        $distanciaSiguiente = $this->calcularDistancia($latitud, $longitud, $siguientePunto->latitude, $siguientePunto->longitude);

                        // Si está dentro del radio del siguiente punto
                        if ($distanciaSiguiente <= $siguientePunto->radius && $contenedor->main_status === $siguientePunto->status_transition) {
                            // Si el punto activo NO tiene el estado 2, se ejecuta la acción de salida
                            if ($puntoActivo->activo !== 2) {
                                // 1. Realizar las acciones de salida del punto activo
                                if ($puntoActivo->type != "proceso") {
                                    $this->ejecutarAccionSalida($puntoActivo->interest_point_id, $contenedor->id_cntr);
                                }
                                // 2. Marcar el punto activo como 2 (se envió correo de salida)
                                DB::table('cntr_interest_point')
                                    ->where('id', $puntoActivo->cntr_interest_point_id)
                                    ->update(['activo' => 2]);

                                // Guardar el detalle de la salida
                                $detalleComparacionSalida = [
                                    'cntr_id' => $contenedor->id_cntr,
                                    'truck_domain' => $truckDomain,
                                    'punto_de_interes_id' => $puntoActivo->description,
                                    'distancia' => $distanciaPuntoActivo,
                                    'accion' => 'salida'
                                ];
                                $detalleComparaciones[] = $detalleComparacionSalida;
                            }

                            // Verificar el estado del siguiente punto (debe ser 0 para enviar correo de entrada)
                            if ($siguientePunto->activo === 0 && $contenedor->main_status == $siguientePunto->status_transition) {
                                // 4. Realizar las acciones de entrada en el siguiente punto
                                $this->ejecutarAccionEntrada($siguientePunto->interest_point_id, $contenedor->id_cntr);

                                // 5. Marcar el siguiente punto como 1 (se envió correo de entrada)
                                DB::table('cntr_interest_point')
                                    ->where('id', $siguientePunto->cntr_interest_point_id)
                                    ->update(['activo' => 1]);

                                // Guardar el detalle de la entrada
                                $detalleComparacionEntrada = [
                                    'cntr_id' => $contenedor->id_cntr,
                                    'truck_domain' => $truckDomain,
                                    'punto_de_interes_id' => $siguientePunto->description,
                                    'distancia' => $distanciaSiguiente,
                                    'accion' => 'entrada'
                                ];
                                $detalleComparaciones[] = $detalleComparacionEntrada;
                            }
                        }
                    }
                }
            }
        }
        return $detalleComparaciones; // Retorna los detalles de comparación si es necesario
    }
    public function calcularDistancia($latitud1, $longitud1, $latitud2, $longitud2)
    {
        $radioTierra = 6371; // Radio de la Tierra en kilómetros
        $dLatitud = deg2rad($latitud2 - $latitud1);
        $dLongitud = deg2rad($longitud2 - $longitud1);
        $a = sin($dLatitud / 2) * sin($dLatitud / 2) +
            cos(deg2rad($latitud1)) * cos(deg2rad($latitud2)) *
            sin($dLongitud / 2) * sin($dLongitud / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distanciaEnKilometros = $radioTierra * $c;

        return $distanciaEnKilometros * 1000; // Convertir a metros
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

        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');
        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);

        if ($sbx[0]->sandbox == 0) {
            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new PuntoInteresEntrada($contenedor, $punto));
        } else {
            Mail::to(['copia@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo3@botzero.com.ar'])
                ->cc(['equipodemo2@botzero.com.ar', 'copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])
                ->bcc($inboxEmail)->send(new PuntoInteresEntrada($contenedor, $punto));
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

        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');
        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);

        if ($sbx[0]->sandbox == 0) {
            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new PuntoInteresSalida($contenedor, $punto));
        } else {
            Mail::to(['copia@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo3@botzero.com.ar'])
                ->cc(['equipodemo2@botzero.com.ar', 'copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])
                ->bcc($inboxEmail)->send(new PuntoInteresSalida($contenedor, $punto));
        }
        //SALIDA
        if ($punto->accion_correo_customer_salida) {
            // Enviar correo al cliente
            $customer = DB::table('users')->where('username', $contenedor->user_cntr)->first();
            Mail::to($customer)->send(new PuntoInteresSalida($contenedor, $punto));
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
}
