<?php

namespace App\Http\Controllers;

use App\Mail\envioInstructivo;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Dompdf\Adapter\PDFLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Svg\Surface\SurfacePDFLib;


class crearpdfController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function carga()
    {
        $cntr_number = $_GET['cntr_number'];

        // revisar si no está generado el Instructivo.
        $conn = mysqli_connect(
            '31.170.161.22',
            'u101685278_sbttl',
            'Pachiman9102$',
            'u101685278_sbttl'
        );

        $query = "SELECT asign.cntr_number, asign.booking, asign.file_instruction, transporte.contacto_logistica_celular FROM asign INNER JOIN transporte ON transporte.razon_social = asign.transport WHERE asign.cntr_number = '$cntr_number'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_array($result);
            
            $booking = $row['booking'];
            $cntr_number = $row['cntr_number'];
            $file = $row['file_instruction'];
            $contacto = $row['contacto_logistica_celular'];
            $file_name = 'instructivo_' . $booking . '_' . $cntr_number . '.pdf';
            $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
            $save_folder = $folder . $file_name;

            if ($file == null) {

                // sino está generado el Instrtructivo lo creamos. 
    
                    $query_file = "SELECT DISTINCT asign.id, asign.transport, asign.transport_agent, asign.observation_load, asign.agent_port, carga.custom_place, carga.load_date, carga.booking, carga.shipper, carga.commodity, carga.load_place, carga.unload_place, carga.cut_off_fis, carga.oceans_line, carga.vessel, carga.voyage, carga.final_point, carga.custom_agent, carga.ref_customer, cntr.cntr_number, cntr.cntr_seal, cntr.cntr_type, cntr.net_weight, cntr.retiro_place, cntr.out_usd, cntr.modo_pago_out, cntr.plazo_de_pago_out, customer_load_place.link_maps, customer_load_place.address, customer_load_place.city FROM carga INNER JOIN cntr INNER JOIN asign INNER JOIN customer_load_place ON carga.booking = cntr.booking AND cntr.cntr_number = asign.cntr_number AND customer_load_place.description = carga.load_place WHERE cntr.cntr_number = '$cntr_number'";                
                    $result_file = mysqli_query($conn, $query_file);
                    
                    if (mysqli_num_rows($result_file) == 1) {
                        $row = mysqli_fetch_array($result_file);
    
                        $data = [
                            'id_asign' => $row['id'],
                            'booking' => $row['booking'],
                            'shipper' => $row['shipper'],
                            'commodity' => $row['commodity'],
                            'load_place' => $row['load_place'],
                            'unload_place' => $row['unload_place'],
                            'cut_off_fis' => $row['cut_off_fis'],
                            'oceans_line' => $row['oceans_line'],
                            'vessel' => $row['vessel'],
                            'voyage' => $row['voyage'],
                            'final_point' => $row['final_point'],
                            'custom_agent' => $row['custom_agent'],
                            'custom_place' => $row['custom_place'],
                            'ref_customer' => $row['ref_customer'],
                            'cntr_number' => $row['cntr_number'],
                            'cntr_seal' => $row['cntr_seal'],
                            'cntr_type' => $row['cntr_type'],
                            'net_weight' => $row['net_weight'],
                            'retiro_place' => $row['retiro_place'],
                            'transport' => $row['transport'],
                            'transport_agent' => $row['transport_agent'],
                            'observation_load' => $row['observation_load'],
                            'agent_port' => $row['agent_port'],
                            'out_usd' => $row['out_usd'],
                            'modo_pago_out' => $row['modo_pago_out'],
                            'plazo_de_pago_out' => $row['plazo_de_pago_out'],
                            'load_date' => $row['load_date'],
                            'link_maps' => $row['link_maps'],
                            'address' => $row['address'],
                            'city' => $row['city'],
    
                            /* 'titulo' => 'Styde.net',
                        'hoy' => Carbon::now()->format('d/m/Y'),
                        'poliza' => $certificado->id,
                        'tomador' => $certificado->nombre,
                        'rut' => $certificado->doc_numero,
                        'domicilio' => $certificado->domicilio,
                        'localidad' => $certificado->localidad,
                        'pais' => $certificado->pais,
                        'fecha_inicio' => $certificado->fecha_emision,
                        'fecha_fin' =>  $certificado->fecha_final , 
                        'asegurados' => Array($asegurados) */
    
                        ];
    
                        // Logica de Creado de Carpera: 
    
    
                        if (!file_exists('instructivos/' . $booking)) {
    
                            /* Si no Existe la Carperta Del booking */
    
                            mkdir('instructivos/' . $booking, 0777, true);
    
                            /* Si existe la Carpeta del Contenedor dentro de la Carpeta del Booking la Asignamos*/
                            if (file_exists('instructivos/' . $booking . '/' . $cntr_number)) {
    
                                $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
                            } else {
    
                                /* Si no existe la Carpeta del Contenedor dentro de la Carpeta del Booking la creamos y  la Asignamos*/
    
    
                                mkdir('instructivos/' . $booking . '/' . $cntr_number, 0777, true);
                                $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
                            }
                        } else {
    
                            /* si Ya existe la Carpeta Booking */
    
                            if (file_exists('instructivos/' . $booking . '/' . $cntr_number)) {
                                /* y existe la carpeta de CNTR la asignamos */
    
                                $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
                            } else {
    
                                /* y si no existe la carpeta de CNTR la creamos y la asignamos */
    
                                mkdir('instructivos/' . $booking . '/' . $cntr_number, 0777, true);
                                $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
                            }
                        }
    
                        $file_name = 'instructivo_' . $booking . '_' . $cntr_number . '.pdf';
    
                        // Ya sabemos que esta creada (o la creamos) entonces creamos variables para usar durante todo el proceso.
    
                        $save_folder = $folder . $file_name;
    
    
                        // Generamos el Archivo PDF
                        $pdf = FacadePdf::loadView('pdf.instructivoCarga', $data);
                        
    
                        file_put_contents($save_folder, $pdf->output());
    
                        $query_upload_file = "UPDATE `asign` SET `file_instruction` = '$file_name' WHERE cntr_number = '$cntr_number'";
                        $result_upload_file = mysqli_query($conn, $query_upload_file);
    
                        return $pdf->download($file_name);
                    }
                }else{
                    
                    return redirect('https://botzero.tech/ttl/views/view_instructivos.php');

                }


        } else {
            
            echo 'no hay instrucción para enviar';

        }


        
        
    }
    public function cargaPorMail( $cntr )
    {
        $cntr_number = $cntr;

       
        // revisar si no está generado el Instructivo.
        $conn = mysqli_connect(
            /* '31.170.161.22',
            'u101685278_ttlgroup',
            'Pachiman9102',
            'u101685278_ttlgroup' */
        
       
            '31.170.161.22',
            'u101685278_sbttl',
            'Pachiman9102$',
            'u101685278_sbttl'
        
    );
       
        $query = DB::table('asign')
        ->select('asign.cntr_number', 'asign.booking', 'asign.file_instruction','transporte.contacto_logistica_celular')
        ->join('transporte', 'transporte.razon_social', '=', 'asign.transport')->where('asign.cntr_number', '=', $cntr_number)->get();
       
        if ($query->count() == 1) {
          
            $booking = $query[0]->booking;
            $cntr_number = $query[0]->cntr_number;
            $file = $query[0]->file_instruction;
            $contacto = $query[0]->contacto_logistica_celular;
            $file_name = 'instructivo_' . $booking . '_' . $cntr_number . '.pdf';
            $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
            $save_folder = $folder . $file_name;
            
            if ($file == null) {
                // sino está generado el Instrtructivo lo creamos. 

                $query_file = "SELECT DISTINCT asign.id, asign.transport, asign.transport_agent, asign.observation_load, asign.agent_port, carga.custom_place, carga.load_date, carga.booking, carga.shipper, carga.commodity, carga.load_place, carga.unload_place, carga.cut_off_fis, carga.oceans_line, carga.vessel, carga.voyage, carga.final_point, carga.custom_agent, carga.ref_customer, cntr.cntr_number, cntr.cntr_seal, cntr.cntr_type, cntr.net_weight, cntr.retiro_place, cntr.out_usd, cntr.modo_pago_out, cntr.plazo_de_pago_out, customer_load_place.link_maps, customer_load_place.address, customer_load_place.city FROM carga INNER JOIN cntr INNER JOIN asign INNER JOIN customer_load_place ON carga.booking = cntr.booking AND cntr.cntr_number = asign.cntr_number AND customer_load_place.description = carga.load_place WHERE cntr.cntr_number = '$cntr_number'";                
                $result_file = mysqli_query($conn, $query_file);
                    
                if (mysqli_num_rows($result_file) == 1) {
                    $row = mysqli_fetch_array($result_file);
                    $date = Carbon::now()->format('d/m/Y');

                    $data = [
                        'id_asign' => $row['id'],
                        'booking' => $row['booking'],
                        'shipper' => $row['shipper'],
                        'commodity' => $row['commodity'],
                        'load_place' => $row['load_place'],
                        'unload_place' => $row['unload_place'],
                        'cut_off_fis' => $row['cut_off_fis'],
                        'oceans_line' => $row['oceans_line'],
                        'vessel' => $row['vessel'],
                        'voyage' => $row['voyage'],
                        'final_point' => $row['final_point'],
                        'custom_agent' => $row['custom_agent'],
                        'custom_place' => $row['custom_place'],
                        'ref_customer' => $row['ref_customer'],
                        'cntr_number' => $row['cntr_number'],
                        'cntr_seal' => $row['cntr_seal'],
                        'cntr_type' => $row['cntr_type'],
                        'net_weight' => $row['net_weight'],
                        'retiro_place' => $row['retiro_place'],
                        'transport' => $row['transport'],
                        'transport_agent' => $row['transport_agent'],
                        'observation_load' => $row['observation_load'],
                        'agent_port' => $row['agent_port'],
                        'out_usd' => $row['out_usd'],
                        'modo_pago_out' => $row['modo_pago_out'],
                        'plazo_de_pago_out' => $row['plazo_de_pago_out'],
                        'load_date' => $row['load_date'],
                        'link_maps' => $row['link_maps'],
                        'address' => $row['address'],
                        'city' => $row['city'],
                        'date'=> $date

                    ];

                    // Logica de Creado de Carpera: 

                    if (!file_exists('instructivos/' . $booking)) {

                        /* Si no Existe la Carperta Del booking */

                        mkdir('instructivos/' . $booking, 0777, true);

                        /* Si existe la Carpeta del Contenedor dentro de la Carpeta del Booking la Asignamos*/
                        if (file_exists('instructivos/' . $booking . '/' . $cntr_number)) {

                            $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
                        } else {

                            /* Si no existe la Carpeta del Contenedor dentro de la Carpeta del Booking la creamos y  la Asignamos*/


                            mkdir('instructivos/' . $booking . '/' . $cntr_number, 0777, true);
                            $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
                        }
                    } else {

                        /* si Ya existe la Carpeta Booking */

                        if (file_exists('instructivos/' . $booking . '/' . $cntr_number)) {
                            /* y existe la carpeta de CNTR la asignamos */

                            $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
                        } else {

                            /* y si no existe la carpeta de CNTR la creamos y la asignamos */

                            mkdir('instructivos/' . $booking . '/' . $cntr_number, 0777, true);
                            $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
                        }
                    }

                    $file_name = 'instructivo_' . $booking . '_' . $cntr_number . '.pdf';

                    // Ya sabemos que esta creada (o la creamos) entonces creamos variables para usar durante todo el proceso.

                    $save_folder = $folder . $file_name;

                    // Generamos el Archivo PDF
                    $pdf = FacadePdf::loadView('pdf.instructivoCarga', $data);
                    
                    file_put_contents($save_folder, $pdf->output());

                    $query_upload_file = "UPDATE `asign` SET `file_instruction` = '$file_name' WHERE cntr_number = '$cntr_number'";
                    mysqli_query($conn, $query_upload_file);
                    
                    $qAsing = DB::table('asign')->select('transport')->where('cntr_number','=',$cntr_number)->get();
                    $empresa = $qAsing[0]->transport;
                    $qmail = DB::table('transporte')->where('razon_social','=',$empresa)->select('contacto_logistica_mail')->get();
                    $mail = $qmail[0]->contacto_logistica_mail;
                    $mail = Mail::to($mail)->cc('totaltrade@botzero.ar')->send(new envioInstructivo($data)); 
                    
                    return 'ok';

                }else{

                    return redirect('https://botzero.tech/ttl/views/view_instructivos.php');

                }

            } else {
                   
                    $query_file = DB::table('carga')
                    ->distinct('asign.id','carga.load_date','carga.booking','carga.shipper','carga.commodity','carga.load_place','cntr.cntr_number','cntr.cntr_type')
                    ->join('cntr','carga.booking', '=','cntr.booking')
                    ->join('asign','cntr.cntr_number', '=', 'asign.cntr_number')
                    ->where('cntr.cntr_number', '=', $cntr_number)->get();

                    $date = Carbon::now()->format('d/m/Y');
                    if ($query_file->count() == 1) {

                        $data = [

                            'id_asign' => $query_file[0]->id,
                            'booking' => $query_file[0]->booking,
                            'load_place' => $query_file[0]->load_place,
                            'cntr_type' => $query_file[0]->cntr_type,
                            'cntr_number' => $query_file[0]->cntr_number,
                            'load_date' => $query_file[0]->load_date,
                            'shipper' => $query_file[0]->shipper,
                            'commodity' => $query_file[0]->commodity,
                            'date'=> $date
                        
                        ];

                        $qempresa = DB::table('carga')->select('empresa')->where('booking','=',$booking)->get();
                        $empresa = $qempresa[0]->empresa;
                        $qmail = DB::table('empresas')->where('razon_social','=',$empresa)->select('mail_logistic')->get();
                        $mail = $qmail[0]->mail_logistic;

                        $mail = Mail::to($mail)->cc('totaltrade@botzero.ar')->send(new envioInstructivo($data)); 
                        return 'ok';
                }else{
                    return 'no hay carga asignada';
                }
            } 
        } else {
            
            return 'no hay instrucción para enviar';
        }
        
    }


    public function vacio()
    {
       
        // me traigo la informacion segun ID seleccionada. 

        $id_cntr = $_GET['id_cntr'];

        $conn = mysqli_connect(
            '31.170.161.22',
            'u101685278_sbttl',
            'Pachiman9102$',
            'u101685278_sbttl'
        );

        $query_file = "SELECT carga.booking, carga.cliente, carga.vessel, carga.voyage, carga.unload_place, carga.final_point, carga.commodity, cntr.retiro_place, carga.oceans_line, cntr.cntr_type FROM carga INNER JOIN cntr ON carga.booking = cntr.booking WHERE cntr.id_cntr = '$id_cntr'";
        $result_file = mysqli_query($conn, $query_file);

        if (mysqli_num_rows($result_file) == 1) {
            $row = mysqli_fetch_array($result_file);

            // armamos el Contenido del Instructivo.

            $data = [
                'booking' => $row['booking'],
                'commodity' => $row['commodity'],
                'unload_place' => $row['unload_place'],
                'oceans_line' => $row['oceans_line'],
                'vessel' => $row['vessel'],
                'voyage' => $row['voyage'],
                'final_point' => $row['final_point'],
                'cntr_type' => $row['cntr_type'],
                'retiro_place' => $row['retiro_place'],
                'cliente' => $row['cliente']
                /* 'titulo' => 'Styde.net',
                'hoy' => Carbon::now()->format('d/m/Y'),
                'poliza' => $certificado->id,
                'tomador' => $certificado->nombre,
                'rut' => $certificado->doc_numero,
                'domicilio' => $certificado->domicilio,
                'localidad' => $certificado->localidad,
                'pais' => $certificado->pais,
                'fecha_inicio' => $certificado->fecha_emision,
                'fecha_fin' =>  $certificado->fecha_final , 
                'asegurados' => Array($asegurados) */

            ];
        }
       
        $pdf = FacadePdf::loadView('pdf.instructivoVacio', $data);
        $archivo = 'Instruccion_retiroVacio' . $row['booking'] . '.pdf';

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
