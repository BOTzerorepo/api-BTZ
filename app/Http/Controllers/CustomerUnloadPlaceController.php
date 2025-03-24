<?php

namespace App\Http\Controllers;

use App\Models\CustomerUnloadPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerUnloadPlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $customerUnloadPlaces = CustomerUnloadPlace::all();
    
            return response()->json([
                'success' => true,
                'message' => 'Lugares de descarga obtenidos correctamente.',
                'data' => $customerUnloadPlaces
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los lugares de descarga.',
                'error' => $e->getMessage()
            ], 500);
        }   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'km_from_town' => 'nullable|string',
                'remarks' => 'nullable|string|max:255',
                'latitud' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
                'longitud' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
                'link_maps' => 'nullable|string|max:255',
                'user' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
            ]);

            $customerUnloadPlace = CustomerUnloadPlace::create($validated);

            return response()->json([
                'message' => 'Lugar de descarga creada con éxito',
                'data' => $customerUnloadPlace
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores si algo falla
            return response()->json([
                'message' => 'No se pudo crear el Lugar de descarga',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerUnloadPlace  $customerUnloadPlace
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerUnloadPlace = DB::table('customer_unload_places')->where('id', '=', $id)->get();
        return $customerUnloadPlace;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerUnloadPlace  $customerUnloadPlace
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerUnloadPlace $customerUnloadPlace)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerUnloadPlace  $customerUnloadPlace
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'km_from_town' => 'nullable|string',
                'remarks' => 'nullable|string|max:255',
                'latitud' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
                'longitud' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
                'link_maps' => 'nullable|string|max:255',
                'user' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
            ]);
            $customerUnloadPlace = CustomerUnloadPlace::findOrFail($id);
            $customerUnloadPlace->update($validated);

            return response()->json([
                'message' => 'Lugar de descarga actualizado con éxito',
                'data' => $customerUnloadPlace
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el Lugar de descarga',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerUnloadPlace  $customerUnloadPlace
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            CustomerUnloadPlace::destroy($id);

            $existe = CustomerUnloadPlace::find($id);
            if ($existe) {
                return response()->json([
                    'message' => 'No se eliminó el Lugar de Descarga. Inténtalo de nuevo.',
                ], 400); 
            } else {
                return response()->json([
                    'message' => 'Lugar de Descarga eliminado con éxito.',
                ], 200); 
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el Lugar de Descarga.',
                'error' => $e->getMessage(),
            ], 500); 
        }
    }
}
