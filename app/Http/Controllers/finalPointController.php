<?php

namespace App\Http\Controllers;

use App\Models\port;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class finalPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $final_points = DB::table('ports')->get();
        return $final_points;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
                'pais' => 'required|string|max:255',
                'provincia' => 'required|string|max:255',
            ]);

            $final_points = port::create($validated);

            return response()->json([
                'message' => 'Destino final creado con éxito',
                'data' => $final_points
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores si algo falla
            return response()->json([
                'message' => 'No se pudo crear el Destino final',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $final_points = port::find($id);
        return $final_points;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|max:255',
                'pais' => 'required|string|max:255',
                'provincia' => 'required|string|max:255',
                'sigla' => 'nullable|string|max:255',
            ]);
            $final_points = port::findOrFail($id);
            $final_points->update($validated);

            return response()->json([
                'message' => 'Destino final actualizado con éxito',
                'data' => $final_points
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el destino final',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        port::destroy($id);

        $existe = port::find($id);
        if($existe){
            return 'No se elimino el Lugar de Descarga';
        }else{
            return 'Se elimino el Lugar de Descarga';
        };
    }
}
