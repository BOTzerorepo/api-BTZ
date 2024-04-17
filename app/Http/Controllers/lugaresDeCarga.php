<?php

namespace App\Http\Controllers;

use App\Mail\cargaAduana;
use App\Mail\cargaCargando;
use App\Mail\cargaDescarga;
use App\Mail\ubicacion;
use App\Models\logapi;
use App\Models\pruebasModel;
use App\Models\statu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class lugaresDeCarga extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        return 'hola mostro';
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

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'CARGANDO',
                        'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Carga.'
                    ]);


                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $chek = new pruebasModel();
                $chek->contenido = $description;
                $chek->save();

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $chek = new pruebasModel();
                $chek->contenido = $empresa;
                $chek->save();

                $qmail = DB::table('empresas')->where('razon_social', '=', $empresa)->select('mail_logistic')->get();
                $mail = $qmail[0]->mail_logistic;

                $chek = new pruebasModel();
                $chek->contenido = $mail;
                $chek->save();

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                $sbx = DB::table('variables')->select('sandbox')->get();
                $inboxEmail = env('INBOX_EMAIL');
                if ($sbx[0]->sandbox == 0) {
        
                Mail::to($mail)->bcc($inboxEmail)->send(new cargaCargando($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to:" . $mail;
                $logApi->save();

                } else {

                Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaCargando($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "+ Sandbox + envio email cargaCargando to :" . $mail;
                $logApi->save();
                }

                $actualizarAvisado = statu::find($qd->id);
                
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                
                return 'ok, Actulizó Status - Envió mail.'  . $qd->avisado;
                
            } /* elseif ($qd->avisado != 0 && $qd->avisado <= 239) { //}} // Buscamos si se aviso o no al cliente. Si se aviso o no fue hace mucho actualizamos. 


                $chek = new pruebasModel();
                $chek->contenido = 'entro en avisado y menos de 119 veces reportado';
                $chek->save();

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                /*   return 'ok, No actulizó Status - No envió mail.'  . $qd->avisado; 
            } elseif ($qd->avisado != 0 && $qd->avisado >= 240) {


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
            /*  return 'ok, Actulizó Status - No envió mail.'  . $qd->avisado;
            } */

            /// Sacamos todo estas lineas porque avisa demaciado sobre el estado de la Carga. 

        } else {


            $chek = new pruebasModel();
            $chek->contenido = 'Entro en manin status != CARGANDO';
            $chek->save();

            DB::table('status')->insert([
                'status' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts del Lugar de Carga.',
                'main_status' => 'CARGANDO',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);

            DB::table('cntr')
                ->where('cntr_number', $cntr->cntr_number)
                ->update([
                    'main_status' => 'CARGANDO',
                    'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Carga.'
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
            $inboxEmail = env('INBOX_EMAIL');
            if ($sbx[0]->sandbox == 0) {

                Mail::to($mail)->bcc($inboxEmail)->send(new cargaCargando($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to:" . $mail;
                $logApi->save();

            } else {

                Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaCargando($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "+ Sandbox + envio email cargaCargando to:" . $mail;
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


        $chek = new pruebasModel();
        $chek->contenido = 'Entro a la funcion accionLugarDeCarga de /accionLugarAduana/{idTrip}con el Parametro:' . $idTrip;
        $chek->save();

        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr_number', 'booking')->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];

        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        $chek = new pruebasModel();
        $chek->contenido = 'Main Status:' . $qd->main_status ;
        $chek->save();

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
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts de la aduana Asignada.',
                    'main_status' => 'EN ADUANA',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);
                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'EN ADUANA',
                        'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts de la aduana asignada.'
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
                $inboxEmail = env('INBOX_EMAIL');
                if ($sbx[0]->sandbox == 0) {
                    
                    Mail::to($mail)->bcc($inboxEmail)->send(new cargaAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to:" . $mail;
                    $logApi->save();

                } else {

                    Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "+ Sandbox + envio email cargaAduana to:" . $mail;
                    $logApi->save();

                }
        

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, Actulizó Status - Envió mail.';

            } /* elseif ($qd->avisado != 0 && $qd->avisado <= 239) { // // Buscamos si se aviso o no al cliente. Si se aviso o no fue hace mucho actualizamos. 


                $chek = new pruebasModel();
                $chek->contenido = 'entro en avisado y menos de 119 veces reportado ';
                $chek->save();

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, No actulizó Status - No envió mail.';

            } elseif ($qd->avisado != 0 && $qd->avisado >= 240) {

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
            } */
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

            DB::table('cntr')
                ->where('cntr_number', $cntr->cntr_number)
                ->update([
                    'main_status' => 'EN ADUANA',
                    'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts de la aduana asignada.'
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
            $inboxEmail = env('INBOX_EMAIL');
            if ($sbx[0]->sandbox == 0) {

                Mail::to($mail)->bcc($inboxEmail)->send(new cargaAduana($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaAduana to:" . $mail;

            } else {

                Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaAduana($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "+ Sandbox + envio email cargaAduana to: " . $mail;
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

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                    'main_status' => 'STACKING',
                    'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Descarga.'
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
                $inboxEmail = env('INBOX_EMAIL');
                if ($sbx[0]->sandbox == 0) {

                    Mail::to($mail)->bcc($inboxEmail)->send(new cargaDescarga($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaDescarga to:" . $mail;

                } else {

                    Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaDescarga($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "+ Sandbox + envio email cargaDescarga to: " . $mail;
                    $logApi->save();
                }
    
                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, Actulizó Status - Envió mail.';
             } /* elseif ($qd->avisado != 0 && $qd->avisado <= 239) { // // Buscamos si se aviso o no al cliente. Si se aviso o no fue hace mucho actualizamos. 

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, No actulizó Status - No envió mail.';
            } elseif ($qd->avisado != 0 && $qd->avisado >= 240) {


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

                    Mail::to($mail)->bcc($inboxEmail)->send(new cargaDescarga($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaDescarga to:" . $mail;

                } else {

                    Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaDescarga($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "+ Sandbox + envio email cargaDescarga to: " . $mail;
                    $logApi->save();
                }


                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'ok, Actulizó Status - No envió mail.';
            } */
        } else {
            DB::table('status')->insert([
                'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Descarga.',
                'main_status' => 'STACKING',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);
 
            DB::table('cntr')
                ->where('cntr_number', $cntr->cntr_number)
                ->update([
                    'main_status' => 'STACKING',
                    'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Descarga.'
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
            $inboxEmail = env('INBOX_EMAIL');
            if ($sbx[0]->sandbox == 0) {

                Mail::to($mail)->bcc($inboxEmail)->send(new cargaDescarga($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaDescarga to:" . $mail;

            } else {

                Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaDescarga($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "+ Sandbox + envio email cargaDescarga to: " . $mail;
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
