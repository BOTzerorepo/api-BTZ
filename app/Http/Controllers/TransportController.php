<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transportes = DB::table('transports')->get();

        return $transportes;
    }
    public function indexTransporteCustomer($id_customer)
    {
        $transportes = DB::table('transports')->where('customer_id','=',$id_customer)->get();

        return $transportes;
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
        $transporte = new Transport();
        $transporte->razon_social = $request['razon_social'];
        $transporte->CUIT = $request['CUIT'];
        $transporte->Direccion = $request['direccion'];
        $transporte->pais = $request['pais'];
        $transporte->Provincia = $request['provincia'];
        $transporte->paut = $request['paut'];
        $transporte->permiso = $request['permiso'];
        $transporte->vto_permiso = $request['vto_permiso'];
        $transporte->contacto_logistica_nombre = $request['contacto_logistica_nombre'];
        $transporte->contacto_logistica_celular = $request['contacto_logistica_celular'];
        $transporte->contacto_logistica_mail = $request['contacto_logistica_mail'];
        $transporte->contacto_admin_nombre = $request['contacto_admin_nombre'];
        $transporte->contacto_admin_celular = $request['contacto_admin_celular'];
        $transporte->contacto_admin_mail = $request['contacto_admin_mail'];
        $transporte->user = $request['user'];
        $transporte->empresa = $request['empresa'];
        
        $transporte->save();

        return $transporte;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transport  $transport
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transport = DB::table('transports')->where('id','=',$id)->get();

        return $transport;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transport  $transport
     * @return \Illuminate\Http\Response
     */
    public function edit(Transport $transport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transport  $transport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $transporte = Transport::findOrFail($id);

        $transporte->razon_social = $request['razon_social'];
        $transporte->CUIT = $request['CUIT'];
        $transporte->Direccion = $request['direccion'];
        $transporte->pais = $request['pais'];
        $transporte->Provincia = $request['provincia'];
        $transporte->paut = $request['paut'];
        $transporte->permiso = $request['permiso'];
        $transporte->vto_permiso = $request['vto_permiso'];
        $transporte->contacto_logistica_nombre = $request['contacto_logistica_nombre'];
        $transporte->contacto_logistica_celular = $request['contacto_logistica_celular'];
        $transporte->contacto_logistica_mail = $request['contacto_logistica_mail'];
        $transporte->contacto_admin_nombre = $request['contacto_admin_nombre'];
        $transporte->contacto_admin_celular = $request['contacto_admin_celular'];
        $transporte->contacto_admin_mail = $request['contacto_admin_mail'];
        $transporte->user = $request['user'];
        $transporte->empresa = $request['empresa'];
        
        $transporte->save();

        return $transporte;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transport  $transport
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Transport::destroy($id);

        $existe = Transport::find($id);
        if($existe){
            return 'No se elimino el Transporte';
        }else{
            return 'Se elimino el Transporte';
        };
    }
}
