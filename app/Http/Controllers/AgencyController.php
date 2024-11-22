<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agencies = DB::table('agencies')->get();
        return $agencies;
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
                'razon_social' => 'required|string|max:255',
                'tax_id' => 'required|numeric|digits_between:8,12',
                'puerto' => 'required|string|max:255',
                'contact_phone' => 'required|numeric|digits_between:7,12',
                'contact_name' => 'required|string|max:255',
                'contact_mail' => 'required|email|max:255',
                'user' => 'required|string|max:255',
                'empresa' => 'required|string|max:255',
                'observation_gral' => 'nullable|string|max:255',
            ]);

            $agency = Agency::create($validated);
            return response()->json([
                'message' => 'Agencia creada con éxito',
                'data' => $agency
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores si algo falla
            return response()->json([
                'message' => 'No se pudo crear la agencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agency  $agency
     * @return \Illuminate\Http\Response
     */
    public function show(Agency $agency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Agency  $agency
     * @return \Illuminate\Http\Response
     */
    public function edit(Agency $agency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Agency  $agency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|max:255',
                'razon_social' => 'required|string|max:255',
                'tax_id' => 'required|numeric|digits_between:8,12',
                'puerto' => 'required|string|max:255',
                'contact_phone' => 'required|numeric|digits_between:7,12',
                'contact_name' => 'required|string|max:255',
                'contact_mail' => 'required|email|max:255',
                'user' => 'required|string|max:255',
                'empresa' => 'required|string|max:255',
                'observation_gral' => 'nullable|string|max:255',
            ]);
            $agency = Agency::findOrFail($id);
            $agency->update($validated);

            return response()->json([
                'message' => 'Agencia actualizada con éxito',
                'data' => $agency
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar la agencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Agency  $agency
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Agency::destroy($id);

        $existe = Agency::find($id);
        if ($existe) {
            return 'No se elimino la Agencia';
        } else {
            return 'Se elimino la Agencia';
        };
    }
}
