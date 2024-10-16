<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterestPoint;
use Illuminate\Support\Facades\Validator;
use App\Models\cntr;
use App\Models\Carga;
use Illuminate\Support\Facades\DB;

class InterestPointController extends Controller
{
    public function index()
    {
        $InterestPoint = InterestPoint::whereNull('deleted_at')->get();

        return response()->json($InterestPoint);
    }
    // Store a new interest point
    public function store(Request $request)
    {
        try {
            // Validación de los datos
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|in:punto,proceso',  // Validación para el campo "type"
                'description' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                //'radius' => 'required|numeric|min:0',
                'status_transition' => 'required|string|max:255',
                // Añade validaciones para los checkboxes (booleanos)
                'accion_correo_customer_entrada' => 'boolean',
                'accion_correo_cliente_entrada' => 'boolean',
                'accion_notificacion_customer_entrada' => 'boolean',
                'accion_notificacion_cliente_entrada' => 'boolean',
                'accion_correo_customer_salida' => 'boolean',
                'accion_correo_cliente_salida' => 'boolean',
                'accion_notificacion_customer_salida' => 'boolean',
                'accion_notificacion_cliente_salida' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 400);
            }

            if($request->type === 'punto'){
                $radius = 1000;
            }else{
                $radius = 5000;
            }
            // Creación del punto de interés
            $interestPoint = new InterestPoint([
                'type' => $request->type,
                'description' => $request->description,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $radius,
                'status_transition' => $request->status_transition,
                
                // Acciones cuando se entra
                'accion_correo_customer_entrada' => $request->accion_correo_customer_entrada,
                'accion_correo_cliente_entrada' => $request->accion_correo_cliente_entrada,
                'accion_notificacion_customer_entrada' => $request->accion_notificacion_customer_entrada,
                'accion_notificacion_cliente_entrada' => $request->accion_notificacion_cliente_entrada,
                
                // Acciones cuando se sale
                'accion_correo_customer_salida' => $request->accion_correo_customer_salida,
                'accion_correo_cliente_salida' => $request->accion_correo_cliente_salida,
                'accion_notificacion_customer_salida' => $request->accion_notificacion_customer_salida,
                'accion_notificacion_cliente_salida' => $request->accion_notificacion_cliente_salida,
            ]);

            $interestPoint->save();

