<?php

namespace App\Http\Controllers;

use App\Models\asign;
use App\Models\Carga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class cargaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function loadTHisWeek($user)

    {

        $user = DB::table('users')->where('username', '=', $user)->first();
        $terminaSemana = Carbon::parse('next Sunday')->endOfDay();
        $empiezaSemana = Carbon::parse('last monday')->startOfDay();

        if ($user->permiso == 'Traffic') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->whereBetween('carga.load_date', [$empiezaSemana, $terminaSemana])
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'DESC')->get();
                
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->whereBetween('carga.load_date', [$empiezaSemana, $terminaSemana])
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'DESC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }
    public function loadLastWeek($user)
    {

        $user = DB::table('users')->where('username', '=', $user)->first();
        $empiezaSemana = Carbon::parse('last monday')->startOfDay();

        if ($user->permiso == 'Traffic') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', "<", $empiezaSemana)
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.status', '!=', 'TERMINADA')
                ->orderBy('carga.load_date', 'DESC')->get();
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', "<", $empiezaSemana)
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'DESC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }
    public function loadNextWeek($user)
    {
        $user = DB::table('users')->where('username', '=', $user)->first();

        $terminaSemana = Carbon::parse('next Sunday')->endOfDay();

        if ($user->permiso == 'Traffic') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', ">", $terminaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'DESC')->get();
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', ">", $terminaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'DESC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }

    public function loadFinished($user)
    {
        $user = DB::table('users')->where('username', '=', $user)->first();

      

        if ($user->permiso == 'Traffic') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.status', '=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'DESC')->get();
                
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.status', '=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'DESC')->get();
        }

        return $todasLasCargasDeEstaSemana;
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
    public function show($id, $user)
    {

        $user = DB::table('users')->where('username', '=', $user)->first();

        if ($user->permiso == 'Traffic') {

            $cargaPorId = DB::table('carga')
            ->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport','asign.file_instruction')
            ->where('carga.empresa', '=', $user->empresa)
            ->where('carga.id', '=', $id)
            ->orderBy('carga.load_date', 'DESC')->get();

            return $cargaPorId;

        }else{

            $cargaPorId = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport','asign.file_instruction')
            ->where('carga.empresa', '=', $user->empresa)
            ->where('carga.user', '=', $user->username)
            ->where('carga.id', '=', $id)
            ->orderBy('carga.load_date', 'DESC')->get();

            return $cargaPorId;
            
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
    public function issetBooking($booking)
    {
        $booking = DB::table('carga')->where('booking', '=', $booking)->get();
        return $booking->count();
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

    public function guardarFormulario(Request $request)
    {



        $carga = new Carga();
        $carga->booking = $request->input('booking');
        $carga->bl_hbl = $request->input('bl_hbl');
        $carga->shipper = $request->input('shipper');
        $carga->commodity = $request->input('commodity');
        $carga->load_place = $request->input('load_place');
        $carga->trader = $request->input('trader');
        $carga->importador = $request->input('importador');
        $carga->load_date = $request->input('load_date');
        $carga->unload_place = $request->input('unload_place');
        $carga->cut_off_fis = $request->input('cut_off_fis');
        $carga->cut_off_doc = $request->input('cut_off_doc');
        $carga->oceans_line = $request->input('oceans_line');
        $carga->vessel = $request->input('vessel');
        $carga->voyage = $request->input('voyage');
        $carga->final_point = $request->input('final_point');
        $carga->ETA = $request->input('ETA');
        $carga->ETD = $request->input('ETD');
        $carga->consignee = $request->input('consignee');
        $carga->notify = $request->input('notify');
        $carga->custom_place = $request->input('custom_place');
        $carga->custom_agent = $request->input('custom_agent');
        $carga->custom_place_impo = $request->input('custom_place_impo');
        $carga->custom_agent_impo = $request->input('custom_agent_impo');
        $carga->ref_customer = $request->input('ref_customer');
        $carga->senasa = $request->input('senasa');
        $carga->senasa_string = $request->input('senasa_string');
        $carga->referencia_carga = $request->input('referencia_carga');
        $carga->comercial_reference = $request->input('comercial_reference');
        $carga->observation_customer = $request->input('observation_customer');
        $carga->tarifa_ref = $request->input('tarifa_ref');
        $carga->user = $request->input('user');
        $carga->empresa = $request->input('empresa');
        $carga->status = $request->input('status');
        $carga->big_state = $request->input('big_state');
        $carga->confirm_date = $request->input('confirm_date');
        $carga->ex_alto = $request->input('ex_alto');
        $carga->ex_ancho = $request->input('ex_ancho');
        $carga->ex_largo = $request->input('ex_largo');
        $carga->obs_imo = $request->input('obs_imo');
        $carga->rf_tem = $request->input('rf_tem');
        $carga->rf_humedad = $request->input('rf_humedad');
        $carga->rf_venti = $request->input('rf_venti');
        $carga->document_bookingConf = $request->input('document_bookingConf');
        $carga->type = $request->input('type');
        $carga->save();

        if ($carga->exists) {
            return response()->json(['message' => 'Carga ingresada correctamente', 'carga' => $carga, 'last_id' => $carga->id], 200);
        } else {
            return response()->json(['message' => 'Error al registrar la carga', 'error' => 'Ups! no pudimos registrar la carga, por favor revisá que no esté repetica bajo el id: ' . $carga->booking], 500);
        }

    }

}
