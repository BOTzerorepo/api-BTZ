<?php

namespace App\Http\Controllers;

use App\Mail\envioInstructivo;
use App\Models\logapi;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Dompdf\Adapter\PDFLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Svg\Surface\SurfacePDFLib;


class verpdfController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function carga($cntr_number)
    {
        $variables = DB::table('variables')->select('api')->get();
        $base = $variables[0]->api;

        $respuesta = DB::table('asign')
            ->join('transports', 'transports.razon_social', '=', 'asign.transport')
            ->where('asign.cntr_number', '=', $cntr_number)
            ->select('asign.cntr_number', 'asign.booking', 'asign.file_instruction', 'transports.contacto_logistica_celular')->get();
        $row = $respuesta[0];

        if ($respuesta->count() == 1) {

            $booking = $row->booking;
            $cntr_number = $row->cntr_number;
            $file = $row->file_instruction;
            $contacto = $row->contacto_logistica_celular;
            $file_name = 'instructivo_' . $booking . '_' . $cntr_number . '.pdf';
            $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
            $save_folder = $folder . $file_name;

                // sino está generado el Instrtructivo lo creamos. 
            $respuesta_file = DB::table('carga')
                ->join('cntr', 'carga.booking', '=', 'cntr.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->leftJoin('customer_load_places', 'customer_load_places.description', '=', 'carga.load_place')
                ->leftJoin('customer_unload_places', 'customer_unload_places.description', '=', 'carga.unload_place')
                ->leftJoin('razon_social', 'asign.sub_empresa', '=', 'razon_social.title')
                ->leftJoin('agencies', 'asign.agent_port', '=', 'agencies.description')
                ->leftJoin('customer_agents as aduanaExpo', 'carga.custom_agent', '=', 'aduanaExpo.razon_social')
                ->leftJoin('customer_agents as aduanaImpo', 'carga.custom_agent_impo', '=', 'aduanaImpo.razon_social')
                ->where('cntr.cntr_number', '=', $cntr_number)
                ->distinct()
                ->get([
                    'asign.id',
                    'razon_social.img', 'razon_social.cuit', 'razon_social.title',
                    'asign.transport', 'asign.transport_agent', 'asign.observation_load', 'asign.agent_port',
                    'carga.custom_place', 'carga.bl_hbl', 'carga.senasa', 'carga.senasa_string', 'carga.type', 'carga.ref_customer', 'carga.load_date', 'carga.booking', 'carga.importador', 'carga.shipper', 'carga.commodity', 'carga.load_place', 'carga.unload_place', 'carga.cut_off_fis', 'carga.oceans_line', 'carga.vessel', 'carga.voyage', 'carga.final_point', 'carga.observation_customer', 'carga.custom_agent', 'carga.custom_place_impo', 'carga.ref_customer',
                    'cntr.cntr_number', 'cntr.cntr_seal', 'cntr.cntr_type', 'cntr.net_weight', 'cntr.retiro_place', 'cntr.out_usd', 'cntr.modo_pago_out', 'cntr.oservation_out', 
                    'customer_load_places.link_maps', 'customer_load_places.address', 'customer_load_places.city',
                    'agencies.observation_gral',
                    'aduanaExpo.mail', 'aduanaExpo.phone',
                    'aduanaImpo.razon_social as aduanaImpo_agent', 'aduanaImpo.mail as aduanaImpo_mail', 'aduanaImpo.phone as aduanaImpo_phone',
                    'customer_unload_places.description as descarga_place', 'customer_unload_places.address as descarga_address', 'customer_unload_places.city as descarga_city', 'customer_unload_places.link_maps as descarga_link'
                ]);

                $row = $respuesta_file[0];

                $weekMap = [
                    0 => 'Domingo',
                    1 => 'Lunes',
                    2 => 'Martes',
                    3 => 'Miércoles',
                    4 => 'Jueves',
                    5 => 'Viernes',
                    6 => 'Sábado',
                ];
                
                $day = Carbon::parse($row->load_date)->dayOfWeek;
                $date = Carbon::parse($row->load_date)->format('d-m-Y');
                $dayW = $weekMap[$day];
                $load_date = $dayW . ' ' . $date;

                if ($respuesta_file->count() >= 1) {

                    if ($row->type == 'Puesta FOB') {

                        $data = [
                            'id_asign' => $row->id,
                            'img' => $base . '/public/image/empresas/' . $row->img,
                            'cuit' => $row->cuit,
                            'title' => $row->title,
                            'booking' => $row->booking,
                            'shipper' => $row->shipper,
                            'commodity' => $row->commodity,
                            'load_place' => $row->load_place,
                            'unload_place' => $row->unload_place,
                            'cut_off_fis' => $row->cut_off_fis,
                            'oceans_line' => $row->oceans_line,
                            'vessel' => $row->vessel,
                            'voyage' => $row->voyage,
                            'final_point' => $row->final_point,
                            'custom_agent' => $row->custom_agent,
                            'custom_agent_mail' => $row->mail,
                            'custom_agent_phone' => $row->phone,
                            'custom_place' => $row->custom_place,
                            'ref_customer' => $row->ref_customer,
                            'cntr_number' => $row->cntr_number,
                            'cntr_seal' => $row->cntr_seal,
                            'cntr_type' => $row->cntr_type,
                            'net_weight' => $row->net_weight,
                            'retiro_place' => $row->retiro_place,
                            'transport' => $row->transport,
                            'transport_agent' => $row->transport_agent,
                            'observation_load' => $row->observation_load,
                            'agent_port' => $row->agent_port,
                            'out_usd' => $row->out_usd,
                            'observation_out' => $row->observation_out,
                            'load_date' => $load_date,
                            'link_maps' => $row->link_maps,
                            'address' => $row->address,
                            'city' => $row->city,
                            'observaciones_agencia' => $row->observation_gral,
                            'observation_customer' => $row->observation_customer,

                        ];

                        return view('pdf.instructivoCargaFOB', $data);

                    } elseif ($row->type == 'Expo Maritima') {

                        $data = [
                            'id_asign' => $row->id,
                            'img' => $base . '/public/image/empresas/' . $row->img,
                            'cuit' => $row->cuit,
                            'title' => $row->title,
                            'booking' => $row->booking,
                            'shipper' => $row->shipper,
                            'commodity' => $row->commodity,
                            'load_place' => $row->load_place,
                            'unload_place' => $row->unload_place,
                            'cut_off_fis' => $row->cut_off_fis,
                            'oceans_line' => $row->oceans_line,
                            'vessel' => $row->vessel,
                            'voyage' => $row->voyage,
                            'final_point' => $row->final_point,
                            'custom_agent' => $row->custom_agent,
                            'custom_agent_mail' => $row->mail,
                            'custom_agent_phone' => $row->phone,
                            'custom_place' => $row->custom_place,
                            'ref_customer' => $row->ref_customer,
                            'cntr_number' => $row->cntr_number,
                            'cntr_seal' => $row->cntr_seal,
                            'cntr_type' => $row->cntr_type,
                            'net_weight' => $row->net_weight,
                            'retiro_place' => $row->retiro_place,
                            'transport' => $row->transport,
                            'transport_agent' => $row->transport_agent,
                            'observation_load' => $row->observation_load,
                            'agent_port' => $row->agent_port,
                            'out_usd' => $row->out_usd,
                            'observation_outt' => $row->observation_out,
                            'load_date' => $load_date,
                            'link_maps' => $row->link_maps,
                            'address' => $row->address,
                            'city' => $row->city,
                            'observaciones_agencia' => $row->observation_gral,
                            'observation_customer' => $row->observation_customer,

                        ];

                        
                        return view('pdf.instructivoCargaExpoMar', $data);
    

                    } elseif ($row->type == 'Expo Terrestre') {

                        $data = [

                            'id_asign' => $row->id,
                            'img' => $base . '/public/image/empresas/' . $row->img,
                            'cuit' => $row->cuit,
                            'title' => $row->title,

                            'booking' => $row->booking,
                            'shipper' => $row->shipper,
                            'commodity' => $row->commodity,
                            'load_place' => $row->load_place,
                            'unload_place' => $row->unload_place,
                            'cut_off_fis' => $row->cut_off_fis,
                            'oceans_line' => $row->oceans_line,
                            'vessel' => $row->vessel,
                            'voyage' => $row->voyage,
                            'final_point' => $row->final_point,
                            'custom_agent' => $row->custom_agent,
                            'custom_agent_impo' => $row->aduanaImpo_agent,

                            'custom_agent_mail' => $row->mail,
                            'custom_agent_mail_impo' => $row->aduanaImpo_mail,

                            'custom_agent_phone' => $row->phone,
                            'custom_agent_phone_impo' => $row->aduanaImpo_phone,

                            'custom_place' => $row->custom_place,
                            'custom_place_impo' => $row->custom_place_impo,

                            'ref_customer' => $row->ref_customer,
                            'importador' => $row->importador,

                            'cntr_number' => $row->cntr_number,
                            'cntr_seal' => $row->cntr_seal,
                            'cntr_type' => $row->cntr_type,
                            'net_weight' => $row->net_weight,
                            'retiro_place' => $row->retiro_place,
                            'transport' => $row->transport,
                            'transport_agent' => $row->transport_agent,
                            'observation_load' => $row->observation_load,
                            'agent_port' => $row->agent_port,
                            'out_usd' => $row->out_usd,
                         
                            'observation_out' => $row->observation_out,
                            'load_date' => $load_date,
                            'link_maps' => $row->link_maps,
                            'address' => $row->address,
                            'city' => $row->city,
                            'observaciones_agencia' => $row->observation_gral,
                            'observation_customer' => $row->observation_customer,
                            "descarga_place" => $row->descarga_place,
                            "descarga_address" => $row->descarga_address,
                            "descarga_city" => $row->descarga_city,
                            "descarga_link" => $row->descarga_link

                        ];


                        
                        return view('pdf.instructivoCargaExpoTer', $data);
                        

                    } elseif ($row->type == 'Impo Maritima') {

                        $data = [

                            'id_asign' => $row->id,
                            'img' => $base . '/public/image/empresas/' . $row->img,
                            'cuit' => $row->cuit,
                            'title' => $row->title,

                            'booking' => $row->booking,
                            'bl_hbl' => $row->bl_hbl,
                            'senasa' => $row->senasa,
                            'senasa_string' => $row->senasa_string,
                            'shipper' => $row->shipper,
                            'commodity' => $row->commodity,
                            'load_place' => $row->load_place,
                            'unload_place' => $row->unload_place,
                            'cut_off_fis' => $row->cut_off_fis,
                            'oceans_line' => $row->oceans_line,
                            'vessel' => $row->vessel,
                            'voyage' => $row->voyage,
                            'final_point' => $row->final_point,
                            'custom_agent' => $row->custom_agent,
                            'custom_agent_impo' => $row->aduanaImpo_agent,

                            'custom_agent_mail' => $row->mail,
                            'custom_agent_mail_impo' => $row->aduanaImpo_mail,

                            'custom_agent_phone' => $row->phone,
                            'custom_agent_phone_impo' => $row->aduanaImpo_phone,

                            'custom_place' => $row->custom_place,
                            'custom_place_impo' => $row->custom_place_impo,

                            'ref_customer' => $row->ref_customer,
                            'importador' => $row->importador,

                            'cntr_number' => $row->cntr_number,
                            'cntr_seal' => $row->cntr_seal,
                            'cntr_type' => $row->cntr_type,
                            'net_weight' => $row->net_weight,
                            'retiro_place' => $row->retiro_place,
                            'transport' => $row->transport,
                            'transport_agent' => $row->transport_agent,
                            'observation_load' => $row->observation_load,
                            'agent_port' => $row->agent_port,
                            'out_usd' => $row->out_usd,
                           
                            'observation_out' => $row->observation_out,
                            'load_date' => $load_date,
                            'link_maps' => $row->link_maps,
                            'address' => $row->address,
                            'city' => $row->city,
                            'observaciones_agencia' => $row->observation_gral,
                            'observation_customer' => $row->observation_customer,
                            "descarga_place" => $row->descarga_place,
                            "descarga_address" => $row->descarga_address,
                            "descarga_city" => $row->descarga_city,
                            "descarga_link" => $row->descarga_link

                        ];


                        
                        return view('pdf.instructivoCargaImpoMar', $data);
                      

                    } elseif ($row->type == 'Impo Terrestre') {

                        $data = [

                            'id_asign' => $row->id,
                            'img' => $base . '/public/image/empresas/' . $row->img,
                            'cuit' => $row->cuit,
                            'title' => $row->title,

                            'booking' => $row->booking,
                            'bl_hbl' => $row->bl_hbl,
                            'senasa' => $row->senasa,
                            'senasa_string' => $row->senasa_string,
                            'shipper' => $row->shipper,
                            'commodity' => $row->commodity,
                            'load_place' => $row->load_place,
                            'unload_place' => $row->unload_place,
                            'cut_off_fis' => $row->cut_off_fis,
                            'oceans_line' => $row->oceans_line,
                            'vessel' => $row->vessel,
                            'voyage' => $row->voyage,
                            'final_point' => $row->final_point,
                            'custom_agent' => $row->custom_agent,
                            'custom_agent_impo' => $row->aduanaImpo_agent,

                            'custom_agent_mail' => $row->mail,
                            'custom_agent_mail_impo' => $row->aduanaImpo_mail,

                            'custom_agent_phone' => $row->phone,
                            'custom_agent_phone_impo' => $row->aduanaImpo_phone,

                            'custom_place' => $row->custom_place,
                            'custom_place_impo' => $row->custom_place_impo,

                            'ref_customer' => $row->ref_customer,
                            'importador' => $row->importador,

                            'cntr_number' => $row->cntr_number,
                            'cntr_seal' => $row->cntr_seal,
                            'cntr_type' => $row->cntr_type,
                            'net_weight' => $row->net_weight,
                            'retiro_place' => $row->retiro_place,
                            'transport' => $row->transport,
                            'transport_agent' => $row->transport_agent,
                            'observation_load' => $row->observation_load,
                            'agent_port' => $row->agent_port,
                            'out_usd' => $row->out_usd,
                            
                            'observation_out' => $row->observation_out,
                            'load_date' => $load_date,
                            'link_maps' => $row->link_maps,
                            'address' => $row->address,
                            'city' => $row->city,
                            'observaciones_agencia' => $row->observation_gral,
                            'observation_customer' => $row->observation_customer,
                            "descarga_place" => $row->descarga_place,
                            "descarga_address" => $row->descarga_address,
                            "descarga_city" => $row->descarga_city,
                            "descarga_link" => $row->descarga_link

                        ];

                       return view('pdf.instructivoCargaImpoTer', $data);
                       

                    } elseif ($row->type == 'Nacional') {

                        $data = [

                            'id_asign' => $row->id,
                            'img' => $base . '/public/image/empresas/' . $row->img,
                            'cuit' => $row->cuit,
                            'title' => $row->title,

                            'booking' => $row->booking,
                            
                            'senasa' => $row->senasa,
                            'senasa_string' => $row->senasa_string,
                            'shipper' => $row->shipper,
                            'commodity' => $row->commodity,
                            'load_place' => $row->load_place,
                            'unload_place' => $row->unload_place,
                            'cut_off_fis' => $row->cut_off_fis,
                            
                            

                            'ref_customer' => $row->ref_customer,
                            
                            'cntr_number' => $row->cntr_number,
                        
                            'cntr_type' => $row->cntr_type,
                            'net_weight' => $row->net_weight,
                            
                            'transport' => $row->transport,
                            
                            'observation_load' => $row->observation_load,
                           
                            'out_usd' => $row->out_usd,
                            
                            'load_date' => $load_date,
                            'link_maps' => $row->link_maps,
                            'address' => $row->address,
                            'city' => $row->city,
                            ' observation_out' => $row-> observation_out,

                           
                            'observation_customer' => $row->observation_customer,
                            "descarga_place" => $row->descarga_place,
                            "descarga_address" => $row->descarga_address,
                            "descarga_city" => $row->descarga_city,
                            "descarga_link" => $row->descarga_link

                        ];


                        return view('pdf.instructivoNacional', $data);
                        
                    
                    }
                } else {

                    return '#ERROR - No hay datos para mostrar';

                }
            } else {

                return '#ERROR - No hay coincidecias';

            }   
       
    }
    public function cargaPorMail($cntr)
    {

        // TOMO EL CNTR 

        $cntr_number = $cntr;

        $query = DB::table('asign')
            ->select('asign.cntr_number', 'asign.booking', 'asign.file_instruction', 'transports.contacto_logistica_celular')
            ->join('transports', 'transports.razon_social', '=', 'asign.transport')->where('asign.cntr_number', '=', $cntr_number)->get();

        // REVISO QUE HAYA ALGUNA ASIGNACIÓN 

        if ($query->count() == 1) {


            $cntr_number = $query[0]->cntr_number;

            // BUSCO DATOS PARA ARMAR CUERPO DEL MAIL 

            $respuesta_file = DB::table('carga')
                ->join('cntr', 'carga.booking', '=', 'cntr.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->join('customer_load_places', 'customer_load_places.description', '=', 'carga.load_place')
                ->where('cntr.cntr_number', '=', $cntr_number)
                ->distinct()
                ->get(['asign.id', 'asign.transport', 'asign.transport_agent', 'asign.observation_load', 'asign.agent_port', 'carga.custom_place', 'carga.load_date', 'carga.booking', 'carga.shipper', 'carga.commodity', 'carga.load_place', 'carga.unload_place', 'carga.cut_off_fis', 'carga.oceans_line', 'carga.vessel', 'carga.voyage', 'carga.final_point', 'carga.custom_agent', 'carga.ref_customer', 'cntr.cntr_number', 'cntr.cntr_seal', 'cntr.cntr_type', 'cntr.net_weight', 'cntr.retiro_place', 'cntr.out_usd', 'cntr.modo_pago_out', 'cntr.plazo_de_pago_out', 'customer_load_places.link_maps', 'customer_load_places.address', 'customer_load_places.city']);
            $row = $respuesta_file[0];

            $weekMap = [
                0 => 'Domingo',
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
            ];
            $day = Carbon::parse($row->load_date)->dayOfWeek;
            $date = Carbon::parse($row->load_date)->format('d-m-Y');
            $dayW = $weekMap[$day];
            $load_date = $dayW . ' ' . $date;

            // ARMO DATOS PARA ENVIAR EN CUERPO DEL MAIL

            $data = [
                'id_asign' => $row->id,
                'booking' => $row->booking,
                'shipper' => $row->shipper,
                'commodity' => $row->commodity,
                'load_place' => $row->load_place,
                'unload_place' => $row->unload_place,
                'cut_off_fis' => $row->cut_off_fis,
                'oceans_line' => $row->oceans_line,
                'vessel' => $row->vessel,
                'voyage' => $row->voyage,
                'final_point' => $row->final_point,
                'custom_agent' => $row->custom_agent,
                'custom_place' => $row->custom_place,
                'ref_customer' => $row->ref_customer,
                'cntr_number' => $row->cntr_number,
                'cntr_seal' => $row->cntr_seal,
                'cntr_type' => $row->cntr_type,
                'net_weight' => $row->net_weight,
                'retiro_place' => $row->retiro_place,
                'transport' => $row->transport,
                'transport_agent' => $row->transport_agent,
                'observation_load' => $row->observation_load,
                'agent_port' => $row->agent_port,
                'out_usd' => $row->out_usd,
                'modo_pago_out' => $row->modo_pago_out,
                'plazo_de_pago_out' => $row->plazo_de_pago_out,
                'load_date' => $load_date,
                'link_maps' => $row->link_maps,
                'address' => $row->address,
                'city' => $row->city,
                'date' => $date

            ];

            // BUSCO DATOS DEL TRANSPORTE DONDE VOY A ENVIAR EL CORREO

            $qAsing = DB::table('asign')->select('transport')->where('cntr_number', '=', $cntr_number)->get();
            $empresa = $qAsing[0]->transport;
            $qmail = DB::table('transports')->where('razon_social', '=', $empresa)->select('contacto_logistica_mail')->get();
            $mail = $qmail[0]->contacto_logistica_mail;

            // ENVIAMOS MAIL 

            $mail = Mail::to($mail)->cc('totaltrade@botzero.ar')->send(new envioInstructivo($data));

            return 'ok';
        } else {

            return 'no hay asignacio para ese camion';
        }
    }


    public function vacio($id_cntr)
    {

        $respuesta_file = DB::table('carga')
            ->select('carga.shipper', 'carga.booking', 'carga.vessel', 'carga.voyage', 'carga.unload_place', 'carga.final_point', 'carga.commodity', 'cntr.retiro_place', 'carga.oceans_line', 'cntr.cntr_type')
            ->join('cntr', 'carga.booking', '=', 'cntr.booking')
            ->where('cntr.id_cntr', '=', $id_cntr)
            ->get();

        $row = $respuesta_file[0];

        if ($respuesta_file->count() == 1) {


            $data = [
                'booking' => $row->booking,
                'commodity' => $row->commodity,
                'unload_place' => $row->unload_place,
                'oceans_line' => $row->oceans_line,
                'vessel' => $row->vessel,
                'voyage' => $row->voyage,
                'final_point' => $row->final_point,
                'cntr_type' => $row->cntr_type,
                'retiro_place' => $row->retiro_place,
                'cliente' => $row->shipper

            ];
        }

        $pdf = FacadePdf::loadView('pdf.instructivoVacio', $data);
        $archivo = 'Instruccion_retiroVacio' . $row->booking . '.pdf';

        file_put_contents('vacios/' . $archivo, $pdf->output());

        return $pdf->download($archivo);
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
