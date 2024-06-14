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


class crearpdfController extends Controller
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

        $logApi = new logapi();
        $logApi->detalle = 'Consulta Variable api base = :' . $base;
        $logApi->user = 'carga(' . $cntr_number . ')';
        $logApi->save();

        $respuesta = DB::table('asign')
            ->join('transports', 'transports.razon_social', '=', 'asign.transport')
            ->where('asign.cntr_number', '=', $cntr_number)
            ->select('asign.cntr_number', 'asign.booking', 'asign.file_instruction', 'transports.contacto_logistica_celular')->get();
        $row = $respuesta[0];

        $logApi = new logapi();
        $logApi->detalle = 'Respuesta api count = :' . $respuesta->count();
        $logApi->user = 'carga(' . $cntr_number . ')';
        $logApi->save();

        if ($respuesta->count() == 1) {

            $booking = $row->booking;
            $cntr_number = $row->cntr_number;
            $file = $row->file_instruction;
            $contacto = $row->contacto_logistica_celular;
            $file_name = 'instructivo_' . $booking . '_' . $cntr_number . '.pdf';
            $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
            $save_folder = $folder . $file_name;

            $logApi = new logapi();
            $logApi->detalle = 'Respuesta file = :' . $file;
            $logApi->user = 'carga(' . $cntr_number . ')';
            $logApi->save();

            if ($file == null) {

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
                        'carga.custom_place', 'carga.bl_hbl', 'carga.senasa', 'carga.senasa_string', 'carga.type', 'carga.ref_customer', 'carga.load_date', 'carga.booking', 'carga.importador', 'carga.shipper', 'carga.commodity', 'carga.load_place', 'carga.unload_place', 'carga.cut_off_fis', 'carga.oceans_line', 'carga.vessel', 'carga.voyage', 'carga.final_point', 'carga.observation_customer', 'carga.custom_agent', 'carga.custom_place_impo', 'carga.ref_customer', 'carga.ex_alto', 'carga.ex_ancho', 'carga.ex_largo', 'carga.obs_imo', 'carga.rf_tem', 'carga.rf_humedad', 'carga.rf_venti',
                        'cntr.cntr_number', 'cntr.confirmacion', 'cntr.cntr_seal', 'cntr.cntr_type', 'cntr.net_weight', 'cntr.retiro_place', 'cntr.out_usd', 'cntr.observation_out',
                        'customer_load_places.link_maps', 'customer_load_places.address', 'customer_load_places.city',
                        'agencies.observation_gral',
                        'aduanaExpo.mail', 'aduanaExpo.phone',
                        'aduanaImpo.razon_social as aduanaImpo_agent', 'aduanaImpo.mail as aduanaImpo_mail', 'aduanaImpo.phone as aduanaImpo_phone',
                        'customer_unload_places.description as descarga_place', 'customer_unload_places.address as descarga_address', 'customer_unload_places.city as descarga_city', 'customer_unload_places.link_maps as descarga_link'
                    ]);

                $row = $respuesta_file[0];

                $logApi = new logapi();
                $logApi->detalle = 'Respuesta file = :' . $file;
                $logApi->user = 'Respuesta Consulta para armar datos de intructivos count: ' . $respuesta_file->count();
                $logApi->save();

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

                        $logApi = new logapi();
                        $logApi->detalle = 'Respuesta file = :' . $file;
                        $logApi->user = 'Ingreso en Puesta FOB';
                        $logApi->save();

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
                            'confirmacion' => $row->confirmacion,
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
                            'ex_alto' => $row->ex_alto,
                            'ex_ancho' => $row->ex_ancho,
                            'ex_largo' => $row->ex_largo,
                            'obs_imo' => $row->obs_imo,
                            'rf_tem' => $row->rf_tem,
                            'rf_humedad' => $row->rf_humedad,
                            'rf_venti' => $row->rf_venti

                        ];

                        if (!file_exists('instructivos/' . $booking)) {

                            $logApi = new logapi();
                            $logApi->detalle = 'Respuesta file = :' . $file;
                            $logApi->user = 'no existe la Carpeta instructivos/' . $booking;
                            $logApi->save();

                            /* Si no Existe la Carperta Del booking */

                            mkdir('instructivos/' . $booking, 0777, true);

                            /* Si existe la Carpeta del Contenedor dentro de la Carpeta del Booking la Asignamos*/
                            if (file_exists('instructivos/' . $booking . '/' . $cntr_number)) {

                                $folder = 'instructivos/' . $booking . '/' . $cntr_number . '/';
                            } else {

                                /* Si no existe la Carpeta del Contenedor dentro de la Carpeta del Booking la creamos y  la Asignamos*/

                                $logApi = new logapi();
                                $logApi->detalle = 'Respuesta file = :' . $file;
                                $logApi->user = 'no existe la Carpeta instructivos/' . $booking . '/' . $cntr_number;
                                $logApi->save();

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

                        $logApi = new logapi();
                        $logApi->detalle = 'Respuesta file = :' . $file;
                        $logApi->user = 'La Carpeta fue creada o ya existia:' . $folder;
                        $logApi->save();


                        $file_name = 'instructivo_' . $booking . '_' . $cntr_number . '.pdf';

                        // Ya sabemos que esta creada (o la creamos) entonces creamos variables para usar durante todo el proceso.

                        $save_folder = $folder . $file_name;

                        $logApi = new logapi();
                        $logApi->detalle = 'Respuesta file = :' . $file;
                        $logApi->user = 'Vamos a guardar el Archivo aca:' .  $save_folder;
                        $logApi->save();

                        // Generamos el Archivo PDF
                        $pdf = FacadePdf::loadView('pdf.instructivoCargaFOB', $data);
                        file_put_contents($save_folder, $pdf->output());

                        $respuesta_update = DB::table('asign')
                            ->where('cntr_number', $cntr_number)
                            ->update(['file_instruction' => $file_name]);

                        return $pdf->download($file_name);
                        
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
                            'confirmacion' => $row->confirmacion,

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
                            'ex_alto' => $row->ex_alto,
                            'ex_ancho' => $row->ex_ancho,
                            'ex_largo' => $row->ex_largo,
                            'obs_imo' => $row->obs_imo,
                            'rf_tem' => $row->rf_tem,
                            'rf_humedad' => $row->rf_humedad,
                            'rf_venti' => $row->rf_venti

                        ];

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
                        $pdf = FacadePdf::loadView('pdf.instructivoCargaExpoMar', $data);
                        file_put_contents($save_folder, $pdf->output());

                        $respuesta_update = DB::table('asign')
                            ->where('cntr_number', $cntr_number)
                            ->update(['file_instruction' => $file_name]);

                        return $pdf->download($file_name);
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
                            'confirmacion' => $row->confirmacion,

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
                            "descarga_link" => $row->descarga_link,
                            'ex_alto' => $row->ex_alto,
                            'ex_ancho' => $row->ex_ancho,
                            'ex_largo' => $row->ex_largo,
                            'obs_imo' => $row->obs_imo,
                            'rf_tem' => $row->rf_tem,
                            'rf_humedad' => $row->rf_humedad,
                            'rf_venti' => $row->rf_venti

                        ];


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
                        $pdf = FacadePdf::loadView('pdf.instructivoCargaExpoTer', $data);
                        file_put_contents($save_folder, $pdf->output());

                        $respuesta_update = DB::table('asign')
                            ->where('cntr_number', $cntr_number)
                            ->update(['file_instruction' => $file_name]);

                        return $pdf->download($file_name);
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
                            'confirmacion' => $row->confirmacion,

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
                            "descarga_link" => $row->descarga_link,
                            'ex_alto' => $row->ex_alto,
                            'ex_ancho' => $row->ex_ancho,
                            'ex_largo' => $row->ex_largo,
                            'obs_imo' => $row->obs_imo,
                            'rf_tem' => $row->rf_tem,
                            'rf_humedad' => $row->rf_humedad,
                            'rf_venti' => $row->rf_venti

                        ];


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
                        $pdf = FacadePdf::loadView('pdf.instructivoCargaImpoMar', $data);
                        file_put_contents($save_folder, $pdf->output());

                        $respuesta_update = DB::table('asign')
                            ->where('cntr_number', $cntr_number)
                            ->update(['file_instruction' => $file_name]);

                        return $pdf->download($file_name);
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
                            'confirmacion' => $row->confirmacion,

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
                            "descarga_link" => $row->descarga_link,
                            'ex_alto' => $row->ex_alto,
                            'ex_ancho' => $row->ex_ancho,
                            'ex_largo' => $row->ex_largo,
                            'obs_imo' => $row->obs_imo,
                            'rf_tem' => $row->rf_tem,
                            'rf_humedad' => $row->rf_humedad,
                            'rf_venti' => $row->rf_venti

                        ];


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
                        $pdf = FacadePdf::loadView('pdf.instructivoCargaImpoTer', $data);
                        file_put_contents($save_folder, $pdf->output());

                        $respuesta_update = DB::table('asign')
                            ->where('cntr_number', $cntr_number)
                            ->update(['file_instruction' => $file_name]);

                        return $pdf->download($file_name);
                    } elseif ($row->type == 'Nacional') {

                        $data = [

                            'id_asign' => $row->id,
                            'img' => $base . '/public/image/empresas/' . $row->img,
                            'cuit' => $row->cuit,
                            'title' => $row->title,
                            'retiro_place' => $row->retiro_place,


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
                            'cntr_seal' => $row->cntr_seal,

                            'confirmacion' => $row->confirmacion,


                            'cntr_type' => $row->cntr_type,
                            'net_weight' => $row->net_weight,

                            'transport' => $row->transport,
                            'transport_agent' => $row->transport_agent,


                            'observation_load' => $row->observation_load,

                            'out_usd' => $row->out_usd,

                            'load_date' => $load_date,
                            'link_maps' => $row->link_maps,
                            'address' => $row->address,
                            'city' => $row->city,

                            'observation_customer' => $row->observation_customer,
                            "descarga_place" => $row->descarga_place,
                            "descarga_address" => $row->descarga_address,
                            "descarga_city" => $row->descarga_city,
                            "descarga_link" => $row->descarga_link,
                            'ex_alto' => $row->ex_alto,
                            'ex_ancho' => $row->ex_ancho,
                            'ex_largo' => $row->ex_largo,
                            'obs_imo' => $row->obs_imo,
                            'rf_tem' => $row->rf_tem,
                            'rf_humedad' => $row->rf_humedad,
                            'rf_venti' => $row->rf_venti

                        ];


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
                        $pdf = FacadePdf::loadView('pdf.instructivoNacional', $data);
                        file_put_contents($save_folder, $pdf->output());

                        $respuesta_update = DB::table('asign')
                            ->where('cntr_number', $cntr_number)
                            ->update(['file_instruction' => $file_name]);

                        return $pdf->download($file_name);
                    }
                } else {

                    return 'Faltan Datos para crear instruccion';
                }
            } else {


                return redirect('https://botzero.tech/ttl/views/view_instructivos.php');
            }
        } else {

            return 'no hay instrucciones creadas.';
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
                ->get(['asign.id', 'asign.transport', 'asign.transport_agent', 'asign.observation_load', 'asign.agent_port', 'carga.custom_place', 'carga.load_date', 'carga.booking', 'carga.shipper', 'carga.commodity', 'carga.load_place', 'carga.unload_place', 'carga.cut_off_fis', 'carga.oceans_line', 'carga.vessel', 'carga.voyage', 'carga.final_point', 'carga.custom_agent', 'carga.ref_customer','cntr.cntr_number','cntr.confirmacion', 'cntr.cntr_seal', 'cntr.cntr_type', 'cntr.net_weight', 'cntr.retiro_place', 'cntr.out_usd', 'cntr.observation_out',  'customer_load_places.link_maps', 'customer_load_places.address', 'customer_load_places.city']);
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
                'confirmacion' => $row->confirmacion,

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
                'date' => $date

            ];

            // BUSCO DATOS DEL TRANSPORTE DONDE VOY A ENVIAR EL CORREO

            $qAsing = DB::table('asign')->select('transport')->where('cntr_number', '=', $cntr_number)->get();
            $empresa = $qAsing[0]->transport;

            $qmail = DB::table('transports')->where('razon_social', '=', $empresa)->select('contacto_logistica_mail')->get();
            $mail = $qmail[0]->contacto_logistica_mail;

            // ENVIAMOS MAIL 

            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');

            if ($sbx[0]->sandbox == 0) {


                Mail::to($mail)->bcc($inboxEmail)->send(new envioInstructivo($data));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email Instructivo to:" . $mail;
                $logApi->save();

                return 'ok';
                
            } elseif($sbx[0]->sandbox == 2) {

                Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new envioInstructivo($data));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email Instructivo to: pablorio@botzero.tech";
                $logApi->save();
                return 'ok';

            } else{

                Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new envioInstructivo($data));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email Instructivo to: pablorio@botzero.tech";
                $logApi->save();
                return 'ok';
            }
        } else {

            return 'no hay asignacion para ese camion';
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
