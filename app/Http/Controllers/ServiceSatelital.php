<?php

namespace App\Http\Controllers;

use App\Models\position;
use App\Models\truck;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use LDAP\Result;
use Mockery\Undefined;

class ServiceSatelital extends Controller
{
    public function serviceSatelital()
    {

        $i = 0;
        foreach (truck::all() as $truck) {

            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json'
            ];
            $body = '{"patentes":["' . $truck->domain . '"], 
            "cercania":true, 
            "domiclio":false,
            "apiKey":"a4f0a4e8e5d34e5b7b7bc16dab941060a5c848c9"
            }';

            $request = new Psr7Request('GET', 'https://app.akercontrol.com/ws/flota/2612128105/E6HW19', $headers, $body); // TEST: E6HW19 - PRODUCCION: C2QC20
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);
            $keys = array($r);
            $todos = $keys[0]['data'];

            if(isset(array_values($todos)[$i])){
            
            $particular = array_values($todos)[$i];
            $i = $i + 1;

            // ultima Latitud y longitud de aker
            $latAker = $particular['ult_latitud'];
            $lngAker = $particular['ult_longitud'];
            
            $position = new position();
            $position->dominio = $particular['patente'];;
            $position->lat = $latAker;
            $position->lng = $lngAker;
            $position->save();


            /*  echo 'El Cambion esta posicionado en:' . $latAker . $lngAker; */

            $requestBOT = new Psr7Request('GET', 'https://rail.com.ar/api/lugarDeCarga/' . $particular['patente']);
            $resBOT = $client->sendAsync($requestBOT)->wait();
            $respuestaBOT = $resBOT->getBody();
            $rBOT = json_decode($respuestaBOT, true);

            if ($rBOT == null) {

                echo 'no hay nada';

            } else {

                $rBOT = $rBOT[0];
                $latCarga = $rBOT['lat'];
                $lngCarga = $rBOT['lon'];
                $lugarCarga = $rBOT['load_place'];
                $lugarAduana = $rBOT['custom_place'];
                $latAduana = $rBOT['latA'];
                $lngAduana = $rBOT['lonA'];
                $lugarDescarga = $rBOT['unload_place'];
                $latDescarga = $rBOT['latU'];
                $lonDescarga = $rBOT['lonU'];

                $IdTrip = $rBOT['IdTrip'];
                $Radio = 6371e3; // metres
                $φ1 = $latAker * pi() / 180; // φ, λ in radians
                $φ2 = $latCarga * pi() / 180;
                $φ3 = $latAduana * pi() / 180;
                $φ4 = $latDescarga * pi() / 180;

                $Δφ = ($latAker - $latCarga) * pi() / 180;
                $Δφ2 = ($latAker - $latAduana) * pi() / 180;
                $Δφ3 = ($latAker - $latDescarga) * pi() / 180;

                $Δλ = ($lngAker - $lngCarga) * pi() / 180;
                $Δλ2 = ($lngAker - $lngAduana) * pi() / 180;
                $Δλ3 = ($lngAker - $lonDescarga) * pi() / 180;

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

                    $clientCarga = new Client();
                    $requestCarga = new Psr7Request('GET', 'https://rail.com.ar/api/accionLugarDeCarga/' . $IdTrip);
                    $resCarga = $clientCarga->sendAsync($requestCarga)->wait();
                    return $resCarga;
                }

                if ($d2 <= 200) { // lugar de aduana

                    $clientAduana = new Client();
                    $requestAduana = new Psr7Request('GET', 'https://rail.com.ar/api/accionLugarAduana/' . $IdTrip);
                    $resAduana = $clientAduana->sendAsync($requestAduana)->wait();
                    return $resAduana;
                }
                if ($d3 <= 200) { // lugar de descarga

                    $clientDescarga = new Client();
                    $requestDescarga = new Psr7Request('GET', 'https://rail.com.ar/api/accionLugarDescarga/' . $IdTrip);
                    $resDescarga = $clientDescarga->sendAsync($requestDescarga)->wait();
                    return $resDescarga;
                }
            }

            // Latitudes y Longitudes de Lugares

            echo 'no esta en ningun punto';
        }
        }
    }
}
