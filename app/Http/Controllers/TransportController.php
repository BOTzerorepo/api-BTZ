<?php

namespace App\Http\Controllers;

use App\Mail\nuevoTranporte;
use App\Mail\transporteAsignado;
use App\Mail\asignarUnidadTransporte;
use App\Mail\cargaAsignada;
use App\Models\Transport;
use App\Models\cntr;
use App\Models\User;
use App\Models\asign;
use App\Models\Driver;
use App\Models\notification;
use App\Models\logapi;
use App\Models\statu;
use App\Models\Carga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

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
        $transportes = Transport::whereNull('deleted_at')->where('customer_id', '=', $id_customer)->get();

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
        $transport = Transport::whereNull('deleted_at')->where('id', '=', $id)->get();

        return $transport;
    }
    public function showRazonSocial($razonSocial)
    {
        $transport = Transport::whereNull('deleted_at')->where('razon_social', '=', $razonSocial)->first();

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
    public function addFletero(Request $request, $transportId)
    {
        $validated = $request->validate([
            'fletero_id' => 'required|exists:fleteros,id', // Verifica que el fletero exista
        ]);

        $transport = Transport::findOrFail($transportId);
        $fleteroId = $validated['fletero_id'];

        // Agrega el fletero al transporte
        $transport->fleteros()->attach($fleteroId);

        return response()->json([
            'message' => 'Fletero asociado al transporte exitosamente.',
            'data' => $transport->load('fleteros') // Carga los fleteros asociados
        ], 200);
    }

    public function transporteAsignado(Request $request, $cntrId)
    {

        DB::beginTransaction();
        try {
            // Validación de datos
            $request->validate([
                'transport' => 'required',
                'transport_agent' => 'required',
                'user' => 'required',
                'company' => 'required',
            ]);
            //Obtener el cntr
            $cntr = cntr::whereNull('deleted_at')->where('id_cntr', '=', $cntrId)->first();

            //Obtener el asign
            $asign = asign::whereNull('deleted_at')->where('cntr_number', '=', $cntr->cntr_number)->first();

            //Actualizar el asign
            $asign->transport = $request->input('transport');
            $asign->transport_agent = $request->input('transport_agent');
            $asign->user = $request->input('user');
            $asign->company = $request->input('company');
            $asign->save();

            //Enviar mail
            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');
            $mailsTrafico = DB::table('particular_soft_configurations')->first();
            $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
            $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);

            //DATOS PARA ENVIAR MAIL
            $date = Carbon::now('-03:00');
            $asignMail = DB::table('asign')
                ->select('asign.id', 'carga.*', 'cntr.cntr_type', 'carga.user as userC', 'asign.cntr_number', 'asign.booking', 'asign.transport', 'asign.transport_agent', 'asign.user', 'asign.company', 'atas.tax_id', 'transports.pais', 'cntr.confirmacion')
                ->join('transports', 'asign.transport', '=', 'transports.razon_social')
                ->leftJoin('atas', 'asign.transport_agent', '=', 'atas.razon_social')
                ->join('carga', 'asign.booking', '=', 'carga.booking')
                ->join('cntr', 'asign.cntr_number', '=', 'cntr.cntr_number')
                ->where('asign.id', '=', $asign->id)
                ->first();
            $datos = [
                'cntr_number' => $asignMail->cntr_number,
                'cntr_type' => $asignMail->cntr_type,
                'booking' => $asignMail->booking,
                'confirmacion' => $asignMail->confirmacion,
                'transport' => $asignMail->transport,
                'transport_agent' => $asignMail->transport_agent,
                'user' => $asignMail->user,
                'company' => $asignMail->company,
                'transport_bandera' => $asignMail->pais,
                'cuit_ata' => $asignMail->tax_id,
                'ref_customer' => $asignMail->ref_customer,
                'type' => $asignMail->type,
                'trader' => $asignMail->trader,
            ];
            $transporteMail = DB::table('asign')
                ->select('users.email')
                ->join('transports', 'asign.transport', '=', 'transports.razon_social')
                ->join('users', 'transports.id', '=', 'users.transport_id')
                ->where('asign.id', '=', $asign->id)
                ->first();
            if ($sbx[0]->sandbox == 0) {
                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new transporteAsignado($datos, $date));
                // Enviar solo al correo del transporte
                if ($transporteMail) {
                    Mail::to($transporteMail->email)
                        ->send(new transporteAsignado($datos, $date));
                }
            } else {
                Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo3@botzero.com.ar'])
                    ->cc(['equipodemo2@botzero.com.ar', 'copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])
                    ->bcc($inboxEmail)->send(new transporteAsignado($datos, $date));
            }

            
            DB::commit();
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $asign->booking)->first();
            return response()->json([
                'message' => 'Transporte asignado correctamente al contenedor: ' .  $cntr->cntr_number,
                'message_type' => 'success',
                'cargaId' => $carga->id
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = [];
            foreach ($e->errors() as $field => $errorMessages) {
                foreach ($errorMessages as $errorMessage) {
                    $errors[] = $errorMessage;
                }
            }
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $asign->booking)->first();
            return response()->json([
                'message' => 'Datos ingresados incorrectamente',
                'message_type' => 'danger',
                'error' => $errors,
                'cargaId' => $carga->id
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $asign->booking)->first();
            return response()->json(['error' => $errorMessage, 'message_type' => 'danger', 'cargaId' => $carga->id], 500);
        }
    }

    //El usuario transporte asigna una unidad de su transporte de manera provisoria
    public function asignarUnidadTransporte(Request $request, $cntrId)
    {
        DB::beginTransaction();
        try {
            // Validación de datos
            $request->validate([
                'driver' => 'required',
                'truck' => 'required',
                'truck_semi' => 'required',
                'user' => 'required',
                'empresa' => 'required',
                'crt' => 'nullable',
                'fletero_razon_social' => 'nullable',
                'fletero_cuit' => 'nullable',
                'fletero_domicilio' => 'nullable',
                'fletero_paut' => 'nullable',
                'fletero_permiso' => 'nullable',
                'fletero_vto_permiso' => 'nullable',
                'crt' => 'nullable',
            ]);
            //Obtener el cntr
            $cntr = cntr::whereNull('deleted_at')->where('id_cntr', '=', $cntrId)->first();

            //Obtener el asign
            $asign = asign::whereNull('deleted_at')->where('cntr_number', '=', $cntr->cntr_number)->first();

            //Actualizar el asign
            $asign->driver = $request->input('driver');
            $asign->truck = $request->input('truck');
            $asign->truck_semi = $request->input('truck_semi');
            $asign->crt = $request->input('crt');
            $asign->fletero_razon_social = $request->input('fletero_razon_social');
            $asign->fletero_cuit = $request->input('fletero_cuit');
            $asign->fletero_domicilio = $request->input('fletero_domicilio');
            $asign->fletero_paut = $request->input('fletero_paut');
            $asign->fletero_permiso = $request->input('fletero_permiso');
            $asign->fletero_vto_permiso = $request->input('fletero_vto_permiso');
            $asign->user = $request->input('user');
            $asign->company = $request->input('empresa');
            $asign->save();

            //Enviar mail
            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');
            $mailsTrafico = DB::table('particular_soft_configurations')->first();
            $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
            $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);

            //DATOS PARA ENVIAR MAIL
            $date = Carbon::now('-03:00');
            $asignMail = DB::table('asign')
                ->select('asign.*', 'cntr.cntr_type', 'carga.trader', 'carga.ref_customer', 'carga.type', 'carga.user as userC', 'transports.Direccion', 'transports.paut', 'transports.CUIT', 'transports.permiso', 'transports.vto_permiso', 'drivers.documento', 'trucks.model', 'trucks.model', 'trucks.year', 'trucks.chasis', 'trucks.poliza', 'trucks.vto_poliza', 'trailers.domain as semi_domain', 'trailers.poliza as semi_poliza', 'trailers.vto_poliza as semi_vto_poliza', 'cntr.confirmacion')
                ->join('transports', 'asign.transport', '=', 'transports.razon_social')
                ->join('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->join('trucks', 'trucks.domain', '=', 'asign.truck')
                ->join('carga', 'asign.booking', '=', 'carga.booking')
                ->join('trailers', 'trailers.domain', '=', 'asign.truck_semi')
                ->join('cntr', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->where('asign.id', '=', $asign->id)
                ->first();
            $datos = [
                // Datos CRT
                'transport' => $asignMail->transport,
                'direccion' => $asignMail->Direccion,
                'paut' => $asignMail->paut,
                'cuit' => $asignMail->CUIT,
                'permiso_int' => $asignMail->permiso,
                'vto_permiso_int' => $asignMail->vto_permiso,
                'crt' => $asignMail->crt,
                // Datos para MIC
                'fletero_razon_social' => $asignMail->fletero_razon_social,
                'fletero_domicilio' => $asignMail->fletero_domicilio,
                'fletero_cuit' => $asignMail->fletero_cuit,
                'fletero_paut' => $asignMail->fletero_paut,
                'fletero_permiso' => $asignMail->fletero_permiso,
                'fletero_vto_permiso' => $asignMail->fletero_vto_permiso,
                'confirmacion' => $asignMail->confirmacion,
                'driver' => $asignMail->driver,
                'documento' => $asignMail->documento,
                'truck' => $asignMail->truck,
                'truck_modelo' => $asignMail->model,
                'truck_year' => $asignMail->year,
                'truck_chasis' => $asignMail->chasis,
                'truck_poliza' => $asignMail->poliza,
                'truck_vto_poliza' => $asignMail->vto_poliza,
                'truck_semi' => $asignMail->truck_semi,
                'truck_semi_poliza' => $asignMail->semi_poliza,
                'truck_semi_vto_poliza' => $asignMail->semi_vto_poliza,
                'cntr_number' => $asignMail->cntr_number,
                'booking' => $asignMail->booking,
                'user' => $asignMail->user,
                'company' => $asignMail->company,
                'ref_customer' => $asignMail->ref_customer,
                'type' => $asignMail->type,
                'trader' => $asignMail->trader,
                'cntr_type' => $asignMail->cntr_type,
                'booking' => $asignMail->booking,
            ];

            if ($sbx[0]->sandbox == 0) {
                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new asignarUnidadTransporte($datos, $date));
            } else {
                Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo3@botzero.com.ar'])
                    ->cc(['equipodemo2@botzero.com.ar', 'copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])
                    ->bcc($inboxEmail)->send(new asignarUnidadTransporte($datos, $date));
            }

            DB::commit();
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $asign->booking)->first();
            return response()->json([
                'message' => 'Unidad asignada correctamente al contenedor: ' .  $cntr->cntr_number,
                'message_type' => 'success',
                'cargaId' => $carga->id
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = [];
            foreach ($e->errors() as $field => $errorMessages) {
                foreach ($errorMessages as $errorMessage) {
                    $errors[] = $errorMessage;
                }
            }
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $asign->booking)->first();
            return response()->json([
                'message' => 'Datos ingresados incorrectamente',
                'message_type' => 'danger',
                'error' => $errors,
                'cargaId' => $carga->id
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $asign->booking)->first();
            return response()->json(['error' => $errorMessage, 'message_type' => 'danger', 'cargaId' => $carga->id], 500);
        }
    }

    //El usuario Traffic confirma una unidad del tranporte
    public function confirmarUnidad(Request $request, $cntrId)
    {
        DB::beginTransaction();
        try {
            // Validación de datos
            $request->validate([
                'driver' => 'required',
                'truck' => 'required',
                'truck_semi' => 'required',
                'user' => 'required',
                'empresa' => 'required',
                'crt' => 'nullable',
                'fletero_razon_social' => 'nullable',
                'fletero_cuit' => 'nullable',
                'fletero_domicilio' => 'nullable',
                'fletero_paut' => 'nullable',
                'fletero_permiso' => 'nullable',
                'fletero_vto_permiso' => 'nullable',
                'crt' => 'nullable',
            ]);

            //Obtener el cntr
            $cntr = cntr::whereNull('deleted_at')->where('id_cntr', '=', $cntrId)->first();

            //Obtener el asign
            $asign = asign::whereNull('deleted_at')->where('cntr_number', '=', $cntr->cntr_number)->first();

            //Actualizar el asign
            $asign->driver = $request->input('driver');
            $asign->truck = $request->input('truck');
            $asign->truck_semi = $request->input('truck_semi');
            $asign->crt = $request->input('crt');
            $asign->fletero_razon_social = $request->input('fletero_razon_social');
            $asign->fletero_cuit = $request->input('fletero_cuit');
            $asign->fletero_domicilio = $request->input('fletero_domicilio');
            $asign->fletero_paut = $request->input('fletero_paut');
            $asign->fletero_permiso = $request->input('fletero_permiso');
            $asign->fletero_vto_permiso = $request->input('fletero_vto_permiso');
            $asign->user = $request->input('user');
            $asign->company = $request->input('empresa');
            $asign->save();

            //Enviar mail
            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');
            $mailsTrafico = DB::table('particular_soft_configurations')->first();
            $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
            $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);

            //DATOS PARA ENVIAR MAIL
            $date = Carbon::now('-03:00');
            $asignMail = DB::table('asign')
                ->select('asign.*', 'cntr.cntr_type', 'carga.trader', 'carga.ref_customer', 'carga.type', 'carga.user as userC', 'transports.Direccion', 'transports.paut', 'transports.CUIT', 'transports.permiso', 'transports.vto_permiso', 'drivers.documento', 'trucks.model', 'trucks.model', 'trucks.year', 'trucks.chasis', 'trucks.poliza', 'trucks.vto_poliza', 'trailers.domain as semi_domain', 'trailers.poliza as semi_poliza', 'trailers.vto_poliza as semi_vto_poliza', 'cntr.confirmacion')
                ->join('transports', 'asign.transport', '=', 'transports.razon_social')
                ->join('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->join('trucks', 'trucks.domain', '=', 'asign.truck')
                ->join('carga', 'asign.booking', '=', 'carga.booking')
                ->join('trailers', 'trailers.domain', '=', 'asign.truck_semi')
                ->join('cntr', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->where('asign.id', '=', $asign->id)
                ->first();
            $datos = [
                // Datos CRT
                'transport' => $asignMail->transport,
                'direccion' => $asignMail->Direccion,
                'paut' => $asignMail->paut,
                'cuit' => $asignMail->CUIT,
                'permiso_int' => $asignMail->permiso,
                'vto_permiso_int' => $asignMail->vto_permiso,
                'crt' => $asignMail->crt,
                // Datos para MIC
                'fletero_razon_social' => $asignMail->fletero_razon_social,
                'fletero_domicilio' => $asignMail->fletero_domicilio,
                'fletero_cuit' => $asignMail->fletero_cuit,
                'fletero_paut' => $asignMail->fletero_paut,
                'fletero_permiso' => $asignMail->fletero_permiso,
                'fletero_vto_permiso' => $asignMail->fletero_vto_permiso,
                'confirmacion' => $asignMail->confirmacion,
                'driver' => $asignMail->driver,
                'documento' => $asignMail->documento,
                'truck' => $asignMail->truck,
                'truck_modelo' => $asignMail->model,
                'truck_year' => $asignMail->year,
                'truck_chasis' => $asignMail->chasis,
                'truck_poliza' => $asignMail->poliza,
                'truck_vto_poliza' => $asignMail->vto_poliza,
                'truck_semi' => $asignMail->truck_semi,
                'truck_semi_poliza' => $asignMail->semi_poliza,
                'truck_semi_vto_poliza' => $asignMail->semi_vto_poliza,
                'cntr_number' => $asignMail->cntr_number,
                'booking' => $asignMail->booking,
                'user' => $asignMail->user,
                'company' => $asignMail->company,
                'ref_customer' => $asignMail->ref_customer,
                'type' => $asignMail->type,
                'trader' => $asignMail->trader,
                'cntr_type' => $asignMail->cntr_type,
                'booking' => $asignMail->booking,
            ];

            if ($sbx[0]->sandbox == 0) {
                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaAsignada($datos, $date));
                $logapi = new logapi();
                $logapi->user = $asignMail->user;
                $logapi->detalle = 'AsignaUnidadCarga-User:' . $asignMail->user . '|Transporte:' . $asignMail->transport . '|Chofer:' . $asignMail->driver . '|Tractor:' . $asignMail->truck . '|Semi:' . $asignMail->truck_semi;
                $logapi->save();

                $status = new statu();
                $status->status = 'Asignado Chofer:' . $asignMail->driver . '|Tractor:' . $asignMail->truck . '|Semi:' . $asignMail->truck_semi;
                $status->avisado = 1;
                $status->main_status = 'ASIGNADA';
                $status->cntr_number = $asignMail->cntr_number;
                $status->user_status = $asignMail->user;
                $status->save();
            } else {
                Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo3@botzero.com.ar'])
                    ->cc(['equipodemo2@botzero.com.ar', 'copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])
                    ->bcc($inboxEmail)->send(new cargaAsignada($datos, $date));
                
                    $logapi = new logapi();
                    $logapi->user = $asignMail->user;
                    $logapi->detalle = '+ Sandbox + to: ' . implode(',', $toEmails) . ' AsignaUnidadCarga-User:' . $asignMail->user . ' |Transporte:' . $asignMail->transport . '|Chofer:' . $asignMail->driver . '|Tractor:' . $asignMail->truck . '|Semi:' . $asignMail->truck_semi;
                    $logapi->save();
                    
                    $status = new statu();
                    $status->status = 'Asignado Chofer:' . $asignMail->driver . '|Tractor:' . $asignMail->truck . '|Semi:' . $asignMail->truck_semi;
                    $status->avisado = 1;
                    $status->main_status = 'ASIGNADA';
                    $status->cntr_number = $asignMail->cntr_number;
                    $status->user_status = $asignMail->user;
                    $status->save();
            }

            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $asign->booking)->first();

            // ESTADO DEL DRIVE EN OCUPADO
            $driver = Driver::whereNull('deleted_at')->where('nombre', '=', $asign->driver)->first();
            $driver->status_chofer = 'ocupado';
            $driver->place = $carga->unload_place;
            $driver->save();

            //ACTUALIZO STATUS CNTR
            $cntr->main_status= 'ASIGNADA';
            $cntr->status_cntr= 'ASIGNADA';
            $cntr->save();

            //CREO UNA NOTIFIACION
            $notification = new notification();
            $notification->title = ' ';
            $notification->description = ' ';
            $notification->user_to = ' ';
            $notification->status = 'No Leido';
            $notification->sta_carga = 'ASIGNADA';
            $notification->user_create = $asign->user;
            $notification->company_create = $asign->company;
            $notification->cntr_number = $asign->cntr_number;
            $notification->booking = $asign->booking;
            $notification->save();

            // Obtener todos los registros de la tabla 'cntr' con el mismo booking
            $cntrs = cntr::where('booking', $asign->booking)->get();
            $cntr_status = [];
            $equal = true;
            foreach ($cntrs as $cntr) {
                $cntr_status[] = $cntr->main_status;
                // Si el primer status es diferente al actual, no son iguales
                if ($cntr_status[0] != $cntr->main_status) {
                    $equal = false;
                    break;
                }
            }
            // Si todos los statuses son iguales, actualiza la tabla 'carga'
            if ($equal && count($cntr_status) > 0) {
                carga::where('booking', $asign->booking)->update(['status' => $cntr_status[0]]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Unidad asignada correctamente al contenedor: ' .  $cntr->cntr_number,
                'message_type' => 'success',
                'cargaId' => $carga->id
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = [];
            foreach ($e->errors() as $field => $errorMessages) {
                foreach ($errorMessages as $errorMessage) {
                    $errors[] = $errorMessage;
                }
            }
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $asign->booking)->first();
            return response()->json([
                'message' => 'Datos ingresados incorrectamente',
                'message_type' => 'danger',
                'error' => $errors,
                'cargaId' => $carga->id
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $asign->booking)->first();
            return response()->json(['error' => $errorMessage, 'message_type' => 'danger', 'cargaId' => $carga->id], 500);
        }
    }


    public function transportesUsuario($id)
    {
        // Obtener el usuario por nombre de usuario
        $user = User::where('username', $id)->first();

        // Verificar si el usuario tiene transportes asociados
        if ($user && $user->transport_id) {
            // Convertir los IDs en un array
            $transportIds = explode(',', $user->transport_id);

            // Buscar todos los transportes que coinciden con los IDs
            $transportes = Transport::whereIn('id', $transportIds)->get();

            return $transportes;
        }

        // Retornar un array vacío si no hay transportes asociados
        return [];
    }


    public function transportesAsignEditar(Request $request, $cntrId)
    {
        DB::beginTransaction();
        try {
            // Validación de datos
            $request->validate([
                'transporte_id' => 'required',
            ]);
            //Obtener el cntr
            $cntr = cntr::whereNull('deleted_at')->where('id_cntr', '=', $cntrId)->first();

            //Obtener el asign
            $asign = asign::whereNull('deleted_at')->where('cntr_number', '=', $cntr->cntr_number)->first();

            // Buscar todos los transportes que coinciden con los IDs
            $transport = Transport::whereNull('deleted_at')->where('id', '=', $request->input('transporte_id'))->first();
            //Actualizar el asign
            $asign->transport = $transport->razon_social;
            $asign->user = $request->input('user');
            $asign->company = $request->input('empresa');
            $asign->save();

            DB::commit();
            return response()->json([
                'message' => 'Transporte modificado correctamente al contenedor: ' .  $cntr->cntr_number,
                'message_type' => 'success',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = [];
            foreach ($e->errors() as $field => $errorMessages) {
                foreach ($errorMessages as $errorMessage) {
                    $errors[] = $errorMessage;
                }
            }

            return response()->json([
                'message' => 'Datos ingresados incorrectamente',
                'message_type' => 'danger',
                'error' => $errors
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            return response()->json(['error' => $errorMessage, 'message_type' => 'danger'], 500);
        }
    }
}
