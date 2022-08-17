<?php

namespace App\Http\Controllers;

use App\Mail\CamnioStatus;
use App\Mail\cargaAsignada;
use App\Mail\cargaAsignadaEditada;
use App\Mail\CargaConProblemas;
use App\Mail\IngresadoStacking;
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
        $asign = DB::table('asign')->where('id', '=', $id)->get();
        $dAsign = $asign[0];
        
        $to = DB::table('users')->select('email')->where('username', '=', $dAsign->user)->get();

        $data = [
            'driver' => $dAsign->driver,
            'cntr_number' => $dAsign->cntr_number,
            'booking' => $dAsign->booking,
            'truck' => $dAsign->truck,
            'truck_semi' => $dAsign->truck_semi,
            'transport' => $dAsign->transport,
            'transport_agent' => $dAsign->transport_agent,
            'user' => $dAsign->user,
            'company' => $dAsign->company
        ];

        $logapi = new logapi();
        $logapi->user = $dAsign->user;
        $logapi->detalle = 'AsignaCarga';
        $logapi->save();

        Mail::to('priopelliza@gmail.com')->send(new cargaAsignada($data, $date));
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
