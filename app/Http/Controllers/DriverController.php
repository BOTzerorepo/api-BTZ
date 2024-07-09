<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Driver;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drivers = DB::table('drivers')->get();       
        return $drivers;
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
        $driver = new Driver();
        $driver->nombre = $request['nombre'];
        $driver->foto= $request['foto'];
        $driver->documento = $request['documento'];
        $driver->vto_carnet = $request['vto_carnet'];
        $driver->WhatsApp = $request['WhatsApp'];
        $driver->mail = $request['mail'];
        $driver->user = $request['user'];
        $driver->empresa = $request['empresa'];
        $driver->transporte = $request['transporte'];
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
        $driver = Driver::findOrFail($id);
        $driver->nombre = $request['nombre'];
        $driver->foto= $request['foto'];
        $driver->documento = $request['documento'];
        $driver->vto_carnet = $request['vto_carnet'];
        $driver->WhatsApp = $request['WhatsApp'];
        $driver->mail = $request['mail'];
        $driver->user = $request['user'];
        $driver->empresa = $request['empresa'];
        $driver->transporte = $request['transporte'];
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
        Driver::destroy($id);

        $existe = Driver::find($id);
        if($existe){
            return 'No se elimino el Chofer';
        }else{
            return 'Se elimino el Chofer';
        };
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
