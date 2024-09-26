<?php

namespace App\Http\Controllers;

use App\Models\asign;
use App\Models\Carga;
use App\Models\cntr;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Mail\UpdateCarga;
use Illuminate\Support\Facades\Mail;
use App\Models\Transport;


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

        $user = User::where('username', '=', $user)->first();
        $terminaSemana = Carbon::parse('next Sunday')->endOfDay();
        $empiezaSemana = Carbon::parse('last monday')->startOfDay();


    if ($user->permiso == 'Traffic' || $user->permiso == 'Master') {
       
            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
            ->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
            ->whereNull('cntr.deleted_at')
            ->whereNull('asign.deleted_at')
            ->whereBetween('carga.load_date', [$empiezaSemana, $terminaSemana])
            ->where('carga.status', '!=', 'TERMINADA')
            ->where('carga.empresa', '=', $user->empresa)
            ->orderBy('carga.load_date', 'ASC')
            ->get();   

        } elseif ($user->permiso == 'Transport') {
       
            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
            ->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
            ->whereNull('cntr.deleted_at')
            ->whereNull('asign.deleted_at')
            ->whereBetween('carga.load_date', [$empiezaSemana, $terminaSemana])
            ->where('carga.status', '!=', 'TERMINADA')
            ->where('carga.empresa', '=', $user->empresa)
            ->orderBy('carga.load_date', 'ASC')
            ->get();   

        } else {
            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
                ->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->whereNull('cntr.deleted_at')
                ->whereNull('asign.deleted_at')
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

        $user = User::where('username', '=', $user)->first();
        $empiezaSemana = Carbon::parse('last monday')->startOfDay();

        if ($user->permiso == 'Traffic'|| $user->permiso == 'Master') {

            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
                ->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', "<", $empiezaSemana)
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.status', '!=', 'TERMINADA')
                ->whereNull('cntr.deleted_at')
                ->whereNull('asign.deleted_at')
                ->orderBy('carga.load_date', 'ASC')->get();
        } elseif ($user->permiso == 'Transport') {

            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
                ->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->whereNull('cntr.deleted_at')
                ->whereNull('asign.deleted_at')
                ->where('carga.load_date', "<", $empiezaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'ASC')
                ->get();
        }else {

            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
                ->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', "<", $empiezaSemana)
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.user', '=', $user->username)
                ->whereNull('cntr.deleted_at')
                ->whereNull('asign.deleted_at')
                ->orderBy('carga.load_date', 'ASC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }
    public function loadNextWeek($user)
    {
        $user = User::where('username', '=', $user)->first();

        $terminaSemana = Carbon::parse('next Sunday')->endOfDay();

        if ($user->permiso == 'Traffic'|| $user->permiso == 'Master') {

            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
                ->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', ">", $terminaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->whereNull('cntr.deleted_at')
                ->whereNull('asign.deleted_at')
                ->orderBy('carga.load_date', 'ASC')->get();
        }  elseif ($user->permiso == 'Transport') {

            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
                ->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->whereNull('cntr.deleted_at')
                ->whereNull('asign.deleted_at')
                ->where('carga.load_date', ">", $terminaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'ASC')
                ->get();
        }else {

            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
                ->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', ">", $terminaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->whereNull('cntr.deleted_at')
                ->whereNull('asign.deleted_at')
                ->orderBy('carga.load_date', 'ASC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }

    public function loadFinished($user)
    {
        $user = User::where('username', '=', $user)->first();

        if ($user->permiso == 'Traffic'|| $user->permiso == 'Master') {

            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
                ->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.status', '=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->whereNull('cntr.deleted_at')
                ->whereNull('asign.deleted_at')
                ->orderBy('carga.load_date', 'ASC')->get();
                
        } else {

            $todasLasCargasDeEstaSemana =  Carga::whereNull('carga.deleted_at')
                ->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.status', '=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->whereNull('cntr.deleted_at')
                ->whereNull('asign.deleted_at')
                ->orderBy('carga.load_date', 'ASC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }
    public function loadFinishedTransport($transport)
    {

        $transport = Transport::find($transport);


            $todasLasCargasDeEstaSemana = Carga::whereNull('carga.deleted_at')
            ->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->select('carga.ref_customer', 'carga.booking', 'carga.shipper','carga.commodity', 'carga.type', 'carga.load_place', 'carga.unload_place', 'carga.load_date', 'carga.cut_off_fis', 'carga.custom_place',  'carga.custom_agent','carga.custom_place_impo', 'carga.custom_agent_impo', 'cntr.cntr_number', 'cntr.cntr_type', 'cntr.main_status','cntr.out_usd', 'cntr.observation_out', 'asign.driver', 'asign.transport', 'asign.truck', 'asign.truck_semi')
            ->where('cntr.main_status', '=', 'TERMINADA')
            ->where('asign.transport', '=', $transport->razon_social)
            ->whereNull('cntr.deleted_at')
            ->whereNull('asign.deleted_at')
            ->orderBy('carga.load_date', 'ASC')->get();
       

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

        $user = User::where('username', '=', $user)->first();

        if ($user->permiso == 'Traffic'|| $user->permiso == 'Master') {

            $cargaPorId = Carga::whereNull('carga.deleted_at')
            ->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->leftjoin('trucks', 'trucks.domain', '=', 'asign.truck')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport','asign.truck','asign.truck_semi','asign.file_instruction', 'trucks.alta_aker')
            ->where('carga.empresa', '=', $user->empresa)
            ->where('carga.id', '=', $id)
            ->whereNull('cntr.deleted_at')
            ->whereNull('asign.deleted_at')
            ->orderBy('carga.load_date', 'DESC')->get();

            return $cargaPorId;

        }else{

            $cargaPorId = Carga::whereNull('carga.deleted_at')
            ->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->leftjoin('trucks', 'trucks.domain', '=', 'asign.truck')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport','asign.truck','asign.truck_semi','asign.file_instruction','trucks.alta_aker')
            ->where('carga.empresa', '=', $user->empresa)
            ->where('carga.user', '=', $user->username)
            ->where('carga.id', '=', $id)
            ->whereNull('cntr.deleted_at')
            ->whereNull('asign.deleted_at')
            ->orderBy('carga.load_date', 'DESC')->get();

            return $cargaPorId;
            
        }
    }

    public function showCargaDomain($domain)
    {

            $cargaPorId = Carga::whereNull('carga.deleted_at')
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
        $booking = Carga::whereNull('deleted_at')->where('booking', '=', $booking)->get();
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
        try {
            // Validar los datos entrantes
            $validatedData = $request->validate([
                'commodity' => 'nullable|string',
                'trader' => 'nullable|string',
                'shipper' => 'nullable|string',
                'custom_agent' => 'nullable|string',
                'custom_agent_impo' => 'nullable|string',
                'custom_place' => 'nullable|string',
                'custom_place_impo' => 'nullable|string',
                'load_place' => 'nullable|string',
                'unload_place' => 'nullable|string',
                'final_point' => 'nullable|string',
                'oceans_line' => 'nullable|string',
                'senasa' => 'nullable|string',
                'tara' => 'nullable|string',
                'importador' => 'nullable|string',
                'tarifa_ref' => 'nullable|string',
                'load_date' => 'nullable|string',
                'ref_customer' => 'nullable|string',
                'cut_off_fis' => 'nullable|string',
                'vessel' => 'nullable|string',
                'voyage' => 'nullable|string',
                'observation_customer' => 'nullable|string',
                'senasa_string' => 'nullable|string',
                'tara_string' => 'nullable|string',
                'bl_hbl' => 'nullable|string',
                'ex_alto' => 'nullable|numeric',
                'ex_ancho' => 'nullable|numeric',
                'ex_largo' => 'nullable|numeric',
                'obs_imo' => 'nullable|string',
                'rf_tem' => 'nullable|numeric',
                'rf_humedad' => 'nullable|numeric',
                'rf_venti' => 'nullable|numeric',
                'cntr_type' => 'nullable|string',
                'retiro_place' => 'nullable|string',
                'q_viajes' => 'nullable|integer',
            ]);
    
            // Buscar la carga y actualizarla
            $carga = Carga::findOrFail($id);
            $cargaOriginal = $carga->getOriginal();
            $carga->update([
                'commodity' => $validatedData['commodity'],
                'trader' => $validatedData['trader'],
                'shipper' => $validatedData['shipper'],
                'custom_agent' => $validatedData['custom_agent'],
                'custom_agent_impo' => $validatedData['custom_agent_impo'],
                'custom_place' => $validatedData['custom_place'],
                'custom_place_impo' => $validatedData['custom_place_impo'],
                'load_place' => $validatedData['load_place'],
                'unload_place' => $validatedData['unload_place'],
                'final_point' => $validatedData['final_point'],
                'oceans_line' => $validatedData['oceans_line'],
                'senasa' => $validatedData['senasa'],
                'tara' => $validatedData['tara'],
                'importador' => $validatedData['importador'],
                'tarifa_ref' => $validatedData['tarifa_ref'],
                'load_date' => $validatedData['load_date'],
                'ref_customer' => $validatedData['ref_customer'],
                'cut_off_fis' => $validatedData['cut_off_fis'],
                'vessel' => $validatedData['vessel'],
                'voyage' => $validatedData['voyage'],
                'observation_customer' => $validatedData['observation_customer'],
                'senasa_string' => $validatedData['senasa_string'],
                'tara_string' => $validatedData['tara_string'],
                'bl_hbl' => $validatedData['bl_hbl'],
                'ex_alto' => $validatedData['ex_alto'],
                'ex_ancho' => $validatedData['ex_ancho'],
                'ex_largo' => $validatedData['ex_largo'],
                'obs_imo' => $validatedData['obs_imo'],
                'rf_tem' => $validatedData['rf_tem'],
                'rf_humedad' => $validatedData['rf_humedad'],
                'rf_venti' => $validatedData['rf_venti'],
            ]);
            
            $changes = $carga->getChanges(); // Obtener los datos que fueron modificados

            // Buscar el CNTR relacionado y actualizarlo
            $cntr = cntr::where('booking', $carga->booking)->firstOrFail();
            $cntrOriginal = $cntr->getOriginal();
            $cntr->update([
                'cntr_type' => $validatedData['cntr_type'],
                'retiro_place' => $validatedData['retiro_place'],
                'q_viajes' => $validatedData['q_viajes'],
            ]);
            $cntrChanges = $cntr->getChanges(); // Obtener los cambios del CNTR

            //Captura las modificaciones
            $modificacionesCarga = [];
            foreach ($changes as $field => $newValue) {
                $modificacionesCarga[$field] = [
                    'original' => $cargaOriginal[$field],
                    'nuevo' => $newValue
                ];
            }
            $modificacionesCntr = [];
            foreach ($cntrChanges as $field => $newValue) {
                $modificacionesCntr[$field] = [
                    'original' => $cntrOriginal[$field],
                    'nuevo' => $newValue
                ];
            }

            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');
            if ($sbx[0]->sandbox == 0) {
                Mail::to(['gzarate@totaltradegroup.com', 'rquero@totaltradegroup.com', 'smingo@totaltradegroup.com'])->cc(['cs.auxiliar@totaltradegroup.com'])->bcc($inboxEmail)->send(new UpdateCarga($modificacionesCntr, $modificacionesCarga,$carga));
            } elseif ($sbx[0]->sandbox == 2) {
                Mail::to(['customer@qa.botzero.com.ar', 'abel.mazzitelli@gmail.com'])->cc(['copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])->bcc($inboxEmail)->send(new UpdateCarga($modificacionesCntr, $modificacionesCarga,$carga));
            }else {
                Mail::to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar', 'equipodemo2@botzero.com.ar'])->cc(['equipodemo2@botzero.com.ar', 'copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])->bcc($inboxEmail)->send(new UpdateCarga($modificacionesCntr, $modificacionesCarga,$carga));
            }
            return response()->json(['message' => 'Carga actualizada exitosamente.'], 200);
    
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
            $carga = Carga::find($id);
            // Verifica si la carga existe
            if (!$carga) {
                return response()->json(['error' => 'La carga no fue encontrada.'], 404);
            }
            // Eliminar los registros de la tabla 'cntr' que coinciden con el 'booking'
            Cntr::where('booking', $carga->booking)->delete();

            // Eliminar los registros de la tabla 'asign' que coinciden con el 'booking'
            Asign::where('booking', $carga->booking)->delete();
            // Elimina la carga
            $carga->delete();

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

            // Buscar una carga con booking o ref_customer, incluyendo eliminados
            $carga = Carga::withTrashed()
                ->where('booking', $request->input('booking'))
                ->first();

            if ($carga) {
                // Si el registro está eliminado, restaurarlo
                if ($carga->trashed()) {
                    $carga->restore();
                    
                }
            } else {
                // Crear una nueva carga si no existe
                $carga = new Carga();
            }

            // Asignar los valores de la solicitud al modelo
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

                $cntr = Cntr::withTrashed()
                    ->where('cntr_number', $numAleatorio)
                    ->first();
                if ($cntr) {
                    // Si el registro está eliminado, restaurarlo
                    if ($cntr->trashed()) {
                        $cntr->restore(); 
                    }
                } else {
                    // Crear una nueva carga si no existe
                    $cntr = new Cntr();
                }
                $cntr->cntr_number = $numAleatorio;
                $cntr->booking = $request->input('booking');
                $cntr->user_cntr = $request->input('user');
                $cntr->retiro_place = $request->input('retiro_place');
                $cntr->cntr_type = $request->input('cntr_type');
                $cntr->company = $request->input('empresa');
                $cntr->save();

                $asing = Asign::withTrashed()
                ->where('cntr_number', $numAleatorio)
                ->first();
                if ($asing) {
                    // Si el registro está eliminado, restaurarlo
                    if ($asing->trashed()) {
                        $asing->restore(); 
                    }
                } else {
                    // Crear una nueva carga si no existe
                    $asing = new Asign();
                }
                $asing->cntr_number = $numAleatorio;
                $asing->booking = $request->input('booking');
                $asing->user = $request->input('user');
                $asing->company = $request->input('empresa');
                $asing->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Carga ingresada correctamente Booking: ' . $request->input('booking'),
                'message_type' => 'success',
                'carga' => $carga,
                'last_id' => $carga->id
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
