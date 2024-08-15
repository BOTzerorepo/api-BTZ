<?php

namespace App\Http\Controllers;

use App\Models\asign;
use App\Models\Carga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

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

        if ($user->permiso == 'Traffic' || $user->permiso == 'Master') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->whereBetween('carga.load_date', [$empiezaSemana, $terminaSemana])
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'ASC')->get();
                
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->whereBetween('carga.load_date', [$empiezaSemana, $terminaSemana])
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'ASC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }
    public function loadLastWeek($user)
    {

        $user = DB::table('users')->where('username', '=', $user)->first();
        $empiezaSemana = Carbon::parse('last monday')->startOfDay();

        if ($user->permiso == 'Traffic'|| $user->permiso == 'Master') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', "<", $empiezaSemana)
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.status', '!=', 'TERMINADA')
                ->orderBy('carga.load_date', 'ASC')->get();
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', "<", $empiezaSemana)
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'ASC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }
    public function loadNextWeek($user)
    {
        $user = DB::table('users')->where('username', '=', $user)->first();

        $terminaSemana = Carbon::parse('next Sunday')->endOfDay();

        if ($user->permiso == 'Traffic'|| $user->permiso == 'Master') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', ">", $terminaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'ASC')->get();
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', ">", $terminaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'ASC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }

    public function loadFinished($user)
    {
        $user = DB::table('users')->where('username', '=', $user)->first();

        if ($user->permiso == 'Traffic'|| $user->permiso == 'Master') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.status', '=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'ASC')->get();
                
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.status', '=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'ASC')->get();
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

        if ($user->permiso == 'Traffic'|| $user->permiso == 'Master') {

            $cargaPorId = DB::table('carga')
            ->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->leftjoin('trucks', 'trucks.domain', '=', 'asign.truck')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport','asign.truck','asign.truck_semi','asign.file_instruction', 'trucks.alta_aker')
            ->where('carga.empresa', '=', $user->empresa)
            ->where('carga.id', '=', $id)
            ->orderBy('carga.load_date', 'DESC')->get();

            return $cargaPorId;

        }else{

            $cargaPorId = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->leftjoin('trucks', 'trucks.domain', '=', 'asign.truck')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport','asign.truck','asign.truck_semi','asign.file_instruction','trucks.alta_aker')
            ->where('carga.empresa', '=', $user->empresa)
            ->where('carga.user', '=', $user->username)
            ->where('carga.id', '=', $id)
            ->orderBy('carga.load_date', 'DESC')->get();

            return $cargaPorId;
            
        }
    }

    public function showCargaDomain($domain)
    {

            $cargaPorId = DB::table('carga')
            ->leftjoin('cntr', 'cntr.booking', '=', 'carga.booking')
            ->leftjoin('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->leftjoin('trucks', 'trucks.domain', '=', 'asign.truck')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport', 'asign.truck', 'asign.truck_semi', 'asign.file_instruction', 'trucks.alta_aker')
            ->where('asign.truck', '=', $domain)
            ->orderBy('carga.load_date', 'DESC')->get();
            return $cargaPorId;
       
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
    
    public function issetTrader($trader)
    {
        $trader = DB::table('customers')->where('registered_name', '=', $trader)->get();
        return $trader->count();
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
        DB::beginTransaction();
        try {
            // Busca la carga por su ID
            $carga = DB::table('carga')->where('id', $id)->first();
    
            // Verifica si la carga existe
            if (!$carga) {
                return response()->json(['error' => 'La carga no fue encontrada.'], 404);
            }
            // Elimina los registros relacionados en las tablas cntr y asign
            DB::table('cntr')->where('booking', $carga->booking)->delete();
            DB::table('asign')->where('booking', $carga->booking)->delete();

            // Elimina la carga
            DB::table('carga')->where('id', $id)->delete();
    
            DB::commit();
    
            // Devuelve una respuesta de éxito
            return response()->json([
                'message' => 'La carga ha sido eliminada correctamente.',
                'message_type' => 'success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            // Manejar otras excepciones si es necesario
            return response()->json(['error' => $errorMessage, 'message_type' => 'danger'], 500);
        }
    }

    public function guardarFormulario(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validación de datos
            $request->validate([
                'ref_customer' => 'required',
                'tarifa_ref' => 'required',
                'trader' => 'required',
                'booking' => 'required',
                'qviajes' => 'required',
                'cntr_type' => 'required',
                'commodity' => 'required',
                'tara' => 'required',
                'load_place' => 'required',
                'load_date' => 'required',
                'unload_place' => 'required',
                'cut_off_fis' => 'required',
                'user' => 'required',
                'status' => 'required',
                'empresa' => 'required',
                'type' => 'required',
            ]);

            // Crear una carga
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
            $carga->tara = $request->input('tara');
            $carga->tara_string = $request->input('tara_string');
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

            for ($i = 1; $i <= $request->input('qviajes'); $i++) {
                $numAleatorio = $request->input('booking') . $i;

                DB::table('cntr')->insert([
                    'booking' => $request->input('booking'),
                    'cntr_number' => $numAleatorio,
                    'user_cntr' => $request->input('user'),
                    'retiro_place' => $request->input('retiro_place'),
                    'cntr_type' => $request->input('cntr_type'),
                    'company' => $request->input('empresa'),
                ]);

                DB::table('asign')->insert([
                    'cntr_number' => $numAleatorio,
                    'booking' => $request->input('booking'),
                    'user' => $request->input('user'),
                    'company' => $request->input('empresa'),
                ]);
            }

            DB::commit();

            // Devuelvo que se creó correctamente el código
            return response()->json(['message' => 'Carga ingresada correctamente Booking: ' . $request->input('booking'), 'message_type' => 'success', 'carga' => $carga, 'last_id' => $carga->id], 200);
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
            // Manejar otras excepciones si es necesario
            return response()->json(['error' => $errorMessage, 'message_type' => 'danger'], 500);
        }

    }

}
