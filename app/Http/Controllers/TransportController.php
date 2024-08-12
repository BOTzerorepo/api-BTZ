<?php

namespace App\Http\Controllers;

use App\Mail\nuevoTranporte;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TransportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transportes = Transport::whereNull('deleted_at')->get();

        return $transportes;
    }
    public function indexTransporteCustomer($id_customer)
    {
        $transportes = Transport::whereNull('deleted_at')->where('customer_id','=',$id_customer)->get();

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
        $transporte = Transport::withTrashed()
                          ->where('cuit', $request['cuit'])
                          ->orWhere('razon_social', $request['razon_social'])
                          ->first();

        if ($transporte) {
            // Si el registro está eliminado, lo restauramos
            if ($transporte->trashed()) {
                $transporte->restore();
            }
        } else {
            // Crear un nuevo registro
            $transporte = new Transport();
        }
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
        $transporte->satelital = $request['satelital'];
        $transporte->observation = $request['observation'];
        $transporte->save();
        Mail::to('pablorio@botzero.ar')->send(new nuevoTranporte($transporte));
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
        $transport = Transport::whereNull('deleted_at')->where('id','=',$id)->get();

        return $transport;
    }
    public function showRazonSocial($razonSocial)
    {
        $transport = Transport::whereNull('deleted_at')->where('razon_social','=',$razonSocial)->first();

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
        $transporte->satelital = $request['satelital'];
        $transporte->observation = $request['observation'];
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
       // Buscar el registro del transporte por su ID
        $transport = Transport::find($id);

        // Verificar si el transporte existe
        if ($transport) {
            // Eliminar el registro (soft delete)
            $transport->delete();

            // Verificar si el registro aún existe (incluso como soft deleted)
            if ($transport->trashed()) {
                return response()->json(['message' => 'El transporte se eliminó correctamente.'], 200);
            } else {
                return response()->json(['message' => 'No se pudo eliminar el transporte.'], 500);
            }
        } else {
            // Respuesta si el transporte no se encuentra
            return response()->json(['message' => 'El transporte no existe.'], 404);
        }
    }
    public function issetTrasnsport($cuit)
    {

        $transport = Transport::whereNull('deleted_at')->where('CUIT', '=', $cuit)->get();
        $count = $transport->count();


        return response()->json([
            'count' => $count,
            'detail' => $transport
        ]);
    }

    public function issetTransportRazon($razonSocial)
    {

        $transport = Transport::whereNull('deleted_at')->where('razon_social', '=', $razonSocial)->get();
        $count = $transport->count();


        return response()->json([
            'count' => $count,
            'detail' => $transport
        ]);
    }
}
