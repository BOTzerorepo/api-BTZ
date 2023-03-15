<?php

namespace App\Http\Controllers;

use App\Mail\envioIntructivoOceans;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class oceansInstructions extends Controller
{
    public function generateOceansIntruction($trip){


        // Conexion con la DB.

        $conn = mysqli_connect(
            '31.170.161.22',
            'u101685278_ttlgroup',
            'Pachiman9102',
            'u101685278_ttlgroup'
        );
       /*  $conn = mysqli_connect(
            '31.170.161.22',
            'u101685278_sbttl',
            'Pachiman9102$',
            'u101685278_sbttl'
        ); */

        $query_file = "SELECT DISTINCT asign.id, asign.transport, asign.transport_agent, asign.observation_load, asign.agent_port, carga.custom_place, carga.load_date, carga.booking, carga.shipper, carga.commodity, carga.load_place, carga.unload_place, carga.cut_off_fis, carga.oceans_line, carga.vessel, carga.voyage, carga.final_point, carga.custom_agent, carga.ref_customer, cntr.cntr_number, cntr.cntr_seal, cntr.cntr_type, cntr.net_weight, cntr.retiro_place, cntr.out_usd, cntr.modo_pago_out, cntr.plazo_de_pago_out, customer_load_place.link_maps, customer_load_place.address, customer_load_place.city FROM carga INNER JOIN cntr INNER JOIN asign INNER JOIN customer_load_place ON carga.booking = cntr.booking AND cntr.cntr_number = asign.cntr_number AND customer_load_place.description = carga.load_place WHERE cntr.cntr_number = '$trip'";                
        $result_file = mysqli_query($conn, $query_file);
        //medu1236782
        if (mysqli_num_rows($result_file) == 1) {
            $row = mysqli_fetch_array($result_file);

            /////////////////////////////////////////////////
            ////////// DATOS PARA EL TREMPLATE //////////////
            /////////////////////////////////////////////////

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
                'date' => Carbon::now() 
            ];

            /////////////////////////////////////////////////
            ///////////// ARMMAMOS LA CARPETA  //////////////
            /////////////////////////////////////////////////

            {{ Controller::carpetaIntructivos($row['booking'], $trip); }}

            $folder = 'instructivos/' . $row['booking']. '/' . $trip . '/';
            $file_name = 'ORDEN DE TRABAJO EXPO MARITIMO ' . $row['booking'] . '_' . $trip . '.pdf';
            $save_folder = $folder . $file_name;

            // Generamos el Archivo PDF
            $pdf = Pdf::loadView('pdf.instructivoCarga', $data);
            file_put_contents($save_folder, $pdf->output());
            $query_upload_file = "UPDATE `asign` SET `file_instruction` = '$file_name' WHERE cntr_number = '$trip'";
            
            $result_upload_file = mysqli_query($conn, $query_upload_file);
            
            {{ oceansInstructions::mailOceans($row['booking'],$data); }}
          
            
            return $pdf->download($file_name);

        }

    }
    public function mailOceans($booking,$data){
        
        $qempresa = DB::table('carga')->select('empresa')->where('booking','=',$booking)->get();
        $empresa = $qempresa[0]->empresa;
        $qmail = DB::table('empresas')->where('razon_social','=',$empresa)->select('mail_logistic')->get();
        $mail = $qmail[0]->mail_logistic;

        $mail = Mail::to($mail)->send(new envioIntructivoOceans($data)); 
        return 'ok';
    }
}
