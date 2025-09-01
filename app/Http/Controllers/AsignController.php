<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\asign;
use App\Models\Transport;
use App\Models\Carga;
use App\Models\Driver;

class AsignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($cntrNumber)
    {
        try {
            $asign = asign::whereNull('deleted_at')->where('cntr_number', '=', $cntrNumber)->first();

            return response()->json([
                'success' => true,
                'message' => 'Tipos de estados obtenidos correctamente.',
                'data' => $asign
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de estados.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *php artisan make:controller
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function imgAsignRazonSocial($id)
    {
        try {
            $asignImg = DB::table('asign')
                ->join('razon_social', 'asign.sub_empresa', '=', 'razon_social.title')
                ->where('asign.id', '=', $id)
                ->select('razon_social.img as img')
                ->first();
            return response()->json([
                'data' => $asignImg,
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

    public function editAsignacion($cntrNumber, Request $request)
    {
        try {
            $validated = $request->validate([
                'booking' => 'nullable|string',
                'transport' => 'nullable|string',
                'transport_agent' => 'nullable|string',
                'driver' => 'nullable|string',
                'truck' => 'nullable|string',
                'truck_semi' => 'nullable|string',
            ]);

            $transport = Transport::whereNull('deleted_at')->where('id', '=', $request->input('transport'))->first();
            $asign = asign::whereNull('deleted_at')->where('cntr_number', '=', $cntrNumber)->first();
            $choferNombre = $asign->driver;

            //Actualizar el asign
            $asign->driver = $request->input('driver');
            $asign->truck = $request->input('truck');
            $asign->truck_semi = $request->input('truck_semi');
            $asign->transport_agent = $request->input('transport_agent');
            $asign->transport = $transport->razon_social;
            $asign->save();

            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $request->input('booking'))->first();
            
            $chofer = Driver::whereNull('deleted_at')->where('nombre', '=', $choferNombre)->first();
            $chofer->status_chofer = 'libre';
            $chofer->place = 'INDEFINIDO';
            $chofer->save();

            $driver = Driver::whereNull('deleted_at')->where('nombre', '=', $request->input('driver'))->first();
            $driver->status_chofer = 'ocupado';
            $driver->place = $carga->unload_place;
            $driver->save();

            return response()->json([
                'carga' => $carga,
                'asign' => $asign,
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
}
