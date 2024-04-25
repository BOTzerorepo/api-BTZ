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
                'customer_unload_places.latitud as latU',
                'customer_unload_places.longitud  as lonU'
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

        if ($qd->main_status != 'CARGANDO') {

            DB::table('status')->insert([
                'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Carga.',
                'main_status' => 'CARGANDO',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);
            DB::table('cntr')
                ->where('cntr_number', $cntr->cntr_number)
                ->update([
                    'main_status' => 'CARGANDO',
                    'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts del Lugar de Carga.'
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

            } elseif($sbx[0]->sandbox == 2) { 

                Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaCargando($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to:" . $mail;
                $logApi->save();

            } else {

                Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaCargando($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to:" . $mail;
                $logApi->save();

            }

            $actualizarAvisado = statu::find($qd->id);

            $avisadoMas = $actualizarAvisado->avisado + 1;
            $actualizarAvisado->avisado = $avisadoMas;
            $actualizarAvisado->save();

            return 'Se cambió Status - Envió mail.'  . $qd->avisado;
            
        } else {

            if ($qd->avisado == 0) {

              

                DB::table('status')->insert([
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts del Lugar de Carga.',
                    'main_status' => 'CARGANDO',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'CARGANDO',
                        'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts del Lugar de Carga.'
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

                } elseif($sbx[0]->sandbox == 2) { 

                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to:" . $mail;
                    $logApi->save();

                } else {
                    Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to:" . $mail;
                    $logApi->save();
                }

                $actualizarAvisado = statu::find($qd->id);

                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();

                return 'No se cambio status y se envio mail porque el usuario no estaba notificado.';
            }
            return 'No ser realizó ninguna acción: El Status estaba cambiado y el usuario notificado.';
        }
        return 'ERROR: algo anduvo mal.';
    }

    public function accionLugarAduana($idTrip)
    {

        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr_number', 'booking')->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];

        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        if ($qd->main_status != 'EN ADUANA') {

                DB::table('status')->insert([
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts de la aduana Asignada.',
                    'main_status' => 'EN ADUANA',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'EN ADUANA',
                        'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts de la Aduana asignada.'
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
                    $logApi->save();

                } elseif($sbx[0]->sandbox == 2) {

                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to: 'pablorio@botzero.tech'";
                    $logApi->save();

                }else {
                    Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to: 'pablorio@botzero.tech'";
                    $logApi->save();

                }

                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
            
                return 'Se cambió Status - Envió mail.'  . $qd->avisado;
            
        } else {

            if ($qd->avisado == 0) {

            

                DB::table('status')->insert([
                    'status' => '[AUTOMATICO] Camión se encuentra en un radio de 50 mts de la aduana Asignada.',
                    'main_status' => 'EN ADUANA',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'EN ADUANA',
                        'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts de la Aduana asignada.'
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
                    $logApi->save();

                } elseif($sbx[0]->sandbox == 2) {

                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to: 'pablorio@botzero.tech'";
                    $logApi->save();

                } else {

                    Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to: 'pablorio@botzero.tech'";
                    $logApi->save();
                }
                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                
                return 'No se cambio status y se envio mail porque el usuario no estaba notificado.';

            }
            return 'No ser realizó ninguna acción: El Status estaba cambiado y el usuario notificado.';

        }
        return 'ERROR: algo anduvo mal.';

    }
    public function accionLugarDescarga($idTrip)
    {
        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr_number', 'booking')->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];

        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        if ($qd->main_status != 'STACKING') {

           

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
                        'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts del Lugar de Descarga.'
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
                    $logApi->save();

                } elseif ($sbx[0]->sandbox == 2) { 

                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaDescarga($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email Instructivo to: 'pablorio@botzero.tech'";
                    $logApi->save();

                }else{

                    Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaDescarga($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email Instructivo to: 'pablorio@botzero.tech'";
                    $logApi->save();

                }
                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();

                return 'Se cambió Status - Envió mail.'  . $qd->avisado;
            
        } else {

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
                        'status_cntr' => '[AUTOMATICO] Camión se encuentra en un radio de 200 mts del Lugar de Descarga.'
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
                    $logApi->save();
                } elseif ($sbx[0]->sandbox == 2) {
                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaDescarga($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email Instructivo to: 'pablorio@botzero.tech'";
                    $logApi->save();
                }else {

                    Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new cargaDescarga($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email Instructivo to: 'pablorio@botzero.tech'";
                    $logApi->save();
                }
                $actualizarAvisado = statu::find($qd->id);
                $avisadoMas = $actualizarAvisado->avisado + 1;
                $actualizarAvisado->avisado = $avisadoMas;
                $actualizarAvisado->save();
                return 'No se cambio status, pero se envio mail porque el usuario no estaba notificado.';
               
            }
            return 'No ser realizó ninguna acción: El Status estaba cambiado y el usuario notificado.';

        }
        return 'ERROR: algo anduvo mal.';

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
        $customerLoadPlace->address = $request['address'];
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
    public function show($id)
    {
        $customerLoadPlace = DB::table('customer_load_places')->where('id', '=', $id)->get();
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
        $customerLoadPlace->address = $request['address'];
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
    public function destroy($id)
    {
        CustomerLoadPlace::destroy($id);

        $existe = CustomerLoadPlace::find($id);
        if ($existe) {
            return 'No se elimino el Lugar de Carga';
        } else {
            return 'Se elimino el Lugar de Carga';
        };
    }
}
