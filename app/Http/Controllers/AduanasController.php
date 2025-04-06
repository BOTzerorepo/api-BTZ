<?php

namespace App\Http\Controllers;

use App\Models\Aduana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AduanasController extends Controller
{
    public function index()
    {
        try {
            $aduanas = Aduana::orderBy('description', 'ASC')->get();
            return response()->json([
                'data' => $aduanas,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        $aduana = DB::table('aduanas')->where('id', $id)->get();
        return $aduana;
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'provincia' => 'nullable|string',
                'pais' => 'required|string',
                'lat' => 'nullable|numeric',
                'lon' => 'nullable|numeric',
                'user' => 'required|string',
                'company' => 'required|string',
            ]);
            $aduana = new Aduana($validated);
            $aduana->km_from_town = 0;
            $aduana->link_maps = 'https://www.google.es/maps?q=' . $request->lat . ',' . $request->lon;
            $aduana->save();
            return response()->json([
                'message' => 'Se cargó correctamente la aduana',
                'data' => $aduana,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la aduana.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request,  $id)
    {
        $aduana = Aduana::findOrFail($id);
        $aduana->description = $request['description'];
        $aduana->address = $request['address'];
        $aduana->provincia = $request['provincia'];
        $aduana->pais = $request['pais'];
        $aduana->lat = $request['lat'];
        $aduana->lon = $request['lon'];
        $aduana->link_maps = 'https://www.google.es/maps?q=' . $request['lat'] . ',' . $request['lon'];
        $aduana->user = $request['user'];
        $aduana->company = $request['company'];
        $aduana->save();

        return $aduana;
    }
    public function destroy($id)
    {
        Aduana::destroy($id);

        $existe = Aduana::find($id);
        if ($existe) {
            return 'No se elimino el Lugar de Carga';
        } else {
            return 'Se elimino el Lugar de Carga';
        };
    }
}
