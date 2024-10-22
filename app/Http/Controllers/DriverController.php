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
        $drivers = Driver::all();       
        return $drivers;
    }
    public function indexTransport($idTransport)
    {
        // Convertir $idTransport en un array si no lo es
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
        if($request['transporte'] != null){
            $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
            $idTranport = $transport->id;
            $transport = $request['transporte'];

        } elseif (isset($request['transporte'])) {

            $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
            $idTranport = $transport->id;
            $transport = $request['transporte'];

        }else{

            $qtr = Transport::where('id', '=', $request['id_transport'])->first();
            $transport = $qtr->razon_social;
            $idTranport = $request['id_transport'];
        }
        

        $driver = new Driver();
        $driver->nombre = $request['nombre'];
        $driver->foto= $request['foto'];
        $driver->documento = $request['documento'];
        $driver->vto_carnet = $request['vto_carnet'];
        $driver->WhatsApp = $request['WhatsApp'];
        $driver->mail = $request['mail'];
        $driver->user = $request['user'];
        $driver->empresa = $request['empresa'];
        $driver->transporte = $transport;
        $driver->fletero_id = $request['id_fletero'];
        $driver->transport_id = $idTranport;
        $driver->Observaciones = $request['Observaciones'];
        $driver->save();

        return $driver;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $driver = DB::table('drivers')->where('id','=',$id)->get();
        return $driver;
    }

    public function showDriver($transporte)
    {
        $idTranport = DB::table('transports')->where('id','=',$transporte)->get('razon_social');
        $id = $idTranport[0]->razon_social;

        /* Hay que recibir el id del Transporte */
        $drivers = DB::table('drivers')->where('transporte','=',$id)->get(); 
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

        if ($request['transporte'] != null) {
            $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
            $idTranport = $transport->id;
            $transport = $request['transporte'];
        } elseif (isset($request['transporte'])) {

            $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
            $idTranport = $transport->id;
            $transport = $request['transporte'];
        } else {

            $qtr = Transport::where('id', '=', $request['id_transport'])->first();
            $transport = $qtr->razon_social;
            $idTranport = $request['id_transport'];
        }

        $driver = Driver::findOrFail($id);
        $driver->nombre = $request['nombre'];
        $driver->foto= $request['foto'];
        $driver->documento = $request['documento'];
        $driver->vto_carnet = $request['vto_carnet'];
        $driver->WhatsApp = $request['WhatsApp'];
        $driver->mail = $request['mail'];
        $driver->user = $request['user'];
        $driver->empresa = $request['empresa'];
        $driver->transporte = $transport;
        $driver->fletero_id = $request['id_fletero'];
        $driver->transport_id = $idTranport;
        $driver->Observaciones = $request['Observaciones'];
        $driver->save();

        return $driver;
    }

    public function status(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);
        $driver->status_chofer = $request['status_chofer'];
        $driver->place= $request['place'];
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
