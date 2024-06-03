<?php

namespace App\Http\Controllers;

use App\Mail\envioIntructivoOceans;
use App\Models\logapi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class oceansInstructions extends Controller
{
    public function generateOceansIntruction($trip){

        return $trip;
        // Conexion con la DB.


        $respuesta_file = DB::table('carga')
        ->join('cntr','carga.booking', '=', 'cntr.booking')
        ->join('asign','cntr.cntr_number', '=', 'asign.cntr_number')
        ->join('customer_load_places','customer_load_places.description', '=', 'carga.load_place')
        ->where('cntr.cntr_number', '=', $trip)
        ->distinct()
        ->get(['asign.id', 'asign.transport', 'asign.transport_agent', 'asign.observation_load', 'asign.agent_port', 'carga.custom_place', 'carga.load_date', 'carga.booking', 'carga.shipper', 'carga.commodity', 'carga.load_place', 'carga.unload_place', 'carga.cut_off_fis', 'carga.oceans_line', 'carga.vessel', 'carga.voyage', 'carga.final_point', 'carga.custom_agent', 'carga.ref_customer', 'cntr.cntr_number', 'cntr.cntr_seal', 'cntr.cntr_type', 'cntr.net_weight', 'cntr.retiro_place', 'cntr.out_usd', 'cntr.modo_pago_out', 'cntr.plazo_de_pago_out', 'customer_load_places.link_maps', 'customer_load_place.address', 'customer_load_places.city']);
        $row = $respuesta_file[0];

       
        if ($respuesta_file->count() == 1) {
           
            /////////////////////////////////////////////////
            ////////// DATOS PARA EL TREMPLATE //////////////
            /////////////////////////////////////////////////

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
                'load_date' => $row->load_date,
                'link_maps' => $row->link_maps,
                'address' => $row->address,
                'city' => $row->city,
                'date' => Carbon::now() 
            ];

            /////////////////////////////////////////////////
            ///////////// ARMMAMOS LA CARPETA  //////////////
            /////////////////////////////////////////////////

            {{ Controller::carpetaIntructivos($row->booking, $trip); }}

            $folder = 'instructivos/' . $row->booking. '/' . $trip . '/';
            $file_name = 'ORDEN DE TRABAJO EXPO MARITIMO ' . $row->booking . '_' . $trip . '.pdf';
            $save_folder = $folder . $file_name;

            // Generamos el Archivo PDF
            $pdf = Pdf::loadView('pdf.instructivoCarga', $data);
            file_put_contents($save_folder, $pdf->output());
            
            $respuesta_update = DB::table('asign')
            ->where('cntr_number', $trip)
            ->update(['file_instruction' => $file_name]);


            
            {{ oceansInstructions::mailOceans($row->booking,$data); }}
          
            
            return $pdf->download($file_name);

        }

    }
    public function mailOceans($booking,$data){
        
        $qempresa = DB::table('carga')->select('empresa')->where('booking','=',$booking)->get();
        $empresa = $qempresa[0]->empresa;
        $qmail = DB::table('empresas')->where('razon_social','=',$empresa)->select('mail_logistic')->get();
        $mail = $qmail[0]->mail_logistic;

        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');
        if ($sbx[0]->sandbox == 0) {

            Mail::to($mail)->bcc($inboxEmail)->send(new envioIntructivoOceans($data)); 

            $logApi = new logapi();
            $logApi->user = 'No Informa';
            $logApi->detalle = "envio email envioIntructivoOceans to:" . $mail;

        } elseif ($sbx[0]->sandbox == 2) {

            Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new envioIntructivoOceans($data));

            $logApi = new logapi();
            $logApi->user = 'No Informa';
            $logApi->detalle = "+ Sandbox + envio email envioIntructivoOceans to: " . $mail;
        
        }else {

            Mail::to('pablorio@botzero.tech')->bcc($inboxEmail)->send(new envioIntructivoOceans($data));
            
            $logApi = new logapi();
            $logApi->user = 'No Informa';
            $logApi->detalle = "+ Sandbox + envio email envioIntructivoOceans to: " . $mail;
        }


        return 'ok';
    }
}
