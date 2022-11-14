<?php

namespace App\Http\Controllers;

use App\Models\asign;
use App\Models\position;
use App\Models\pruebasModel;
use App\Models\truck;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LDAP\Result;
use Mockery\Undefined;

//wget -O /dev/null "https://rail.com.ar/api/servicioSatelital"
// tiempo */2 * * * *

class ServiceSatelital extends Controller
{
    public function serviceSatelital()
    {

        $todosMisCamiones = DB::table('trucks')
            ->join('asign', 'trucks.domain', '=', 'asign.truck')
            ->join('cntr', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->join('carga', 'carga.booking', '=', 'cntr.booking')
            ->join('aduanas', 'aduanas.description', '=', 'carga.custom_place')
            ->join('customer_load_place', 'customer_load_place.description', '=', 'carga.load_place')
            ->join('customer_unload_place', 'customer_unload_place.description', '=', 'carga.unload_place')
            ->select('cntr.id_cntr as IdTrip', 'carga.id as idCarga', 'trucks.id', 'trucks.id_satelital', 'trucks.domain', 'customer_load_place.description as LugarCarga', 'customer_load_place.lat as CargaLat', 'customer_load_place.lon as CargaLng', 'aduanas.description as LugarAduana', 'aduanas.lat as aduanaLat', 'aduanas.lon as aduanaLon', 'customer_unload_place.description as lugarDescarga', 'customer_unload_place.lat as descargaLat', 'customer_unload_place.lon as descargaLon')
            ->where('trucks.domain', '=', 'AE792WJ')
            ->get();

            $chek = new pruebasModel();
                $chek->contenido = 'Consulto las patentes del Camion';
                $chek->save();


        foreach ($todosMisCamiones as $camion) {

            $chek = new pruebasModel();
            $chek->contenido = 'Inbgrso al Camion '. $camion->domain;
            $chek->save();


            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json'
            ];
            $body = '{
                    "patentes":["' . $camion->domain . '"],
                    "cercania":true,
                    "domicilio":false,
                    "apiKey":"a4f0a4e8e5d34e5b7b7bc16dab941060a5c848c9",
                    "phone":"2612128105"
                    }';

            $request = new Psr7Request('GET', 'https://app.akercontrol.com/ws/v2/servicios', $headers, $body); // TEST: E6HW19 - PRODUCCION: C2QC20
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);
            $keys = array($r);

            $datos = $keys[0]['data'][$camion->domain];

            $posicionLat = $datos['ult_latitud'];
            $posicionLon = $datos['ult_longitud'];


            $IdTrip = $camion->IdTrip;
            return $IdTrip;
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

            if ($d <= 200) { // lugar de Carga

                $chek = new pruebasModel();
                $chek->contenido = 'entro a lugar de carga / Camion: '.$camion->domain;
                $chek->save();

                $clientCarga = new Client();
                $requestCarga = new Psr7Request('GET', 'https://rail.com.ar/api/accionLugarDeCarga/' . $IdTrip);
                $resCarga = $clientCarga->sendAsync($requestCarga)->wait();
                return $resCarga;
            }

            if ($d2 <= 200) { // lugar de aduana


                $chek = new pruebasModel();
                $chek->contenido = 'entro a lugar de Aduana / Camion: '.$camion->domain;
                $chek->save();

                $clientAduana = new Client();
                $requestAduana = new Psr7Request('GET', 'https://rail.com.ar/api/accionLugarAduana/' . $IdTrip);
                $resAduana = $clientAduana->sendAsync($requestAduana)->wait();
                return $resAduana;
            }
            if ($d3 <= 200) { // lugar de descarga



                $chek = new pruebasModel();
                $chek->contenido = 'entro a lugar de descarga / Camion: '.$camion->domain;
                $chek->save();

                $clientDescarga = new Client();
                $requestDescarga = new Psr7Request('GET', 'https://rail.com.ar/api/accionLugarDescarga/' . $IdTrip);
                $resDescarga = $clientDescarga->sendAsync($requestDescarga)->wait();
                return $resDescarga;
            }

            $chek = new pruebasModel();
            $chek->contenido = 'No esta cerca de ningun lado / camion: '.$camion->domain;
            $chek->save();
        }
    }
}
