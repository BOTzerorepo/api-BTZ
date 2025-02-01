<?php

namespace App\Http\Controllers;

use App\Exports\transports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Driver;
use App\Models\Transport;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drivers = Driver::whereNull('deleted_at')->get();
        return $drivers;
    }
    public function indexTransport($idTransport)
    {
        // Convertir $idTransport en un array si no lo es (separado por comas)
        $idArray = explode(',', $idTransport);

        // Buscar los drivers cuyos transport_id coincidan con cualquiera de los IDs en el array
        $drivers = Driver::whereIn('transport_id', $idArray)->get();

        return $drivers;
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
                'nombre' => 'required|string|max:255',
                'foto' => 'nullable|string|max:255',
                'documento' => 'required|numeric|digits_between:8,12|unique:transports,CUIT',
                'vto_carnet' => 'nullable|date',
                'WhatsApp' => 'nullable|numeric|digits_between:7,12',
                'mail' => 'nullable|email|max:255',
                'user' => 'required|string|max:255',
                'empresa' => 'required|string|max:255',
                'transporte' => 'nullable|string|max:255',
                'status_chofer' => 'nullable|string|max:255',
                'place' => 'nullable|string|max:255',
                'Observaciones' => 'nullable|string|max:255',
                'customer_id' => 'nullable|numeric',
                'fletero_id' => 'nullable|numeric',
                'id_transport' => 'nullable|numeric',
            ]);
            // Verificación y asignación de transporte
            if ($request['transporte'] != null) {
                $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
                $idTransport = $transport->id;
                $transport = $request['transporte'];
            } elseif (isset($request['transporte'])) {
                $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
                $idTransport = $transport->id;
                $transport = $request['transporte'];
            } else {
                $qtr = Transport::where('id', '=', $request['id_transport'])->first();
                $transport = $qtr->razon_social;
                $idTransport = $request['id_transport'];
            }

            // Creación del conductor
            $driver = Driver::create([
                'nombre' => $request['nombre'],
                'foto' => $request['foto'],
                'documento' => $request['documento'],
                'vto_carnet' => $request['vto_carnet'],
                'WhatsApp' => $request['WhatsApp'],
                'mail' => $request['mail'],
                'user' => $request['user'],
                'empresa' => $request['empresa'],
                'transporte' => $transport,
                'fletero_id' => $request['id_fletero'],
                'transport_id' => $idTransport,
                'Observaciones' => $request['Observaciones']
            ]);

            return response()->json([
                'message' => 'Se cargó correctamente el Chofer ' . $request['nombre'],
                'data' => $driver,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el conductor.',
                'error' => $e->getMessage(),
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
        $driver = DB::table('drivers')->where('id', '=', $id)->get();
        return $driver;
    }

    public function showDriver($transporte)
    {
        $idTranport = DB::table('transports')->where('id', '=', $transporte)->get('razon_social');
        $id = $idTranport[0]->razon_social;

        /* Hay que recibir el id del Transporte */
        $drivers = DB::table('drivers')->whereNull('deleted_at')->where('transporte', '=', $id)->get();
        return $drivers;
    }

    /**
     * Show the form for editing the specified resource.
     *
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
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'foto' => 'nullable|string|max:255',
                'documento' => "required|numeric|digits_between:8,12|unique:transports,CUIT,$id",
                'vto_carnet' => 'nullable|date',
                'WhatsApp' => 'nullable|numeric|digits_between:7,12',
                'mail' => 'nullable|email|max:255',
                'user' => 'required|string|max:255',
                'empresa' => 'required|string|max:255',
                'transporte' => 'nullable|string|max:255',
                'status_chofer' => 'nullable|string|max:255',
                'place' => 'nullable|string|max:255',
                'Observaciones' => 'nullable|string|max:255',
                'customer_id' => 'nullable|numeric',
                'fletero_id' => 'nullable|numeric',
                'id_transport' => 'nullable|numeric',
            ]);
            // Verificación y asignación de transporte
            if ($request['transporte'] != null) {
                $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
                $idTransport = $transport->id;
                $transport = $request['transporte'];
            } elseif (isset($request['transporte'])) {
                $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
                $idTransport = $transport->id;
                $transport = $request['transporte'];
            } else {
                $qtr = Transport::where('id', '=', $request['id_transport'])->first();
                $transport = $qtr->razon_social;
                $idTransport = $request['id_transport'];
            }

            // Encontrar y actualizar el conductor
            $driver = Driver::findOrFail($id);
            $driver->update([
                'nombre' => $request['nombre'],
                'foto' => $request['foto'],
                'documento' => $request['documento'],
                'vto_carnet' => $request['vto_carnet'],
                'WhatsApp' => $request['WhatsApp'],
                'mail' => $request['mail'],
                'user' => $request['user'],
                'empresa' => $request['empresa'],
                'transporte' => $transport,
                'fletero_id' => $request['id_fletero'],
                'transport_id' => $idTransport,
                'Observaciones' => $request['Observaciones']
            ]);

            return response()->json([
                'message' => 'Conductor actualizado exitosamente.',
                'data' => $driver,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el conductor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function status(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);
        $driver->status_chofer = $request['status_chofer'];
        $driver->place = $request['place'];
        $driver->user = $request['user'];
        $driver->empresa = $request['empresa'];
        $driver->save();

        return $driver;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $driver = Driver::findOrFail($id);
        $driver->delete();  // Esto marcará el registro como eliminado (soft delete)

        return response()->json([
            'message' => 'Driver marcado como eliminado exitosamente.'
        ], 200);
    }

    public function issetDriver(Request $request)
    {
        $nombre = $request->query('nombre');
        $transporte = $request->query('transporte'); // Obtener el valor del transporte

        // Realiza las operaciones necesarias con los parámetros
        $driver = DB::table('drivers')
            ->leftJoin('transports', 'transports.razon_social', '=', 'drivers.transporte')
            ->select('drivers.id', 'drivers.nombre', 'transports.razon_social')
            ->where('drivers.nombre', '=', $nombre)
            ->where('transports.razon_social', '=', $transporte) // Filtrar también por transporte
            ->get();
        $count = $driver->count();

        return response()->json([
            'count' => $count,
            'detail' => $driver
        ]);
    }
}
