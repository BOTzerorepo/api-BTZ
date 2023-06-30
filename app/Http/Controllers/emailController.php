<?php

namespace App\Http\Controllers;

use App\Mail\avisoNewCarga;
use App\Mail\CamnioStatus;
use App\Mail\cargaAsignada;
use App\Mail\cargaAsignadaEditada;
use App\Mail\CargaConProblemas;
use App\Mail\IngresadoStacking;
use App\Mail\pruebaMail;
use App\Mail\transporteAsignado;
use App\Models\empresa;
use App\Models\particularSoftConfiguration;

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
            ->select('asign.*', 'carga.user as userC','transports.Direccion', 'transports.paut', 'transports.CUIT', 'transports.permiso', 'transports.vto_permiso', 'drivers.documento', 'trucks.model', 'trucks.model', 'trucks.year', 'trucks.chasis', 'trucks.poliza', 'trucks.vto_poliza', 'trailers.domain as semi_domain', 'trailers.poliza as semi_poliza', 'trailers.vto_poliza as semi_vto_poliza')
            ->join('transports', 'asign.transport', '=', 'transports.razon_social')
            ->join('drivers', 'drivers.nombre', '=', 'asign.driver')
            ->join('trucks', 'trucks.domain', '=', 'asign.truck')
            ->join('carga','asign.booking','=','carga.booking')
            ->join('trailers', 'trailers.domain', '=', 'asign.truck_semi')
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
            'company' => $dAsign->company
        ];


        

        $sbx = DB::table('variables')->select('sandbox')->get();
        if ($sbx[0]->sandbox == 0) {

            Mail::to($to)->bcc('inboxplataforma@botzero.ar')->send(new cargaAsignada($data, $date));
            
            $logapi = new logapi();
            $logapi->user = $dAsign->user;
            $logapi->detalle = 'AsignaUnidadCarga-User:' . $dAsign->user . '|Transporte:' . $dAsign->transport . '|Chofer:' . $dAsign->driver . '|Tractor:' . $dAsign->truck . '|Semi:' . $dAsign->truck_semi;
            $logapi->save();

            return 'ok';
        } else {
            Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new cargaAsignada($data, $date));

            $logapi = new logapi();
            $logapi->user = $dAsign->user;
            $logapi->detalle = '+ Sandbox + to: '. $to .'AsignaUnidadCarga-User:' . $dAsign->user . '|Transporte:' . $dAsign->transport . '|Chofer:' . $dAsign->driver . '|Tractor:' . $dAsign->truck . '|Semi:' . $dAsign->truck_semi;
            $logapi->save();
            
            return 'ok';
        }
    }
    public function transporteAsignado($id)
    {

        $date = Carbon::now('-03:00');
        $asign = DB::table('asign')
        ->select('asign.id', 'carga.user as userC','asign.cntr_number', 'asign.booking', 'asign.transport', 'asign.transport_agent', 'asign.user', 'asign.company', 'atas.tax_id', 'transports.pais')->join('transports', 'asign.transport', '=', 'transports.razon_social')
        ->join('atas', 'asign.transport_agent', '=', 'atas.razon_social')
        ->join('carga','asign.booking','=','carga.booking')
        ->where('asign.id', '=', $id)
        ->get();
        $dAsign = $asign[0];
        $to = DB::table('users')->select('email')->where('username', '=', $dAsign->userC)->get();

    
        
        // Cambiar para que este correo se para el Customer.
        
        $data = [

            'cntr_number' => $dAsign->cntr_number,
            'booking' => $dAsign->booking,
            'transport' => $dAsign->transport,
            'transport_agent' => $dAsign->transport_agent,
            'user' => $dAsign->user,
            'company' => $dAsign->company,
            'transport_bandera' => $dAsign->pais,
            'cuit_ata' => $dAsign->tax_id
        ];

        $logapi = new logapi();
        $logapi->user = $dAsign->user;
        $logapi->detalle = 'AsignaCarga';
        $logapi->save();

        $sbx = DB::table('variables')->select('sandbox')->get();
        
        if ($sbx[0]->sandbox == 0) {

            Mail::to($to)->bcc('inboxplataforma@botzero.ar')->send(new transporteAsignado($data, $date));

            $logapi = new logapi();
            $logapi->user = $dAsign->user;
            $logapi->detalle = 'to: '. $to .'AsignaUnidadTransporte-User:' . $dAsign->user . '|Transporte:' . $dAsign->transport .'| ATA:' . $dAsign->transport_agent .'| Bandera:' . $dAsign->pais .'| CUIT :' . $dAsign->tax_id ;
            $logapi->save();

            return 'ok';
            
        } else {

            Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new transporteAsignado($data, $date));
            
            $logapi =  new logapi();
            $logapi->user = $dAsign->user;
            $logapi->detalle = '+ Sandbox +to: '. $to .'AsignaUnidadTransporte-User:' . $dAsign->user . '|Transporte:' . $dAsign->transport .'| ATA:' . $dAsign->transport_agent .'| Bandera:' . $dAsign->pais .'| CUIT :' . $dAsign->tax_id ;
            $logapi->save();

            return 'ok';
        }
    }

    public function cambiaStatus($cntr, $empresa, $booking, $user, $tipo)
    {

        $logapi = new logapi();
        $logapi->user = $user;
        $logapi->detalle = 'Envio Mail_' . $tipo;
        $logapi->save();
        $date = Carbon::now('-03:00');


        if ($tipo == 'problema') {

            $qd = DB::table('status')->select('status')->where('cntr_number', '=', $cntr)->latest('id')->first();
            $description = $qd->status;
            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'date' => $date
            ];


            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $booking)->get();
            $empresa = $qempresa[0]->empresa;
            $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
            $mail = $qmail[0]->mail_logistic;
            $sbx = DB::table('variables')->select('sandbox')->get();

            if ($sbx[0]->sandbox == 0) {

                Mail::to($mail)->bcc('inboxplataforma@botzero.ar')->send(new CargaConProblemas($datos));

                return 'ok';

            } else {

                Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new CamnioStatus($datos));

                return 'ok';
            }
        } elseif ($tipo == 'stacking') {

            $qd = DB::table('status')->select('status', 'main_status')->where('cntr_number', '=', $cntr)->latest('id')->first();
            $description = $qd->status;
            $status = $qd->main_status;

            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'date' => $date
            ];

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $booking)->get();
            $empresa = $qempresa[0]->empresa;
            $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
            $mail = $qmail[0]->mail_logistic;
            $sbx = DB::table('variables')->select('sandbox')->get();
            if ($sbx[0]->sandbox == 0) {
                Mail::to($mail)->bcc('inboxplataforma@botzero.ar')->send(new IngresadoStacking($datos));
                return 'ok';
            } else {
                Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new CamnioStatus($datos));
                return 'ok';
            }
        } else {


            $qd = DB::table('status')->select('status', 'main_status')->where('cntr_number', '=', $cntr)->latest('id')->first();
            $description = $qd->status;
            $status = $qd->main_status;

            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'date' => $date,
                'status' => $status
            ];

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $booking)->get();
            $empresa = $qempresa[0]->empresa;

            $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
            $mail = $qmail[0]->mail_logistic;

            $sbx = DB::table('variables')->select('sandbox')->get();

            if ($sbx[0]->sandbox == 0) {

                Mail::to($mail)->bcc('inboxplataforma@botzero.ar')->send(new CamnioStatus($datos));

                $logApi = new logapi();
                $logApi->user = $user;
                $logApi->detalle = "envio email camnioStatus to: " . $mail;
                $logApi->save();
                return 'ok';
            } else {
                Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new CamnioStatus($datos));
                $logApi = new logapi();
                $logApi->user = $user;
                $logApi->detalle = "+ Sandbox + envio email camnioStatus to: " . $mail;
                $logApi->save();
                return 'ok';
            }
        }
    }

    public function avisoNuevaCarga($idCarga, $user)
    {

        $user = DB::table('users')->join('particular_soft_configurations', 'users.configCompany', '=', 'particular_soft_configurations.name')->where('users.username', '=', $user)->get();

        $toMailsEnviar = $user[0]->to_mail_trafico_Team;
        $ccMailsEnviar = $user[0]->cc_mail_trafico_Team;

        $qcarga = DB::table('carga')
            ->select(
                'carga.booking',
                'carga.trader',
                'carga.user',
                'carga.ref_customer',
                'carga.shipper',
                'carga.load_date',
                'carga.load_place',
                'carga.custom_place',
                'carga.custom_agent',
                'carga.oceans_line',
                'carga.vessel',
                'carga.voyage',
                'carga.cut_off_fis',
                'carga.unload_place',
                'carga.final_point',
                'carga.commodity',
                'carga.observation_customer',
                'cntr.retiro_place',
                'cntr.cntr_type'
            )
            ->join('cntr', 'carga.booking', '=', 'cntr.booking')->where('carga.id', '=', $idCarga)->get();
        $cantidad = $qcarga->count();
        $carga = $qcarga[0];
        $date = Carbon::now('-03:00');

        $datos = [

            'operacion' => $carga->ref_customer,
            'trader' => $carga->trader,
            'booking' => $carga->booking,
            'loadDate' => $carga->load_date,
            'depositoRetiro' => $carga->retiro_place,
            'shipper' => $carga->shipper,
            'loadPlace' => $carga->load_place,
            'customPlace' => $carga->custom_place,
            'customAgent' => $carga->custom_agent,
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
            'date' => $date


        ];

        $sbx = DB::table('variables')->select('sandbox')->get();

        if ($sbx[0]->sandbox == 0) {

            $mail = Mail::to(['ddicarlo@totaltradegroup.com', 'rquero@totaltradegroup.com', 'cs.auxiliar@totaltradegroup.com'])->cc(['gzarate@totaltradegroup.com', 'czelada@totaltradegroup.com', 'fzgaib@totaltradegroup.com'])->bcc('inboxplataforma@botzero.ar')->send(new avisoNewCarga($datos));
            $logApi = new logapi();
            $logApi->user = $user[0]->username;
            $logApi->detalle = "envio email to(['ddicarlo@totaltradegroup.com', 'rquero@totaltradegroup.com', 'cs.auxiliar@totaltradegroup.com'])->cc(['gzarate@totaltradegroup.com', 'czelada@totaltradegroup.com', 'fzgaib@totaltradegroup.com'])";
            $logApi->save();
            return 'ok';

        } else {

            $mail = Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar','equipodemo2@botzero.com.ar'])->cc(['equipodemo2@botzero.com.ar','copiaequipodemo5@botzero.com.ar','copiaequipodemo6@botzero.com.ar'])->bcc('inboxplataforma@botzero.ar')->send(new avisoNewCarga($datos));
            $logApi = new logapi();
            $logApi->user = $user[0]->username;
            $logApi->detalle = "envio email cto(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar','equipodemo2@botzero.com.ar'])->cc(['equipodemo2@botzero.com.ar','copiaequipodemo5@botzero.com.ar','copiaequipodemo6@botzero.com.ar'])";
            $logApi->save();
            return 'ok';

        }

        /*   return view('mails.avisoNewCarga')->with('datos',$datos); */
        /* $mail = Mail::to(['ddicarlo@totaltradegroup.com', 'rquero@totaltradegroup.com','cs.auxiliar@totaltradegroup.com'])->cc(['gzarate@totaltradegroup.com', 'czelada@totaltradegroup.com','fzgaib@totaltradegroup.com'])->bcc('traficottl@botzero.ar')->send(new avisoNewCarga($datos)); 
        return 'ok'; */
        /* Por ahora hay que setear a mano!
        Para futuros hay que ver la formad enviar de acuerdo a un seteo dentro de la configuracion. 
        Tiene que enviar todo en la misma cadena. 
        */
    }
}
