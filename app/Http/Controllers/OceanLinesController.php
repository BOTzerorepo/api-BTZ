<?php

namespace App\Http\Controllers;

use App\Models\OceanLines;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OceanLinesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $oceanLines = OceanLines::orderBy('razon_social', 'ASC')->get();
            return response()->json([
                'success' => true,
                'message' => 'Oceans Lines obtenidos correctamente.',
                'data' => $oceanLines
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las Oceans Lines.',
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OceanLines  $oceanLines
     * @return \Illuminate\Http\Response
     */
    public function show(OceanLines $oceanLines)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OceanLines  $oceanLines
     * @return \Illuminate\Http\Response
     */
    public function edit(OceanLines $oceanLines)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OceanLines  $oceanLines
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OceanLines $oceanLines)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OceanLines  $oceanLines
     * @return \Illuminate\Http\Response
     */
    public function destroy(OceanLines $oceanLines)
    {
        //
    }
}
