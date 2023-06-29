<?php

namespace App\Http\Controllers;

use App\Models\CustomerLoadPlace;
use App\Mail\cargaAduana;
use App\Mail\cargaCargando;
use App\Mail\cargaDescarga;
use App\Mail\ubicacion;
use App\Models\logapi;
use App\Models\pruebasModel;
use App\Models\statu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;


class CustomerLoadPlaceController extends Controller
{

    public function coordenadas($patente)
    {

        $chek = new pruebasModel();
        $chek->contenido = 'Entro a la funcion coordenadas de /lugarDeCarga/{patente} con el Parametro:' . $patente;
        $chek->save();
        $coordenadas = DB::table('carga')
            ->select(
                'carga.id as idLoad',
                'cntr.id_cntr as IdTrip',
                'carga.load_place',
                'customer_load_places.lat',
                'customer_load_places.lon',
                'carga.custom_place',
                'aduanas.lat as latA',
                'aduanas.lon  as lonA',
                'carga.unload_place',
                'customer_unload_places.lat as latU',
                'customer_unload_places.lon  as lonU'
            )
            ->join('cntr', 'carga.booking', '=', 'cntr.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->join('aduanas', 'aduanas.description', '=', 'carga.custom_place')
            ->join('customer_load_places', 'customer_load_places.description', '=', 'carga.load_place')
            ->join('customer_unload_places', 'customer_unload_places.description', '=', 'carga.unload_place')
            ->where('asign.truck', '=', $patente)
            ->get();

        $chek = new pruebasModel();
        $chek->contenido = 'La api devolvio:' . $coordenadas;
        $chek->save();

        return $coordenadas;

        // SELECT * FROM `carga` INNER JOIN `cntr` INNER JOIN `asign` ON carga.booking = cntr.booking AND cntr.cntr_number = asign.cntr_number WHERE asign.truck = 'AE792WJ';
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accionLugarDeCarga($idTrip)
    {
       
        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr_number', 'booking')->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];

        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        if ($qd->main_status == 'CARGANDO') {

            $chek = new pruebasModel();
            $chek->contenido = 'Entro en manin status = CARGANDO';
            $chek->save();

            // si el status es igual al informado.     

            // Buscamos si se aviso o no al cliente. Si no se aviso. Avisamos.
            
            if ($qd->avisado == 0) {

                $chek = new pruebasModel();
                $chek->contenido = 'Entro en NO AVISADO';
                $chek->save();

                $insert = DB::table('status')->insert([
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Carga.',
                    'main_status' => 'CARGANDO',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
                $mail = $qmail[0]->mail_logistic;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                $sbx = DB::table('variables')->select('sandbox')->get();
             
                if ($sbx[0]->sandbox == 0) {


                    Mail::to($mail)->bcc('inboxplataforma@botzero.ar')->send(new cargaCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to:" . $mail;
                    $logApi->save();

                } else {

                    Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new cargaCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to:" . $mail;
                    $logApi->save();
                    
                }

                $actualizarAvisado = statu::find($qd->id);
                
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                
                return 'ok, Actulizó Status - Envió mail.'  . $qd->avisado;
                
            } elseif ($qd->avisado != 0 && $qd->avisado <= 119) { // // Buscamos si se aviso o no al cliente. Si se aviso o no fue hace mucho actualizamos. 


                $chek = new pruebasModel();
                $chek->contenido = 'entro en avisado y menos de 119 veces reportado';
                $chek->save();

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                /*   return 'ok, No actulizó Status - No envió mail.'  . $qd->avisado; */
            } elseif ($qd->avisado != 0 && $qd->avisado >= 120) {


                $chek = new pruebasModel();
                $chek->contenido = 'entro en avisado y mas de 120 veces reportado';
                $chek->save();

                DB::table('status')->insert([
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Carga.',
                    'main_status' => 'CARGANDO',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
                $mail = $qmail[0]->mail_logistic;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];


                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                /*  return 'ok, Actulizó Status - No envió mail.'  . $qd->avisado; */
            }
        } else {


            $chek = new pruebasModel();
            $chek->contenido = 'Entro en manin status != CARGANDO';
            $chek->save();

            DB::table('status')->insert([
                'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Carga.',
                'main_status' => 'CARGANDO',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);

            $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
            $description = $qd->status;

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
            $empresa = $qempresa[0]->empresa;

            $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
            $mail = $qmail[0]->mail_logistic;

            $datos = [
                'cntr' => $cntr->cntr_number,
                'description' =>  $description,
                'user' => $qd->user_status,
                'empresa' => $empresa,
                'booking' => $cntr->booking,
                'date' => $date
            ];
            
            $sbx = DB::table('variables')->select('sandbox')->get();
             
        if ($sbx[0]->sandbox == 0) {

            Mail::to($mail)->bcc('totaltrade@botzero.ar')->send(new cargaCargando($datos));

            $logApi = new logapi();
            $logApi->user = 'No Informa';
            $logApi->detalle = "envio email carga Cargnando to:" . $mail;
            $logApi->save();

        } else {

            Mail::to('pablorio@botzero.tech')->bcc('totaltrade@botzero.ar')->send(new cargaCargando($datos));

            $logApi = new logapi();
            $logApi->user = 'No Informa';
            $logApi->detalle = "envio email carga Cargnando to: pablorio@botzero.tech" ;
            $logApi->save();
        
        }

            $actualizarAvisado = statu::find($qd->id);
            $avisadoMas = $actualizarAvisado->avisado + 1;
            $actualizarAvisado->avisado = $avisadoMas;
            $actualizarAvisado->save();
            /* return 'ok, Actulizó Status - Envió mail.'  . $qd->avisado; */
        }
    }
    public function accionLugarAduana($idTrip)
    {

        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr_number', 'booking')->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];

        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        if ($qd->main_status == 'EN ADUANA') {

            $chek = new pruebasModel();
            $chek->contenido = 'entro en main status == EN aduana';
            $chek->save();

            // si el status es igual al informado.     

            // Buscamos si se aviso o no al cliente. Si no se aviso. Avisamos.

            if ($qd->avisado == 0) {

                $chek = new pruebasModel();
                $chek->contenido = 'entro en no avisado';
                $chek->save();

                DB::table('status')->insert([
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts de la aduana Asignada.',
                    'main_status' => 'EN ADUANA',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
                $mail = $qmail[0]->mail_logistic;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                $chek = new pruebasModel();
                $chek->contenido = 'envia mail con '.$datos;
                $chek->save();

                $sbx = DB::table('variables')->select('sandbox')->get();
             
                if ($sbx[0]->sandbox == 0) {
        

                Mail::to($mail)->bcc('inboxplataforma@botzero.ar')->send(new cargaAduana($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaAduana to:" . $mail;
                $logApi->save();

                }else{

                Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new cargaAduana($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaAduana to: 'pablorio@botzero.tech'" ;
                $logApi->save();

                }

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, Actulizó Status - Envió mail.';

            } elseif ($qd->avisado != 0 && $qd->avisado <= 119) { // // Buscamos si se aviso o no al cliente. Si se aviso o no fue hace mucho actualizamos. 


                $chek = new pruebasModel();
                $chek->contenido = 'entro en avisado y menos de 119 veces reportado ';
                $chek->save();

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, No actulizó Status - No envió mail.';

            } elseif ($qd->avisado != 0 && $qd->avisado >= 120) {

                $chek = new pruebasModel();
                $chek->contenido = 'entro en avisado y mas de 119 veces reportado ';
                $chek->save();

                DB::table('status')->insert([
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts de la Aduana asignada.',
                    'main_status' => 'EN ADUANA',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
                $mail = $qmail[0]->mail_logistic;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, Actulizó Status - No envió mail.';
            }
        } else {


            $chek = new pruebasModel();
            $chek->contenido = 'Entro en manin status != ADUANA';
            $chek->save();


            DB::table('status')->insert([
                'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts de la Aduana asignada.',
                'main_status' => 'EN ADUANA',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);

            $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
            $description = $qd->status;

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
            $empresa = $qempresa[0]->empresa;

            $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
            $mail = $qmail[0]->mail_logistic;

            $datos = [
                'cntr' => $cntr->cntr_number,
                'description' =>  $description,
                'user' => $qd->user_status,
                'empresa' => $empresa,
                'booking' => $cntr->booking,
                'date' => $date
            ];

            $sbx = DB::table('variables')->select('sandbox')->get();
             
            if ($sbx[0]->sandbox == 0) {

            Mail::to($mail)->bcc('inboxplataforma@botzero.ar')->send(new cargaAduana($datos));
            
            $logApi = new logapi();
            $logApi->user = 'No Informa';
            $logApi->detalle = "envio email carga Aduana to:" . $mail;
            $logApi->save();

            }else{

            Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new cargaAduana($datos));

            $logApi = new logapi();
            $logApi->user = 'No Informa';
            $logApi->detalle = "envio email carga Aduana to: pablorio@botzero.tech";
            $logApi->save();

            }
            $actualizarAvisado = statu::find($qd->id);
            $avisadoMas = $actualizarAvisado->avisado + 1;
            $actualizarAvisado->avisado = $avisadoMas;
            $actualizarAvisado->save();
            return 'ok, Actulizó Status - Envió mail.';
        }
    }
    public function accionLugarDescarga($idTrip)
    {
        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr_number', 'booking')->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];

        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        if ($qd->main_status == 'STACKING') {

            // si el status es igual al informado.     

            // Buscamos si se aviso o no al cliente. Si no se aviso. Avisamos.

            if ($qd->avisado == 0) {

                DB::table('status')->insert([
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Descarga.',
                    'main_status' => 'STACKING',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
                $mail = $qmail[0]->mail_logistic;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                $sbx = DB::table('variables')->select('sandbox')->get();
             
            if ($sbx[0]->sandbox == 0) {

                Mail::to($mail)->bcc('inboxplataforma@botzero.ar')->send(new cargaDescarga($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaDescarga to:" . $mail;
                $logApi->save();

            } else {

                Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new cargaDescarga($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email Instructivo to: 'pablorio@botzero.tech'";
                $logApi->save();

            }
                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, Actulizó Status - Envió mail.';
            } elseif ($qd->avisado != 0 && $qd->avisado <= 119) { // // Buscamos si se aviso o no al cliente. Si se aviso o no fue hace mucho actualizamos. 

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, No actulizó Status - No envió mail.';
            } elseif ($qd->avisado != 0 && $qd->avisado >= 120) {


                DB::table('status')->insert([
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Descarga.',
                    'main_status' => 'STACKING',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
                $mail = $qmail[0]->mail_logistic;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                
                $sbx = DB::table('variables')->select('sandbox')->get();
             
                if ($sbx[0]->sandbox == 0) {
        
                Mail::to($mail)->bcc('inboxplataforma@botzero.ar')->send(new cargaDescarga($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaDescarga to:" . $mail;
                $logApi->save();

                }else{

                Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new cargaDescarga($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaDescarga to: pablorio@botzero.tech";
                $logApi->save();


                }

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, Actulizó Status - No envió mail.';
            }
        } else {
            DB::table('status')->insert([
                'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Descarga.',
                'main_status' => 'STACKING',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);

            $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
            $description = $qd->status;

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
            $empresa = $qempresa[0]->empresa;

            $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
            $mail = $qmail[0]->mail_logistic;

            $datos = [
                'cntr' => $cntr->cntr_number,
                'description' =>  $description,
                'user' => $qd->user_status,
                'empresa' => $empresa,
                'booking' => $cntr->booking,
                'date' => $date
            ];

            $sbx = DB::table('variables')->select('sandbox')->get();
             
            if ($sbx[0]->sandbox == 0) {

            Mail::to($mail)->bcc('inboxplataforma@botzero.ar')->send(new cargaDescarga($datos));

            $logApi = new logapi();
            $logApi->user = 'No Informa';
            $logApi->detalle = "envio email cargaDescarga to:" . $mail;
            $logApi->save();

            } else {

            Mail::to('pablorio@botzero.tech')->bcc('inboxplataforma@botzero.ar')->send(new cargaDescarga($datos));

            $logApi = new logapi();
            $logApi->user = 'No Informa';
            $logApi->detalle = "envio email cargaDescarga to:" . $mail;
            $logApi->save();


            }
            $actualizarAvisado = statu::find($qd->id);
            $avisadoMas = $actualizarAvisado->avisado + 1;
            $actualizarAvisado->avisado = $avisadoMas;
            $actualizarAvisado->save();
            return 'ok, Actulizó Status - Envió mail.';
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerLoadPlaces = DB::table('customer_load_places')->get();       
        return $customerLoadPlaces;
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
        $customerLoadPlace = new CustomerLoadPlace();
        $customerLoadPlace->description = $request['description'];
        $customerLoadPlace->address= $request['address'];
        $customerLoadPlace->city = $request['city'];
        $customerLoadPlace->country = $request['country'];
        $customerLoadPlace->km_from_town = $request['km_from_town'];
        $customerLoadPlace->remarks = $request['remarks'];
        $customerLoadPlace->latitud = $request['latitud'];
        $customerLoadPlace->longitud = $request['longitud'];
        $customerLoadPlace->link_maps = $request['link_maps'];
        $customerLoadPlace->user = $request['user'];
        $customerLoadPlace->company = $request['company'];
        $customerLoadPlace->save();

        return $customerLoadPlace;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerLoadPlace  $customerLoadPlace
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        $customerLoadPlace = DB::table('customer_load_places')->where('id','=',$id)->get();       
        return $customerLoadPlace;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerLoadPlace  $customerLoadPlace
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerLoadPlace $customerLoadPlace)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerLoadPlace  $customerLoadPlace
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $customerLoadPlace = CustomerLoadPlace::findOrFail($id);
        $customerLoadPlace->description = $request['description'];
        $customerLoadPlace->address= $request['address'];
        $customerLoadPlace->city = $request['city'];
        $customerLoadPlace->country = $request['country'];
        $customerLoadPlace->km_from_town = $request['km_from_town'];
        $customerLoadPlace->remarks = $request['remarks'];
        $customerLoadPlace->latitud = $request['latitud'];
        $customerLoadPlace->longitud = $request['longitud'];
        $customerLoadPlace->link_maps = $request['link_maps'];
        $customerLoadPlace->user = $request['user'];
        $customerLoadPlace->company = $request['company'];
        $customerLoadPlace->save();

        return $customerLoadPlace;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerLoadPlace  $customerLoadPlace
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        CustomerLoadPlace::destroy($id);

        $existe = CustomerLoadPlace::find($id);
        if($existe){
            return 'No se elimino el Lugar de Carga';
        }else{
            return 'Se elimino el Lugar de Carga';
        };
    }
}
