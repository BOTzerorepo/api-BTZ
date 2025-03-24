<?php

namespace App\Http\Controllers;

use App\Models\CustomerAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $customerAgents = CustomerAgent::all();
            return response()->json([
                'data' => $customerAgents,
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

    public function indexCompany($empresa)
    {
        $customerAgent = DB::table('customer_agents')->where('empresa', '=', $empresa)->get();
        return $customerAgent;
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
                'pais' => 'nullable|string|max:255',
                'provincia' => 'nullable|string|max:255',
                'mail' => 'nullable|email|max:255',
                'phone' => 'nullable|numeric|digits_between:7,12',
                'user' => 'nullable|string|max:255',
                'empresa' => 'nullable|string|max:255',
            ]);

            $customerAgent = CustomerAgent::create($validated);

            return response()->json([
                'message' => 'Despachante creado con éxito',
                'data' => $customerAgent
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores si algo falla
            return response()->json([
                'message' => 'No se pudo crear el Despachante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerAgent  $customerAgent
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerAgent = DB::table('customer_agents')->where('id', '=', $id)->get();
        return $customerAgent;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerAgent  $customerAgent
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerAgent $customerAgent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerAgent  $customerAgent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        try {
            $validated = $request->validate([
                'razon_social' => 'required|string|max:255',
                'tax_id' => 'required|numeric|digits_between:8,12',
                'pais' => 'nullable|string|max:255',
                'provincia' => 'nullable|string|max:255',
                'mail' => 'nullable|email|max:255',
                'phone' => 'nullable|numeric|digits_between:7,12',
                'user' => 'nullable|string|max:255',
                'empresa' => 'nullable|string|max:255',
            ]);
            $customerAgent = CustomerAgent::findOrFail($id);
            $customerAgent->update($validated);

            return response()->json([
                'message' => 'Despachante actualizado con éxito',
                'data' => $customerAgent
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el despachante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerAgent  $customerAgent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            CustomerAgent::destroy($id);
            $existe = CustomerAgent::find($id);
            if ($existe) {
                return response()->json([
                    'message' => 'No se eliminó el Despachante. Inténtalo de nuevo.',
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Despachante eliminado con éxito.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el Despachante.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
