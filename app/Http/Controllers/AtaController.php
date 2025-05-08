<?php

namespace App\Http\Controllers;

use App\Models\Ata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtaController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $atas = ATA::all();
            return response()->json([
                'data' => $atas,
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
                'razon_social' => 'required|string|max:255',
                'tax_id' => 'required|numeric|digits_between:8,12',
                'provincia' => 'nullable|string|max:255',
                'phone' => 'nullable|numeric|digits_between:7,12',
                'pais' => 'nullable|string',
                'mail' => 'nullable|email|max:255',
                'user' => 'nullable|string|max:255',
                'empresa' => 'nullable|string|max:255',
            ]);

            $ata = Ata::create($validated);
            return response()->json([
                'message' => 'Lugar de descarga creada con éxito',
                'data' => $ata
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
     * @param  \App\Models\Ata  $ata
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ata = DB::table('atas')->where('id', '=', $id)->get();
        return $ata;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ata  $ata
     * @return \Illuminate\Http\Response
     */
    public function edit(Ata $ata)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ata  $ata
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'razon_social' => 'required|string|max:255',
                'tax_id' => 'required|numeric|digits_between:8,12',
                'provincia' => 'nullable|string|max:255',
                'phone' => 'nullable|numeric|digits_between:7,12',
                'pais' => 'nullable|string',
                'mail' => 'nullable|email|max:255',
                'user' => 'nullable|string|max:255',
                'empresa' => 'nullable|string|max:255',
            ]);
            $ata = Ata::findOrFail($id);
            $ata->update($validated);

            return response()->json([
                'message' => 'Ata actualizado con éxito',
                'data' => $ata
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el ata',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ata  $ata
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Ata::destroy($id);
            $existe = Ata::find($id);
            if ($existe) {
                return response()->json([
                    'message' => 'No se eliminó el Ata. Inténtalo de nuevo.',
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Ata eliminado con éxito.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el Ata.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
