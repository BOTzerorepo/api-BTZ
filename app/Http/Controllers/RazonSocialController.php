<?php

namespace App\Http\Controllers;

use App\Models\razonSocial;
use Illuminate\Http\Request;

class RazonSocialController extends Controller
{
    public function indexTransport($id){

        $razonesSociales = razonSocial::all()->where('transport_id',$id);

        return response()->json([
            'message' => 'Detalles de los fleteros asociados al transporte.',
            'data' => $razonesSociales // Devuelve la colección de fleteros
        ], 200);
        
    }
    public function index()
    {
        try {
            $razonesSociales = razonSocial::all();
            return response()->json([
                'data' => $razonesSociales,
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

    public function store(Request $request){


        $validated = $request->validate([
            'razon_social' => 'required|string',
            'cuit' => 'required|string',
            'direccion' => 'required|string',
            'provincia' => 'required|string',
            'pais' => 'required|string',
            'paut' => 'required|string',
            'permiso' => 'required|string',
            'vto_permiso' => 'required|date',
        ]);

        $razonSocial = new razonSocial($validated);
        $razonSocial->transport_id = $request['transport_id'];
        $razonSocial->observation = $request['observation'];
        $razonSocial->save();

        return response()->json($razonSocial, 200);

        

    }
    public function update(Request $request, $id){

        $validated = $request->validate([
            'razon_social' => 'required|string|max:255',
            'cuit' => 'required|string',
            'direccion' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
            'pais' => 'required|string|max:255',
            'paut' => 'nullable|string|max:255',
            'permiso' => 'nullable|string|max:255',
            'vto_permiso' => 'nullable|date',
        ]);

        $razonSocial = razonSocial::findOrFail($id);
        $razonSocial->observation = $request['observation'];
        $razonSocial->update($validated);

        return response()->json([
            'message' => 'Razón Social actualizado exitosamente.',
            'data' => $razonSocial
        ], 200);


    }
    public function show($id){
        
        $razonSocial = razonSocial::findOrFail($id);
        return response()->json($razonSocial, 200);

    }
    public function destroy($id)
    {
        $fletero = razonSocial::findOrFail($id);
        $fletero->delete();

        return response()->json([
            'message' => 'Razón Social marcada como eliminada exitosamente.'
        ], 200);
    }

}
