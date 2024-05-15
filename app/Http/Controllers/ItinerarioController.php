<?php

namespace App\Http\Controllers;

use App\Models\itinerario;
use App\Models\PuntoDeInteres;
use Illuminate\Http\Request;

class ItinerarioController extends Controller
{
    public function guardarFormulario(Request $request)
    {

        //return $request;
        $itinerario = Itinerario::create($request->all());

        // Crear puntos de interés asociados al itinerario
        $orden = 1;
        foreach ($request->puntos_de_interes as $punto) {
            $punto['orden'] = $orden;
            $itinerario->puntosDeInteres()->create($punto);
            $orden++;
        }

        $itinerarioFinal = Itinerario::find($itinerario->id)
            ->join('punto_de_interes', 'itinerarios.id', '=', 'punto_de_interes.itinerario_id')
            ->join('customer_load_places', 'itinerarios.carga_id', '=', 'customer_load_places.id')
            ->join('customer_unload_places', 'itinerarios.descarga_id', '=', 'customer_unload_places.id')
            ->join('cntr', 'itinerarios.trip_id', '=', 'cntr.id_cntr')
            ->select(
                'itinerarios.unidad_asignada',
                'customer_load_places.description as ldc_desc',
                'customer_load_places.latitud as ldc_lat',
                'customer_load_places.longitud as ldc_lng',
                'customer_load_places.rango as ldc_rango',
                'customer_unload_places.description as ldu_desc',
                'customer_unload_places.latitud as ldu_lat',
                'customer_unload_places.longitud as ldu_lng',
                'customer_unload_places.rango as ldu_rango',
                'punto_de_interes.descripcion as pi_desc',
                'punto_de_interes.latitud as pi_lat',
                'punto_de_interes.longitud as long',
                'punto_de_interes.rango as pi_rango',
                'punto_de_interes.accion_mail as pi_mail',
                'punto_de_interes.accion_status as pi_status',
                'punto_de_interes.accion_notificacion as pi_notificacion',
                'cntr.cntr_number as referenciaTrip',
                'cntr.booking as referenciaGeneral'
            )->get();

        $data = json_decode($itinerarioFinal, true); // $tu_json es tu JSON original

        // Extraer los datos generales
        $datos_generales = [
            "unidad_asignada" => $data[0]["unidad_asignada"],
            "ldc_desc" => $data[0]["ldc_desc"],
            "ldc_lat" => $data[0]["ldc_lat"],
            "ldc_lng" => $data[0]["ldc_lng"],
            "ldc_rango" => $data[0]["ldc_rango"],
            "ldu_desc" => $data[0]["ldu_desc"],
            "ldu_lat" => $data[0]["ldu_lat"],
            "ldu_lng" => $data[0]["ldu_lng"],
            "ldu_rango" => $data[0]["ldu_rango"],
            "referenciaTrip" => $data[0]["referenciaTrip"],
            "referenciaGeneral" => $data[0]["referenciaGeneral"]
        ];

        // Extraer los detalles de carga y descarga
        $carga = [
            "tipo" => "Carga",
            "descripcion" => $data[0]["ldc_desc"],
            "latitud" => $data[0]["ldc_lat"],
            "longitud" => $data[0]["ldc_lng"],
            "rango" => $data[0]["ldc_rango"]
        ];

        $descarga = [
            "tipo" => "Descarga",
            "descripcion" => $data[0]["ldu_desc"],
            "latitud" => $data[0]["ldu_lat"],
            "longitud" => $data[0]["ldu_lng"],
            "rango" => $data[0]["ldu_rango"]
        ];

        // Extraer los detalles de puntos de interés
        $puntos_de_interes = [];
        foreach ($data as $detalle) {
            $puntos_de_interes[] = [
                "tipo" => "Punto de Interés",
                "descripcion" => $detalle["pi_desc"],
                "latitud" => $detalle["pi_lat"],
                "longitud" => $detalle["long"],
                "rango" => $detalle["pi_rango"],
                "accion_mail" => $detalle["pi_mail"],
                "accion_status" => $detalle["pi_status"],
                "accion_notificacion" => $detalle["pi_notificacion"]
            ];
        }

        // Combinar todos los datos en un solo arreglo
        $resultado = [
            "datos_generales" => $datos_generales,
            "detalles" => array_merge([$carga, $descarga], $puntos_de_interes)
        ];
        // Convertir el arreglo a JSON
        $json_final = json_encode($resultado, JSON_PRETTY_PRINT);

        return $json_final;
    }
    public function index()
    {
        $itinerarios = Itinerario::join('punto_de_interes', 'itinerarios.id', '=', 'punto_de_interes.itinerario_id')
        ->join('customer_load_places', 'itinerarios.carga_id', '=', 'customer_load_places.id')
        ->join('customer_unload_places', 'itinerarios.descarga_id', '=', 'customer_unload_places.id')
        ->join('cntr', 'itinerarios.trip_id', '=', 'cntr.id_cntr')
        ->select(
            'itinerarios.id',
            'itinerarios.unidad_asignada',
            'customer_load_places.description as ldc_desc',
            'customer_load_places.latitud as ldc_lat',
            'customer_load_places.longitud as ldc_lng',
            'customer_load_places.rango as ldc_rango',
            'customer_unload_places.description as ldu_desc',
            'customer_unload_places.latitud as ldu_lat',
            'customer_unload_places.longitud as ldu_lng',
            'customer_unload_places.rango as ldu_rango',
            'punto_de_interes.descripcion as pi_desc',
            'punto_de_interes.latitud as pi_lat',
            'punto_de_interes.longitud as long',
            'punto_de_interes.rango as pi_rango',
            'punto_de_interes.accion_mail as pi_mail',
            'punto_de_interes.accion_status as pi_status',
            'punto_de_interes.accion_notificacion as pi_notificacion',
            'cntr.cntr_number as referenciaTrip',
            'cntr.booking as referenciaGeneral'
        )->get();

        $itinerarios_con_detalles = [];

        foreach ($itinerarios as $itinerario) {
            // Extraer los datos generales
            $datos_generales = [
                "unidad_asignada" => $itinerario->unidad_asignada,
                "ldc_desc" => $itinerario->ldc_desc,
                "ldc_lat" => $itinerario->ldc_lat,
                "ldc_lng" => $itinerario->ldc_lng,
                "ldc_rango" => $itinerario->ldc_rango,
                "ldu_desc" => $itinerario->ldu_desc,
                "ldu_lat" => $itinerario->ldu_lat,
                "ldu_lng" => $itinerario->ldu_lng,
                "ldu_rango" => $itinerario->ldu_rango,
                "referenciaTrip" => $itinerario->referenciaTrip,
                "referenciaGeneral" => $itinerario->referenciaGeneral
            ];

            // Extraer los detalles de carga y descarga
            $carga = [
                "tipo" => "Carga",
                "descripcion" => $itinerario->ldc_desc,
                "latitud" => $itinerario->ldc_lat,
                "longitud" => $itinerario->ldc_lng,
                "rango" => $itinerario->ldc_rango
            ];

            $descarga = [
                "tipo" => "Descarga",
                "descripcion" => $itinerario->ldu_desc,
                "latitud" => $itinerario->ldu_lat,
                "longitud" => $itinerario->ldu_lng,
                "rango" => $itinerario->ldu_rango
            ];

            // Extraer los detalles de puntos de interés
            $puntos_de_interes = [
                "tipo" => "Punto de Interés",
                "descripcion" => $itinerario->pi_desc,
                "latitud" => $itinerario->pi_lat,
                "longitud" => $itinerario->long,
                "rango" => $itinerario->pi_rango,
                "accion_mail" => $itinerario->pi_mail,
                "accion_status" => $itinerario->pi_status,
                "accion_notificacion" => $itinerario->pi_notificacion
            ];

            // Combinar todos los datos en un solo arreglo
            $itinerarios_con_detalles[] = [
                "id" => $itinerario->id,
                "datos_generales" => $datos_generales,
                "detalles" => array_merge([$carga, $descarga], [$puntos_de_interes])
            ];
        }

        // Convertir el arreglo a JSON
        $json_final = json_encode($itinerarios_con_detalles, JSON_PRETTY_PRINT);

        return $json_final;
    }

