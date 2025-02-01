<?php

namespace App\Http\Controllers;

use App\Mail\avisoNewCarga;
use App\Mail\CamnioStatus;
use App\Mail\cargaAsignada;
use App\Mail\cargaAsignadaEditada;
use App\Mail\CargaConProblemas;
use App\Mail\cargaTerminada;
use App\Mail\IngresadoStacking;
use App\Mail\pruebaMail;
use App\Mail\transporteAsignado;
use App\Models\empresa;
use App\Models\particularSoftConfiguration;
use App\Models\Carga;
use App\Models\logapi;
use App\Models\statu;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class emailController extends Controller
{

    public function cargaAsignada($id)
    {

        $date = Carbon::now('-03:00');
        $asign = DB::table('asign')
            ->select('asign.*', 'cntr.cntr_type', 'carga.trader', 'carga.ref_customer', 'carga.type', 'carga.user as userC', 'transports.Direccion', 'transports.paut', 'transports.CUIT', 'transports.permiso', 'transports.vto_permiso', 'drivers.documento', 'trucks.model', 'trucks.model', 'trucks.year', 'trucks.chasis', 'trucks.poliza', 'trucks.vto_poliza', 'trailers.domain as semi_domain', 'trailers.poliza as semi_poliza', 'trailers.vto_poliza as semi_vto_poliza', 'cntr.confirmacion')
            ->join('transports', 'asign.transport', '=', 'transports.razon_social')
            ->join('drivers', 'drivers.nombre', '=', 'asign.driver')
            ->join('trucks', 'trucks.domain', '=', 'asign.truck')
            ->join('carga', 'asign.booking', '=', 'carga.booking')
            ->join('trailers', 'trailers.domain', '=', 'asign.truck_semi')
            ->join('cntr', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->where('asign.id', '=', $id)->get();

        $dAsign = $asign[0];
        $to = DB::table('users')->select('email')->where('username', '=', $dAsign->userC)->get();

        $data = [
            // Datos CRT
            'transport' => $dAsign->transport,
            'direccion' => $dAsign->Direccion,
            'paut' => $dAsign->paut,
            'cuit' => $dAsign->CUIT,
            'permiso_int' => $dAsign->permiso,
            'vto_permiso_int' => $dAsign->vto_permiso,
            'crt' => $dAsign->crt,
            // Datos para MIC
            'fletero_razon_social' => $dAsign->fletero_razon_social,
            'fletero_domicilio' => $dAsign->fletero_domicilio,
            'fletero_cuit' => $dAsign->fletero_cuit,
            'fletero_paut' => $dAsign->fletero_paut,
            'fletero_permiso' => $dAsign->fletero_permiso,
            'fletero_vto_permiso' => $dAsign->fletero_vto_permiso,
            'confirmacion' => $dAsign->confirmacion,
            'driver' => $dAsign->driver,
            'documento' => $dAsign->documento,
            'truck' => $dAsign->truck,
            'truck_modelo' => $dAsign->model,
            'truck_year' => $dAsign->year,
            'truck_chasis' => $dAsign->chasis,
            'truck_poliza' => $dAsign->poliza,
            'truck_vto_poliza' => $dAsign->vto_poliza,
            'truck_semi' => $dAsign->truck_semi,
            'truck_semi_poliza' => $dAsign->semi_poliza,
            'truck_semi_vto_poliza' => $dAsign->semi_vto_poliza,
            'cntr_number' => $dAsign->cntr_number,
            'booking' => $dAsign->booking,
            'user' => $dAsign->user,
            'company' => $dAsign->company,
            'ref_customer' => $dAsign->ref_customer,
            'type' => $dAsign->type,
            'trader' => $dAsign->trader,
            'cntr_type' => $dAsign->cntr_type,
            'booking' => $dAsign->booking,
        ];

        //Enviar mail
        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');
        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
        $carga = Carga::whereNull('deleted_at')->where('booking', '=', $dAsign->booking)->first();

        if ($sbx[0]->sandbox == 0) {

            $customer = DB::table('users')
                ->where('username', '=', $carga->user)
                ->value('email');
            $toEmails = array_merge([$customer], (array) $toEmails);

            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaAsignada($data, $date));

            $logapi = new logapi();
            $logapi->user = $dAsign->user;
            $logapi->detalle = 'AsignaUnidadCarga-User:' . $dAsign->user . '|Transporte:' . $dAsign->transport . '|Chofer:' . $dAsign->driver . '|Tractor:' . $dAsign->truck . '|Semi:' . $dAsign->truck_semi;
            $logapi->save();

            $status = new statu();
            $status->status = 'Asignado Chofer:' . $dAsign->driver . '|Tractor:' . $dAsign->truck . '|Semi:' . $dAsign->truck_semi;
            $status->avisado = 1;
            $status->main_status = 'ASIGNADA';
            $status->cntr_number = $dAsign->cntr_number;
            $status->user_status = $dAsign->user;
            $status->save();

            return 'ok';
        } else {

            Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo3@botzero.com.ar'])->cc(['copia@botzero.com.ar'])->bcc($inboxEmail)->send(new cargaAsignada($data, $date));

            $logapi = new logapi();
            $logapi->user = $dAsign->user;
            $logapi->detalle = '+ Sandbox + to: ' . $to . 'AsignaUnidadCarga-User:' . $dAsign->user . ' |Transporte:' . $dAsign->transport . '|Chofer:' . $dAsign->driver . '|Tractor:' . $dAsign->truck . '|Semi:' . $dAsign->truck_semi;
            $logapi->save();

            $status = new statu();
            $status->status = 'Asignado Chofer:' . $dAsign->driver . '|Tractor:' . $dAsign->truck . '|Semi:' . $dAsign->truck_semi;
            $status->avisado = 1;
            $status->main_status = 'ASIGNADA';
            $status->cntr_number = $dAsign->cntr_number;
            $status->user_status = $dAsign->user;
            $status->save();

            return 'ok';
        }
    }
    public function transporteAsignado($id)
    {

        $date = Carbon::now('-03:00');

        try {
            $asign = DB::table('asign')
                ->select('asign.id', 'carga.*', 'cntr.cntr_type', 'carga.user as userC', 'asign.cntr_number', 'asign.booking', 'asign.transport', 'asign.transport_agent', 'asign.user', 'asign.company', 'atas.tax_id', 'transports.pais', 'cntr.confirmacion')
                ->join('transports', 'asign.transport', '=', 'transports.razon_social')
                ->leftJoin('atas', 'asign.transport_agent', '=', 'atas.razon_social')
                ->join('carga', 'asign.booking', '=', 'carga.booking')
                ->join('cntr', 'asign.cntr_number', '=', 'cntr.cntr_number')
                ->where('asign.id', '=', $id)
                ->get();

            if ($asign->count() === 0) {

                return 'Assignment not found'; // Handle the case where no assignment is found
            }

            $asign = $asign[0]; // Retrieve the first assignment

            // Retrieve recipient's email
            $to = DB::table('users')->select('email')->where('username', '=', $asign->userC)->first();

            // Data to be sent in the email
            $data = [
                'cntr_number' => $asign->cntr_number,
                'cntr_type' => $asign->cntr_type,
                'booking' => $asign->booking,
                'confirmacion' => $asign->confirmacion,
                'transport' => $asign->transport,
                'transport_agent' => $asign->transport_agent,
                'user' => $asign->user,
                'company' => $asign->company,
                'transport_bandera' => $asign->pais,
                'cuit_ata' => $asign->tax_id,
                'ref_customer' => $asign->ref_customer,
                'type' => $asign->type,
                'trader' => $asign->trader,
            ];

            // Log API action
            $logapi = new logapi();
            $logapi->user = $asign->user;
            $logapi->detalle = 'AsignaCarga';
            $logapi->save();

            // Retrieve sandbox status
            $sbx = DB::table('variables')->select('sandbox')->first();
            $inboxEmail = env('INBOX_EMAIL');
            // Determine the recipient and log message based on sandbox status
            $recipient = $to ? $to->email : 'copia@botzero.com.ar';
            $logMessage = '+ Sandbox +' . ($sbx->sandbox == 0 ? '' : 'to: ' . $recipient) . 'AsignaUnidadTransporte-User:' . $asign->user . '|Transporte:' . $asign->transport . '| ATA:' . $asign->transport_agent . '| Bandera:' . $asign->pais . '| CUIT :' . $asign->tax_id;
            if ($sbx->sandbox == 0) {
                // Send email
                Mail::to($to)->cc(['gzarate@totaltradegroup.com', 'lgonzalez@totaltradegroup.com'])->bcc($inboxEmail)->send(new transporteAsignado($data, $date));
            } elseif ($sbx->sandbox == 2) {

                Mail::to($to)->cc(['abel.mazzitelli@gmail.com'])->bcc($inboxEmail)->send(new transporteAsignado($data, $date));
            } else {

                Mail::to($to)->cc(['copia@botzero.com.ar'])->bcc($inboxEmail)->send(new transporteAsignado($data, $date));
            }

            // Log API action again with updated log message
            $logapi = new logapi();
            $logapi->user = $asign->user;
            $logapi->detalle = $logMessage;
            $logapi->save();

            return 'ok'; // Return success message
        } catch (\Exception $e) {
            // Handle and log the error
            $errorMessage = 'An error occurred: ' . $e->getMessage();
            // Log the error here using your preferred logging mechanism
            return $errorMessage; // Return error message
        }
    }

    public function cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath)
    {

        $logapi = new logapi();
        $logapi->user = $user;
        $logapi->detalle = 'Envio Mail_' . $tipo;
        $logapi->save();
        $date = Carbon::now('-03:00');

        //Enviar mail
        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');
        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
        $carga = Carga::whereNull('deleted_at')->where('booking', '=', $booking)->first();

        if ($tipo == 'problema') {

            $qd = DB::table('status')->select('status.id', 'status.status', 'cntr.cntr_type', 'carga.trader', 'carga.type', 'carga.ref_customer', 'cntr.confirmacion', 'asign.transport', 'asign.transport_agent', 'asign.truck', 'asign.truck_semi', 'asign.driver', 'drivers.documento')
                ->join('cntr', 'cntr.cntr_number', '=', 'status.cntr_number')
                ->join('carga', 'carga.booking', '=', 'cntr.booking')
                ->leftJoin('asign', 'asign.cntr_number', '=', 'status.cntr_number')
                ->leftJoin('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->where('status.cntr_number', '=', $cntr)->latest('id')->first();
            $description = $qd->status;
            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'confirmacion' => $qd->confirmacion,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'date' => $date,
                'status' => 'con Problema',
                'cntr_type' => $qd->cntr_type,
                'trader' => $qd->trader,
                'type' => $qd->type,
                'ref_customer' => $qd->ref_customer,
                'transport' => $qd->transport,
                'transport_agent' => $qd->transport_agent,
                'driver' => $qd->driver,
                'truck' => $qd->truck,
                'truck_semi' => $qd->truck_semi,
                'documento' => $qd->documento,
            ];

            if ($sbx[0]->sandbox == 0) {
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer], (array) $toEmails);

                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new CargaConProblemas($datos, $statusArchivoPath));
                return 'ok';
            } else {

                Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo2@botzero.com.ar'])->cc(['copia@botzero.com.ar'])->bcc($inboxEmail)
                    ->send(new CargaConProblemas($datos, $statusArchivoPath));
                return 'ok';
            }
        } elseif ($tipo == 'stacking') {

            $qd = DB::table('status')->select('status.main_status', 'status.id', 'status.status', 'cntr.cntr_type', 'carga.trader', 'carga.type', 'carga.ref_customer', 'cntr.confirmacion', 'asign.transport', 'asign.transport_agent', 'asign.truck', 'asign.truck_semi', 'asign.driver', 'drivers.documento')
                ->join('cntr', 'cntr.cntr_number', '=', 'status.cntr_number')
                ->join('carga', 'carga.booking', '=', 'cntr.booking')
                ->leftJoin('asign', 'asign.cntr_number', '=', 'status.cntr_number')
                ->leftJoin('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->where('status.cntr_number', '=', $cntr)->latest('status.id')->first();
            $description = $qd->status;
            $status = $qd->main_status;

            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'confirmacion' => $qd->confirmacion,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'date' => $date,
                'cntr_type' => $qd->cntr_type,
                'trader' => $qd->trader,
                'type' => $qd->type,
                'ref_customer' => $qd->ref_customer,
                'transport' => $qd->transport,
                'transport_agent' => $qd->transport_agent,
                'driver' => $qd->driver,
                'truck' => $qd->truck,
                'truck_semi' => $qd->truck_semi,
                'documento' => $qd->documento,
            ];

            if ($sbx[0]->sandbox == 0) {
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer], (array) $toEmails);
                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)
                    ->send(new IngresadoStacking($datos, $statusArchivoPath));
                return 'ok';
            } else {
                Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo2@botzero.com.ar'])->cc(['copia@botzero.com.ar'])->bcc($inboxEmail)
                    ->send(new IngresadoStacking($datos, $statusArchivoPath));
                return 'ok';
            }
        } elseif ($tipo == 'terminada') {

            $qd = DB::table('status')->select('status.id', 'status.status', 'cntr.cntr_type', 'carga.trader', 'carga.type', 'carga.ref_customer', 'cntr.confirmacion', 'asign.transport', 'asign.transport_agent', 'asign.truck', 'asign.truck_semi', 'asign.driver', 'drivers.documento')
                ->join('cntr', 'cntr.cntr_number', '=', 'status.cntr_number')
                ->join('carga', 'carga.booking', '=', 'cntr.booking')
                ->leftJoin('asign', 'asign.cntr_number', '=', 'status.cntr_number')
                ->leftJoin('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->where('status.cntr_number', '=', $cntr)->latest('id')->first();
            $description = $qd->status;
            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'date' => $date,
                'status' => 'con Problema',
                'cntr_type' => $qd->cntr_type,
                'confirmacion' => $qd->confirmacion,
                'trader' => $qd->trader,
                'type' => $qd->type,
                'ref_customer' => $qd->ref_customer,
                'transport' => $qd->transport,
                'transport_agent' => $qd->transport_agent,
                'driver' => $qd->driver,
                'truck' => $qd->truck,
                'truck_semi' => $qd->truck_semi,
                'documento' => $qd->documento,
            ];

            if ($sbx[0]->sandbox == 0) {
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer], (array) $toEmails);
                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)
                    ->send(new cargaTerminada($datos, $statusArchivoPath));
                return 'ok';
            } else {
                Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo2@botzero.com.ar'])->cc(['copia@botzero.com.ar'])->bcc($inboxEmail)
                    ->send(new cargaTerminada($datos, $statusArchivoPath));
                return 'ok';
            }
        } else {

            $qd = DB::table('status')->select('status.main_status', 'status.id', 'status.status', 'cntr.cntr_type', 'carga.trader', 'carga.type', 'carga.ref_customer', 'cntr.confirmacion', 'asign.transport', 'asign.transport_agent', 'asign.truck', 'asign.truck_semi', 'asign.driver', 'drivers.documento')
                ->join('cntr', 'cntr.cntr_number', '=', 'status.cntr_number')
                ->join('carga', 'carga.booking', '=', 'cntr.booking')
                ->leftJoin('asign', 'asign.cntr_number', '=', 'status.cntr_number')
                ->leftJoin('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->where('status.cntr_number', '=', $cntr)->latest('status.id')->first();
            $description = $qd->status;
            $status = $qd->main_status;

            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'confirmacion' => $qd->confirmacion,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'date' => $date,
                'status' => $status,
                'cntr_type' => $qd->cntr_type,
                'trader' => $qd->trader,
                'type' => $qd->type,
                'ref_customer' => $qd->ref_customer,
                'transport' => $qd->transport,
                'transport_agent' => $qd->transport_agent,
                'driver' => $qd->driver,
                'truck' => $qd->truck,
                'truck_semi' => $qd->truck_semi,
                'documento' => $qd->documento,
            ];

            if ($sbx[0]->sandbox == 0) {
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer], (array) $toEmails);

                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new CamnioStatus($datos, $statusArchivoPath));

                $logApi = new logapi();
                $logApi->user = $user;
                $logApi->detalle = "envio email camnioStatus to: " . $toEmails;
                $logApi->save();
                return 'ok';
            } else {
                Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo2@botzero.com.ar'])->cc(['copia@botzero.com.ar'])->bcc($inboxEmail)
                    ->send(new CamnioStatus($datos, $statusArchivoPath));
                $logApi = new logapi();
                $logApi->user = $user;
                $logApi->detalle = "+ Sandbox + envio email camnioStatus to: ['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo2@botzero.com.ar']";
                $logApi->save();
                return 'ok';
            }
        }
    }

    public function avisoNuevaCarga($idCarga, $user)
    {

        $user = DB::table('users')->join('particular_soft_configurations', 'users.configCompany', '=', 'particular_soft_configurations.name')->where('users.username', '=', $user)->get();

        $qcarga = DB::table('carga')
            ->select(
                'carga.id',
                'carga.booking',
                'carga.trader',
                'carga.importador',
                'carga.user',
                'carga.ref_customer',
                'carga.shipper',
                'carga.load_date',
                'carga.load_place',
                'carga.custom_place',
                'carga.custom_agent',
                'carga.custom_place_impo',
                'carga.custom_agent_impo',
                'carga.oceans_line',
                'carga.vessel',
                'carga.voyage',
                'carga.cut_off_fis',
                'carga.unload_place',
                'carga.final_point',
                'carga.commodity',
                'carga.observation_customer',
                'cntr.retiro_place',
                'cntr.cntr_type',
                'carga.type',
                'carga.senasa',
                'carga.senasa_string',
                'carga.tara',
                'carga.tara_string',
                'carga.bl_hbl',
            )
            ->join('cntr', 'carga.booking', '=', 'cntr.booking')->where('carga.id', '=', $idCarga)->get();
        $cantidad = $qcarga->count();
        $carga = $qcarga[0];
        $date = Carbon::now('-03:00');

        $fecha = Carbon::parse($carga->load_date);
        //$loadDate = $fecha->format('d/m/Y');
        $loadDate = $fecha->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');

        $datos = [
            'id' => $carga->id,
            'operacion' => $carga->ref_customer,
            'trader' => $carga->trader,
            'importador' => $carga->importador,
            'booking' => $carga->booking,
            'loadDate' => $loadDate,
            'depositoRetiro' => $carga->retiro_place,
            'shipper' => $carga->shipper,
            'loadPlace' => $carga->load_place,
            'customPlace' => $carga->custom_place,
            'customPlaceImpo' => $carga->custom_place_impo,
            'customAgent' => $carga->custom_agent,
            'customAgentImpo' => $carga->custom_agent_impo,
            'armador' => $carga->oceans_line,
            'vessel' => $carga->vessel,
            'voyage' => $carga->voyage,
            'cutOffFisico' => $carga->cut_off_fis,
            'loadPort' => $carga->unload_place,
            'finalPoint' => $carga->final_point,
            'commodity' => $carga->commodity,
            'obeservaciones' => $carga->observation_customer,
            'cantidad' => $cantidad,
            'cntr_type' => $carga->cntr_type,
            'user' => $user[0]->username,
            'date' => $date,
            'type' => $carga->type,
            'bl_hbl' => $carga->bl_hbl,
            'senasa' => $carga->senasa,
            'senasa_string' => $carga->senasa_string,
            'tara' => $carga->tara,
            'tara_string' => $carga->tara_string,
        ];

        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');

        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);

        if ($sbx[0]->sandbox == 0) {
            $customer = DB::table('users')
                ->where('username', '=', $carga->user)
                ->value('email');
            $toEmails = array_merge([$customer], (array) $toEmails);

            //Se envia el email al equipo de trafico y al customer que genero la carga. Copia a la gzarate
            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new avisoNewCarga($datos));
            $logApi = new logapi();
            $logApi->user = $user[0]->username;
            $logApi->detalle = "envio email to(Traffic y customer carga)->cc(['gzarate@totaltradegroup.com'])";
            $logApi->save();
            return 'ok';
        } elseif ($sbx[0]->sandbox == 2) {

            Mail::to(['customer@qa.botzero.com.ar', 'abel.mazzitelli@gmail.com'])->cc(['copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])->bcc($inboxEmail)->send(new avisoNewCarga($datos));
            $logApi = new logapi();
            $logApi->user = $user[0]->username;
            $logApi->detalle = "envio email to(['customer@qa.botzero.com.ar', 'abel.mazzitelli@gmail.com'])->cc(['copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])";
            $logApi->save();
            return 'ok';
        } else {

            Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo2@botzero.com.ar'])->cc(['equipodemo2@botzero.com.ar', 'copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])->bcc($inboxEmail)->send(new avisoNewCarga($datos));
            $logApi = new logapi();
            $logApi->user = $user[0]->username;
            $logApi->detalle = "envio email to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar','equipodemo2@botzero.com.ar'])->cc(['equipodemo2@botzero.com.ar','copiaequipodemo5@botzero.com.ar','copiaequipodemo6@botzero.com.ar'])";
            $logApi->save();
            return 'ok';
        }

        /*   return view('mails.avisoNewCarga')->with('datos',$datos); */
        /* $mail = Mail::to(['ddicarlo@totaltradegroup.com', 'rquero@totaltradegroup.com','cs.auxiliar@totaltradegroup.com'])->cc(['gzarate@totaltradegroup.com', 'fzgaib@totaltradegroup.com'])->bcc('traficottl@botzero.ar')->send(new avisoNewCarga($datos)); 
        return 'ok'; */
        /* Por ahora hay que setear a mano!
        Para futuros hay que ver la formad enviar de acuerdo a un seteo dentro de la configuracion. 
        Tiene que enviar todo en la misma cadena. 
        */
    }
}
