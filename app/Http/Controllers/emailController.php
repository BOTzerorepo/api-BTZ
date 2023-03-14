<?php

namespace App\Http\Controllers;

use App\Mail\CamnioStatus;
use App\Mail\cargaAsignada;
use App\Mail\cargaAsignadaEditada;
use App\Mail\CargaConProblemas;
use App\Mail\IngresadoStacking;
use App\Mail\transporteAsignado;
use App\Models\empresa;
use App\Models\logapi;
use App\Models\statu;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class emailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function cargaAsignada($id){

        $date = Carbon::now('-03:00');
        $asign = DB::table('asign')
        ->select('asign.*','transporte.Direccion','transporte.paut','transporte.CUIT','transporte.permiso','transporte.vto_permiso','choferes.documento', 'trucks.model','trucks.model','trucks.year','trucks.chasis','trucks.poliza','trucks.vto_poliza','trailers.domain as semi_domain')
        ->join('transporte','asign.transport','=','transporte.razon_social')
        ->join('choferes','choferes.nombre','=','asign.driver')
        ->join('trucks','trucks.domain','=','asign.truck')
        ->join('trailers','trailers.domain','=','asign.truck_semi')

        ->where('asign.id', '=', $id)->get();
        $dAsign = $asign[0];
        $to = DB::table('users')->select('email')->where('username', '=', $dAsign->user)->get();
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

            'fletero_razon_social'=> $dAsign->fletero_razon_social,
            'fletero_domicilio'=> $dAsign->fletero_domicilio,
            'fletero_cuit'=> $dAsign->fletero_cuit,
            'fletero_paut'=> $dAsign->fletero_paut,
            'fletero_permiso'=> $dAsign->fletero_permiso,
            'fletero_vto_permiso'=> $dAsign->fletero_vto_permiso,

            'driver' => $dAsign->driver,
            'documento' => $dAsign->documento,

            'truck' => $dAsign->truck,
            'truck_modelo' => $dAsign->model,
            'truck_year' => $dAsign->year,
            'truck_chasis' => $dAsign->chasis,
            'truck_poliza' => $dAsign->poliza,
            'truck_vto_poliza' => $dAsign->vto_poliza,

            'truck_semi' => $dAsign->truck_semi,

            'cntr_number' => $dAsign->cntr_number,
            'booking' => $dAsign->booking,
            
            'user' => $dAsign->user,
            'company' => $dAsign->company
        ];


        $logapi = new logapi();
        $logapi->user = $dAsign->user;
        $logapi->detalle = 'AsignaUnidadCarga-User:'.$dAsign->user.'|Transporte:'. $dAsign->transport.'|Chofer:'.$dAsign->driver.'|Tractor:'.$dAsign->truck.'|Semi:'.$dAsign->truck_semi;
        $logapi->save();

        Mail::to($to)->send(new cargaAsignada($data, $date));
        return 'ok';

    }
    public function transporteAsignado($id){

        $date = Carbon::now('-03:00');
        $asign = DB::table('asign')->select('asign.id','asign.cntr_number','asign.booking','asign.transport','asign.transport_agent','asign.user','asign.company','ata.tax_id','transporte.pais')->join('transporte','asign.transport','=','transporte.razon_social')->join('ata','asign.transport_agent','=','ata.razon_social')->where('asign.id', '=', $id)->get();
        $dAsign = $asign[0];
        
        $to = DB::table('users')->select('email')->where('username', '=', $dAsign->user)->get();
      
        $data = [
           
            'cntr_number' => $dAsign->cntr_number,
            'booking' => $dAsign->booking,
            'transport' => $dAsign->transport,
            'transport_agent' => $dAsign->transport_agent,
            'user' => $dAsign->user,
            'company' => $dAsign->company,
            'transport_bandera' => $dAsign->pais,
            'cuit_ata'=> $dAsign->tax_id
        ];

        $logapi = new logapi();
        $logapi->user = $dAsign->user;
        $logapi->detalle = 'AsignaCarga';
        $logapi->save();

        Mail::to($to)->send(new transporteAsignado($data, $date));
        return 'ok';

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

            Mail::to($mail)->send(new CargaConProblemas($datos));
            return 'ok';

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

            Mail::to($mail)->send(new IngresadoStacking($datos));
            return 'ok';
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

            Mail::to($mail)->send(new CamnioStatus($datos));
            return 'ok';
        }
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
    public function show($id)
    {
        //
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
}