            return response()->json(['message' => 'Punto de interés creado con éxito'], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'No se pudo crear el punto de interés: ' . $e->getMessage()], 500);
        }
    }

    // Update an existing interest point
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|in:punto,proceso',  // Validación para el campo "type"
                'description' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'required|numeric|min:0',
                'status_transition' => 'required|string|max:255',
                // Añade validaciones para los checkboxes (booleanos)
                'accion_correo_customer_entrada' => 'boolean',
                'accion_correo_cliente_entrada' => 'boolean',
                'accion_notificacion_customer_entrada' => 'boolean',
                'accion_notificacion_cliente_entrada' => 'boolean',
                'accion_correo_customer_salida' => 'boolean',
                'accion_correo_cliente_salida' => 'boolean',
                'accion_notificacion_customer_salida' => 'boolean',
                'accion_notificacion_cliente_salida' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 400);
            }
            if($request->type === 'punto'){
                $radius = 1000;
            }else{
                $radius = 5000;
            }

            // Buscar el punto de interés por ID
            $interestPoint = InterestPoint::findOrFail($id);

            // Actualizar los campos del punto de interés
            $interestPoint->description = $request->description;
            $interestPoint->latitude = $request->latitude;
            $interestPoint->longitude = $request->longitude;
            $interestPoint->radius =  $radius;
            $interestPoint->status_transition = $request->status_transition;

            // Acciones cuando se entra
            $interestPoint->accion_correo_customer_entrada = $request->accion_correo_customer_entrada ?? 0;
            $interestPoint->accion_correo_cliente_entrada = $request->accion_correo_cliente_entrada ?? 0;
            $interestPoint->accion_notificacion_customer_entrada = $request->accion_notificacion_customer_entrada ?? 0;
            $interestPoint->accion_notificacion_cliente_entrada = $request->accion_notificacion_cliente_entrada ?? 0;

            // Acciones cuando se sale
            $interestPoint->accion_correo_customer_salida = $request->accion_correo_customer_salida ?? 0;
            $interestPoint->accion_correo_cliente_salida = $request->accion_correo_cliente_salida ?? 0;
            $interestPoint->accion_notificacion_customer_salida = $request->accion_notificacion_customer_salida ?? 0;
            $interestPoint->accion_notificacion_cliente_salida = $request->accion_notificacion_cliente_salida ?? 0;

            // Guardar los cambios
            $interestPoint->save();

            return response()->json(['message' => 'Punto de interés actualizado con éxito'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'No se pudo actualizar el punto de interés: ' . $e->getMessage()], 500);
        }
    }

    // Delete an interest point
    public function destroy($id)
    {
        try {
            $interestPoint = InterestPoint::findOrFail($id);
            $interestPoint->delete();

            return response()->json(['message' => 'Punto de interés eliminado correctamente'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'No se pudo eliminar el punto de interés: ' . $e->getMessage()], 500);
        }
    }

    public function agregarPuntoInteresCarga(Request $request, $id)
    {
        // Obtener los datos de 'points' en lugar de 'points_data'
        $pointsData = $request->input('points');
        
        // Verificar que pointsData no sea null
        if (!is_array($pointsData) || empty($pointsData)) {
            return response()->json(['message' => 'Datos de puntos de interés no válidos o vacíos'], 400);
        }

        $cargaPorId = Carga::whereNull('deleted_at')->where('id', '=', $id)->first();

        if (!$cargaPorId) {
            return response()->json(['message' => 'Carga no encontrada'], 404);
        }
        
        // Buscar los cntr asociados con el cargaId
        $cntrs = cntr::where('booking', $cargaPorId->booking)->get();

        // Procesar cada punto de interés y asociarlo con los cntr
        foreach ($pointsData as $point) {
            // Buscar o crear el punto de interés                  
            $interestPoint = InterestPoint::find($point['point_id']);

            if (!$interestPoint) {
                return response()->json(['message' => 'Punto de interés no encontrado con ID ' . $point['id']], 404);
            }

            // Asociar el punto de interés con cada cntr
            foreach ($cntrs as $cntr) {
                // Usar syncWithoutDetaching para actualizar o agregar el valor sin eliminar otros puntos de interés existentes
                $cntr->interestPoints()->syncWithoutDetaching([
                    $interestPoint->id => ['order' => $point['order']]
                ]);
            }            
        }

        // Responder con éxito
        return response()->json(['message' => 'Puntos de interés actualizados exitosamente']);
    }

    public function puntoInteresCntr($id)
    {
        try {
            // Buscar el CNTR
            $cntr = cntr::whereNull('deleted_at')->where('id_cntr', '=', $id)->first();
            
            if (!$cntr) {
                return response()->json(['error' => 'CNTR no encontrado'], 404);
            }

            // Obtener los puntos de interés relacionados al CNTR
            $puntosDeInteres = DB::table('cntr_interest_point')
                ->join('interest_points', 'cntr_interest_point.interest_point_id', '=', 'interest_points.id')
                ->where('cntr_interest_point.cntr_id_cntr', '=', $cntr->id_cntr)
                ->select('interest_points.*', 'cntr_interest_point.order', 'cntr_interest_point.activo')
                ->get();

            return response()->json($puntosDeInteres);

        } catch (\Exception $e) {
            // Manejo de errores o excepciones
            return response()->json(['error' => 'Error al obtener los puntos de interés', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function ModificarPuntoInteresCntr($id)
    {
        try {
            // Buscar el CNTR
            $cntr = cntr::whereNull('deleted_at')->where('id_cntr', '=', $id)->first();
            
            if (!$cntr) {
                return response()->json(['error' => 'CNTR no encontrado'], 404);
            }

            // Obtener los puntos de interés relacionados al CNTR
            $puntosDeInteres = DB::table('cntr_interest_point')
                ->join('interest_points', 'cntr_interest_point.interest_point_id', '=', 'interest_points.id')
                ->where('cntr_interest_point.cntr_id_cntr', '=', $cntr->id_cntr)
                ->select('interest_points.*', 'cntr_interest_point.order', 'cntr_interest_point.activo')
                ->get();

            return response()->json($puntosDeInteres);

        } catch (\Exception $e) {
            // Manejo de errores o excepciones
            return response()->json(['error' => 'Error al obtener los puntos de interés', 'message' => $e->getMessage()], 500);
        }
    }
    public function actualizarPuntosInteresCntr(Request $request, $id)
    {
        // Obtener el CNTR a partir del ID
        $cntr = cntr::whereNull('deleted_at')->where('id_cntr', '=', $id)->first();

        if (!$cntr) {
            return response()->json(['error' => 'CNTR no encontrado'], 404); // Retorna un error si no se encuentra el CNTR
        }

        // Verificar que haya puntos en el request
        $pointsData = $request->input('points');
        if (!is_array($pointsData) || empty($pointsData)) {
            return response()->json(['message' => 'Datos de puntos de interés no válidos o vacíos'], 400);
        }

        // Eliminar todos los puntos de interés actuales de este CNTR
        DB::table('cntr_interest_point')->where('cntr_id_cntr', '=', $cntr->id_cntr)->delete();

        // Agregar los nuevos puntos de interés
        foreach ($pointsData as $point) {
            // Verificar que el punto de interés exista
            $interestPoint = InterestPoint::find($point['point_id']);
            if (!$interestPoint) {
                return response()->json(['message' => 'Punto de interés no encontrado con ID ' . $point['point_id']], 404);
            }

            // Insertar el nuevo punto de interés con su orden en la tabla intermedia
            $cntr->interestPoints()->attach($interestPoint->id, ['order' => $point['order']]);
        }

        return response()->json(['message' => 'Puntos de interés actualizados correctamente'], 200);
    }
}
