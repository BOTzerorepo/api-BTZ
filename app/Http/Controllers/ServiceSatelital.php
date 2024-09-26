<?php

namespace App\Http\Controllers;

use App\Mail\puntoDeInteres as MailPuntoDeInteres;
use App\Models\akerTruck;
use App\Models\asign;
use App\Models\cntr;
use App\Models\itinerario;
use App\Models\logapi;
use App\Models\position;
use App\Models\pruebasModel;
use App\Models\PuntoDeInteres;
use App\Models\statu;
use App\Models\Transport;
use App\Models\truck;
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

    public function serviceSatelital()
    {
        $todosMisCamiones = DB::table('trucks')
            ->join('asign', 'trucks.domain', '=', 'asign.truck')
            ->join('cntr', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->join('carga', 'carga.booking', '=', 'cntr.booking')
            ->join('aduanas', 'aduanas.description', '=', 'carga.custom_place')
            ->join('customer_load_places', 'customer_load_places.description', '=', 'carga.load_place')
            ->join('customer_unload_places', 'customer_unload_places.description', '=', 'carga.unload_place')
            ->select('cntr.id_cntr as IdTrip', 'carga.id as idCarga', 'trucks.id', 'trucks.id_satelital', 'trucks.domain', 'customer_load_places.description as LugarCarga', 'customer_load_places.latitud as CargaLat', 'customer_load_places.longitud as CargaLng', 'aduanas.description as LugarAduana', 'aduanas.lat as aduanaLat', 'aduanas.lon as aduanaLon', 'customer_unload_places.description as lugarDescarga', 'customer_unload_places.latitud as descargaLat', 'customer_unload_places.longitud as descargaLon')
            ->where('cntr.main_status', '!=', 'TERMINADA')
            ->where('trucks.alta_aker', '!=', 0)
            ->get();

        foreach ($todosMisCamiones as $camion) {

            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json'
            ];

            // TEST: E6HW19 - PRODUCCION: C2QC20

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

                $Radio = 6371e3; // metres
                $φ1 = $posicionLat * pi() / 180; // φ, λ in radians
                $φ2 = $camion->CargaLat * pi() / 180;
                $φ3 = $camion->aduanaLat * pi() / 180;
                $φ4 = $camion->descargaLat * pi() / 180;

                $Δφ = ($posicionLat - $camion->CargaLat) * pi() / 180;
                $Δφ2 = ($posicionLat - $camion->aduanaLat) * pi() / 180;
                $Δφ3 = ($posicionLat - $camion->descargaLat) * pi() / 180;

                $Δλ = ($posicionLon - $camion->CargaLng) * pi() / 180;
                $Δλ2 = ($posicionLon - $camion->aduanaLon) * pi() / 180;
                $Δλ3 = ($posicionLon - $camion->descargaLon) * pi() / 180;

                $a = sin($Δφ / 2) * sin($Δφ / 2) + cos($φ1) * cos($φ2) * sin($Δλ / 2) * sin($Δλ / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $d = $Radio * $c; // in metres

                $a2 = sin($Δφ2 / 2) * sin($Δφ2 / 2) + cos($φ1) * cos($φ3) * sin($Δλ2 / 2) * sin($Δλ2 / 2);
                $c2 = 2 * atan2(sqrt($a2), sqrt(1 - $a2));
                $d2 = $Radio * $c2; // in metres

                $a3 = sin($Δφ3 / 2) * sin($Δφ3 / 2) + cos($φ1) * cos($φ4) * sin($Δλ3 / 2) * sin($Δλ3 / 2);
                $c3 = 2 * atan2(sqrt($a3), sqrt(1 - $a3));
                $d3 = $Radio * $c3; // in metres */


                 // Traer puntos de interes general 

                //if 

                    // /api/accionLugarDeCarga/ 

                        // Opcion Mail 
                        // Opcion Notificcion
                        // opcion actualizacion.

                // Armar formula por cada punto de interes asociado al viaje.
                 //if 

                    // /api/accionLugarDeCarga/ 

                        // Opcion Mail 
                        // Opcion Notificcion
                        // opcion actualizacion.
                // .....................::COTIZAR::.........................//
               
                // Formulario de Carga de Punto de Interés.
                // formulario de Edición punto de Interés.
                // Index itinerario.(puntos de interés asociados a un viaje) endpoints de Index puntos de interés.
            
                // endpoints de accion 

                // cambiar esta logica (agregarla acá).

                
                if ($d <= 200) { // lugar de Carga

                    $clientCarga = new Client();
                    $requestCarga = new Psr7Request('GET', env('APP_URL') . '/api/accionLugarDeCarga/' . $IdTrip);
                    $resCarga = $clientCarga->sendAsync($requestCarga)->wait();
                }

                if ($d2 <= 200) { // lugar de aduana

                    $clientAduana = new Client();
                    $requestAduana = new Psr7Request('GET', env('APP_URL') . '/api/accionLugarAduana/' . $IdTrip);
                    $resAduana = $clientAduana->sendAsync($requestAduana)->wait();
                }
                if ($d3 <= 200) { // lugar de descarga

                    $clientDescarga = new Client();
                    $requestDescarga = new Psr7Request('GET', env('APP_URL') . '/api/accionLugarDescarga/' . $IdTrip);
                    $resDescarga = $clientDescarga->sendAsync($requestDescarga)->wait();
                }



                // Agregar punntos Criticos Globales.
            }
        }

        $truckPosition = DB::table('trucks')->where('alta_aker', "!=", 0)->get();

        foreach ($truckPosition as $camion) {

            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json'
            ];


            $body = '{
                    "patentes":["' . $camion->domain . '"],
                    "cercania":true,
                    "domicilio":false,
                    "apiCode":"E6HW19",
                    "phone":"2612128105"
                    }';


            $request = new Psr7Request('GET', 'https://app.akercontrol.com/ws/v2/servicios', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);
            $keys = array($r);

            if (array_key_exists('data', $r)) {

                $datos = $keys[0]['data'][$camion->domain];
                $posicionLat = $datos['ult_latitud'];
                $posicionLon = $datos['ult_longitud'];

                $positionDB = new position();
                $positionDB->dominio = $camion->domain;
                $positionDB->lat = $posicionLat;
                $positionDB->lng = $posicionLon;
                $positionDB->save();
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
    public function flotaTransport($transport)
    {
        $transport = Transport::find($transport);
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

    public function revisarCoordenadas()
    {
        $detalleComparaciones = [];
        // Obtener todas las unidades desde el modelo Truck
        $unidades = Truck::all();
        foreach ($unidades as $unidad) {

            // Realizar una solicitud a la API para obtener las coordenadas de la unidad
            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json'
            ];


            $body = '{
                    "patentes":["' . $unidad->domain . '"],
                    "cercania":true,
                    "domicilio":false,
                    "apiCode":"E6HW19",
                    "phone":"2612128105"
                    }';


            $request = new Psr7Request('GET', 'https://app.akercontrol.com/ws/v2/servicios', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);
            $keys = array($r);

            // Verificar si la solicitud fue exitosa y si hay coordenadas disponibles
            if (isset($r['data'])) {
                $datos = $r['data'][$unidad->domain];
                $latitud = $datos['ult_latitud'];
                $longitud = $datos['ult_longitud'];

                // Obtener los itinerarios activos que tienen puntos de interés asociados
                $itinerarios = Itinerario::where('estado', 1)
                    ->with(['puntosDeInteres' => function ($query) {
                        $query->where('estado', 1);
                    }])
                    ->get();

                foreach ($itinerarios as $itinerario) {
                    foreach ($itinerario->puntosDeInteres as $puntoDeInteres) {
                        // Calcular la distancia entre la coordenada de la unidad y la coordenada del punto de interés
                        $distancia = ServiceSatelital::calcularDistancia($latitud, $longitud, $puntoDeInteres->latitud, $puntoDeInteres->longitud);

                        // Si la distancia es menor o igual al rango de aviso del punto de interés, ejecutar la acción asociada
                        if ($distancia <= $puntoDeInteres->rango) { // ver como hacer para comparar en metros

                            $accion = ServiceSatelital::ejecutarAccion($puntoDeInteres, $itinerario->trip_id);

                            $detalleComparacion = [
                                'itinerario_id' => $itinerario->id,
                                'unidad' => $itinerario->unidad_asignada,

                                'punto_de_interes_id' => $puntoDeInteres->id,
                                'distancia' => $distancia,
                                'lugar' => 'esta cerca',
                                'accion_ejecutada' => $puntoDeInteres->accion_id ?? null,
                                'accion' => $accion
                            ];
                            $detalleComparaciones[] = $detalleComparacion;
                        } else {

                            $detalleComparacion = [
                                'itinerario_id' => $itinerario->id,
                                'unidad' => $itinerario->unidad_asignada,

                                'punto_de_interes_id' => $puntoDeInteres->id,
                                'distancia' => $distancia,
                                'lugar' => 'esta lejos',
                                'accion_ejecutada' => $puntoDeInteres->accion_id ?? null,
                                'accion' => 'nada'

                            ];
                            $detalleComparaciones[] = $detalleComparacion;
                        }
                    }
                }
            }
        }

        return response()->json(['detalle_comparaciones' => $detalleComparaciones]);
        return response()->json(['message' => 'Revisión de coordenadas completada.']);
    }
    public function calcularDistancia($latitud1, $longitud1, $latitud2, $longitud2)
    {
        // Fórmula de Haversine para calcular la distancia entre dos puntos en coordenadas geográficas
        $radioTierra = 6371; // Radio de la Tierra en kilómetros
        $dLatitud = deg2rad($latitud2 - $latitud1);
        $dLongitud = deg2rad($longitud2 - $longitud1);
        $a = sin($dLatitud / 2) * sin($dLatitud / 2) +
            cos(deg2rad($latitud1)) * cos(deg2rad($latitud2)) *
            sin($dLongitud / 2) * sin($dLongitud / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distanciaEnKilometros = $radioTierra * $c * 1000;
        // Convertir de kilómetros a metros

        return $distanciaEnKilometros; // Distancia en metros // Distancia en kilómetros
    }

    public function ejecutarAccion(PuntoDeInteres $puntoDeInteres, $trip_id)
    {

        //return 'se envia correo:' . $puntoDeInteres;

        $datos =  DB::table('cntr')
            ->join('users', 'cntr.user_cntr', '=', 'users.username')
            ->leftjoin('itinerarios', 'cntr.id_cntr', '=', 'itinerarios.trip_id')
            ->leftjoin('punto_de_interes', 'itinerarios.id', '=', 'punto_de_interes.itinerario_id')
            ->join('carga', 'carga.booking', '=', 'cntr.booking')
            ->select('carga.ref_customer', 'cntr.id_cntr', 'cntr.booking', 'cntr.cntr_number', 'cntr.cntr_type', 'cntr.confirmacion', 'cntr.retiro_place', 'cntr.company', 'cntr.status_cntr', 'cntr.main_status', 'users.username', 'users.email', 'users.celular', 'users.empresa', 'punto_de_interes.*', 'itinerarios.unidad_asignada')
            ->where('cntr.id_cntr', '=', $trip_id)
            ->where('punto_de_interes.id', '=', $puntoDeInteres->id)
            ->get();

        if ($puntoDeInteres->accion_mail == 1) {

            // revisa si status tambien hay que cambiar

            // avisa por correo normal 


            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');

            if ($sbx[0]->sandbox == 0) {

                Mail::to($datos[0]->email)->bcc($inboxEmail)->send(new MailPuntoDeInteres($datos[0]));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email punto de interes to:" . $datos[0]->email;
                $logApi->save();
            } elseif ($sbx[0]->sandbox == 2) {

                Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new MailPuntoDeInteres($datos[0]));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email Instructivo to: pablorio@botzero.tech";
                $logApi->save();
            } else {

                Mail::to($datos[0]->email)->bcc($inboxEmail)->send(new MailPuntoDeInteres($datos[0]));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email punto de interes to:" . $datos[0]->email;
                $logApi->save();
            }
        }

        if ($puntoDeInteres->accion_status == 1) {

            // buscar el status que está y subir un nivel. 

            $status = DB::table('status_type')->where('STATUS', $datos[0]->main_status)->first();
            $findId = $status->id + 1;
            $newstatus = DB::table('status_type')->where('id', $findId)->first();

            DB::table('cntr')
                ->where('id_cntr', $trip_id)
                ->update([
                    'main_status' => $newstatus->STATUS,
                    'status_cntr' => 'modificado automáticamente por punto de interés: ' . $datos[0]->descripcion
                ]);
            $status = new statu();
            $status->status = 'modificado automáticamente por punto de interés: ' . $datos[0]->descripcion;
            if ($puntoDeInteres->accion_mail == 1) {
                $status->avisado = 1;
            } else {
                $status->avisado = 0;
            }
            $status->main_status = $newstatus->STATUS;
            $status->cntr_number = $datos[0]->cntr_number;
            $status->user_status = 'Automatico';
            $status->save();
        }

        $puntoDeInteres->estado = 2;
        $puntoDeInteres->save();
    }
}
