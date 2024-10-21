<?php

namespace App\Http\Controllers;

use App\Mail\nuevoTranporte;
use App\Mail\transporteAsignado;
use App\Models\Transport;
use App\Models\cntr;
use App\Models\User;
use App\Models\asign;
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

            //Actualizar status cntr ASIGNADA
            $cntr->main_status = 'ASIGNADA';
            $cntr->status_cntr = 'ASIGNADA';
            $cntr->save();

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
