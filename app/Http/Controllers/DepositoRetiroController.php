<?php

namespace App\Http\Controllers;

use App\Models\DepositoRetiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositoRetiroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $depositoRetiros = DepositoRetiro::orderBy('title', 'ASC')->get();
            return response()->json([
                'data' => $depositoRetiros,
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
                'title' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'km_from_town' => 'required|numeric',
                'latitud' => 'required|regex:/^-?\d{1,3}\.\d+$/',
                'longitud' => 'required|regex:/^-?\d{1,3}\.\d+$/',
                'link_maps' => 'required|string|max:255',
                'user' => 'required|string|max:255',
                'empresa' => 'required|string|max:255',
            ]);

            $depositoRetiro = DepositoRetiro::create($validated);
            return response()->json([
                'message' => 'Deposito creado con éxito',
                'data' => $depositoRetiro
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores si algo falla
            return response()->json([
                'message' => 'No se pudo crear el Deposito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function show(DepositoRetiro $depositoRetiro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function edit(DepositoRetiro $depositoRetiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'km_from_town' => 'required|numeric',
                'latitud' => 'required|regex:/^-?\d{1,3}\.\d+$/',
                'longitud' => 'required|regex:/^-?\d{1,3}\.\d+$/',
                'link_maps' => 'required|string|max:255',
                'user' => 'required|string|max:255',
                'empresa' => 'required|string|max:255',
            ]);
            $depositoRetiro = DepositoRetiro::findOrFail($id);
            $depositoRetiro->update($validated);

            return response()->json([
                'message' => 'Deposito actualizado con éxito',
                'data' => $depositoRetiro
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el Deposito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DepositoRetiro::destroy($id);

        $existe = DepositoRetiro::find($id);
        if ($existe) {
            return 'No se elimino la Agencia';
        } else {
            return 'Se elimino la Agencia';
        };
    }
}