    public function show($id)
    {
        //return $request;
       
        $itinerarioFinal = Itinerario::find($id)
            ->leftjoin('customer_load_places', 'itinerarios.carga_id', '=', 'customer_load_places.id')
            ->leftjoin('customer_unload_places', 'itinerarios.descarga_id', '=', 'customer_unload_places.id')
            ->leftjoin('cntr', 'itinerarios.trip_id', '=', 'cntr.id_cntr')
            ->select(
                'itinerarios.unidad_asignada',
                'customer_load_places.description as ldc_desc',
                'customer_load_places.latitud as ldc_lat',
                'customer_load_places.longitud as ldc_lng',
                'customer_load_places.rango as ldc_rango',
                'customer_unload_places.description as ldu_desc',
                'customer_unload_places.latitud as ldu_lat',
                'customer_unload_places.longitud as ldu_lng',
                'customer_unload_places.rango as ldu_rango',
                /* 'punto_de_interes.descripcion as pi_desc',
                'punto_de_interes.latitud as pi_lat',
                'punto_de_interes.longitud as long',
                'punto_de_interes.rango as pi_rango',
                'punto_de_interes.accion_mail as pi_mail',
                'punto_de_interes.accion_status as pi_status',
                'punto_de_interes.accion_notificacion as pi_notificacion', */
                'cntr.cntr_number as referenciaTrip',
                'cntr.booking as referenciaGeneral'
            )->get();

        $data = json_decode($itinerarioFinal, true); // $tu_json es tu JSON original

        // Extraer los datos generales
        $datos_generales = [
            "unidad_asignada" => $data[0]["unidad_asignada"],
            "ldc_desc" => $data[0]["ldc_desc"],
            "ldc_lat" => $data[0]["ldc_lat"],
            "ldc_lng" => $data[0]["ldc_lng"],
            "ldc_rango" => $data[0]["ldc_rango"],
            "ldu_desc" => $data[0]["ldu_desc"],
            "ldu_lat" => $data[0]["ldu_lat"],
            "ldu_lng" => $data[0]["ldu_lng"],
            "ldu_rango" => $data[0]["ldu_rango"],
            "referenciaTrip" => $data[0]["referenciaTrip"],
            "referenciaGeneral" => $data[0]["referenciaGeneral"]
        ];

        // Extraer los detalles de carga y descarga
        $carga = [
            "tipo" => "Carga",
            "descripcion" => $data[0]["ldc_desc"],
            "latitud" => $data[0]["ldc_lat"],
            "longitud" => $data[0]["ldc_lng"],
            "rango" => $data[0]["ldc_rango"]
        ];

        $descarga = [
            "tipo" => "Descarga",
            "descripcion" => $data[0]["ldu_desc"],
            "latitud" => $data[0]["ldu_lat"],
            "longitud" => $data[0]["ldu_lng"],
            "rango" => $data[0]["ldu_rango"]
        ];

        // Extraer los detalles de puntos de interés


        $puntos_de_interes = [];
        $datos = PuntoDeInteres::where('itinerario_id','=',$id)->get();
        foreach ($datos as $detalle) {
            $puntos_de_interes[] = [
                "tipo" => "Punto de Interés",
                "descripcion" => $detalle["descripcion"],
                "latitud" => $detalle["latitud"],
                "longitud" => $detalle["longitud"],
                "rango" => $detalle["rango"],
                "accion_mail" => $detalle["accion_mail"],
                "accion_status" => $detalle["accion_status"],
                "accion_notificacion" => $detalle["accion_notificacion"]
            ];
        } 

        // Combinar todos los datos en un solo arreglo
        $resultado = [
            "datos_generales" => $datos_generales,
            "detalles" => array_merge([$carga, $descarga] , $puntos_de_interes )
        ];
        // Convertir el arreglo a JSON
        $json_final = json_encode($resultado, JSON_PRETTY_PRINT);

        return $json_final;
    }

}
