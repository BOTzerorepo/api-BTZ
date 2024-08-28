<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterestPoint;
use Illuminate\Support\Facades\Validator;
use App\Models\cntr;
use App\Models\Carga;

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
            $validator = Validator::make($request->all(), [
                'description' => 'required|string|max:255',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'required|numeric',
                // Añade aquí las validaciones para los otros campos
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 400);
            }

            $interestPoint = new InterestPoint([
                'description' => $request->description,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $request->radius,
                'trigger_on' => $request->trigger_on,
                'status_on_trigger' => $request->status_on_trigger,
                'is_general' => $request->is_general,
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
                'description' => 'required|string|max:255',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'required|numeric',
                // Añade aquí las validaciones para los otros campos
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 400);
            }

            $interestPoint = InterestPoint::findOrFail($id);

            $interestPoint->description = $request->description;
            $interestPoint->latitude = $request->latitude;
            $interestPoint->longitude = $request->longitude;
            $interestPoint->radius = $request->radius;
            $interestPoint->trigger_on = $request->trigger_on;
            $interestPoint->status_on_trigger = $request->status_on_trigger;
            $interestPoint->is_general = $request->is_general;


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

    public function puntoInteresCarga(Request $request, $id)
    {
        // Obtener los datos de 'points' en lugar de 'points_data'
        $pointsData = $request->input('points');
        
        // Verificar que pointsData no sea null
        if (!is_array($pointsData) || empty($pointsData)) {
            return response()->json(['error' => 'Datos de puntos de interés no válidos o vacíos'], 400);
        }

        $cargaPorId = Carga::whereNull('deleted_at')->where('id', '=', $id)->first();

        if (!$cargaPorId) {
            return response()->json(['error' => 'Carga no encontrada'], 404);
        }
        
        // Buscar los cntr asociados con el cargaId
        $cntrs = cntr::where('booking', $cargaPorId->booking)->get();

        // Procesar cada punto de interés y asociarlo con los cntr
        foreach ($pointsData as $point) {
            // Buscar o crear el punto de interés                  
            $interestPoint = InterestPoint::find($point['point_id']);

            if (!$interestPoint) {
                return response()->json(['error' => 'Punto de interés no encontrado con ID ' . $point['id']], 404);
            }

            // Asociar el punto de interés con cada cntr
            foreach ($cntrs as $cntr) {
                $cntr->interestPoints()->updateExistingPivot($interestPoint->id, ['order' => $point['order']]);
            }
        }

        // Responder con éxito
        return response()->json(['message' => 'Puntos de interés actualizados exitosamente']);
    }


}
