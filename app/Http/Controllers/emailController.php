<?php

namespace App\Http\Controllers;

use App\Mail\avisoNewCarga;
use App\Mail\CamnioStatus;
use App\Mail\cargaAsignada;
use App\Mail\cargaAsignadaEditada;
use App\Mail\CargaConProblemas;
use App\Mail\cargaTerminada;
use App\Mail\IngresadoStacking;
use App\Mail\transporteAsignado;
use App\Models\empresa;
use App\Models\particularSoftConfiguration;
use App\Models\Carga;
use App\Models\logapi;
use App\Models\statu;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class emailController extends Controller
{
    function parseEmailList($value): array
    {
        if (!$value) return [];
        if (is_array($value)) $value = implode(',', $value);
        $normalized = str_replace([';', "\n", "\r", "\t"], ',', $value);
        $arr = array_map('trim', explode(',', $normalized));
        $arr = array_filter($arr, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));
        return array_values(array_unique($arr));
    }
    function pushIfEmail(&$arr, $email): void
    {
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) $arr[] = trim($email);
    }
    public function cargaAsignada($id) // FALTA ACOMODAR TODO
    {

        $date = Carbon::now('-03:00');
        $asign = DB::table('asign')
            ->select('asign.*', 'cntr.cntr_type', 'carga.trader', 'carga.ref_customer', 'carga.type', 'carga.user as userC', 'transports.Direccion', 'transports.paut', 'transports.CUIT', 'transports.permiso', 'transports.vto_permiso', 'drivers.documento', 'trucks.model', 'trucks.model', 'trucks.year', 'trucks.chasis', 'trucks.poliza', 'trucks.vto_poliza', 'trailers.domain as semi_domain', 'trailers.poliza as semi_poliza', 'trailers.vto_poliza as semi_vto_poliza', 'trailers.semi_genset', 'cntr.confirmacion', 'cntr.cntr_seal')
            ->join('transports', 'asign.transport', '=', 'transports.razon_social')
            ->join('drivers', 'drivers.nombre', '=', 'asign.driver')
            ->join('trucks', 'trucks.domain', '=', 'asign.truck')
            ->join('carga', 'asign.booking', '=', 'carga.booking')
            ->join('trailers', 'trailers.domain', '=', 'asign.truck_semi')
            ->join('cntr', function ($join) {
                $join->on('cntr.cntr_number', '=', 'asign.cntr_number')
                    ->where('cntr.main_status', '!=', 'TERMINADA');
            })
            ->where('asign.id', '=', $id)->get();

        $dAsign = $asign[0];
        $to = DB::table('users')->select('email')->where('username', '=', $dAsign->userC)->get();

        $data = [
            // Datos CRT
            'transport' => $dAsign->transport,
            'direccion' => $dAsign->Direccion,
            'paut' => $dAsign->paut,
            'cuit' => $dAsign->CUIT,
            'permiso_int' => $dAsign->permiso,
            'vto_permiso_int' => $dAsign->vto_permiso,
            'crt' => $dAsign->crt,
            // Datos para MIC
            'fletero_razon_social' => $dAsign->fletero_razon_social,
            'fletero_domicilio' => $dAsign->fletero_domicilio,
            'fletero_cuit' => $dAsign->fletero_cuit,
            'fletero_paut' => $dAsign->fletero_paut,
            'fletero_permiso' => $dAsign->fletero_permiso,
            'fletero_vto_permiso' => $dAsign->fletero_vto_permiso,
            'confirmacion' => $dAsign->confirmacion,
            'driver' => $dAsign->driver,
            'documento' => $dAsign->documento,
            'truck' => $dAsign->truck,
            'truck_modelo' => $dAsign->model,
            'truck_year' => $dAsign->year,
            'truck_chasis' => $dAsign->chasis,
            'truck_poliza' => $dAsign->poliza,
            'truck_vto_poliza' => $dAsign->vto_poliza,
            'truck_semi' => $dAsign->truck_semi,
            'truck_semi_poliza' => $dAsign->semi_poliza,
            'truck_semi_genset' => $dAsign->semi_genset,
            'truck_semi_vto_poliza' => $dAsign->semi_vto_poliza,
            'cntr_number' => $dAsign->cntr_number,
            'booking' => $dAsign->booking,
            'user' => $dAsign->user,
            'company' => $dAsign->company,
            'ref_customer' => $dAsign->ref_customer,
            'type' => $dAsign->type,
            'trader' => $dAsign->trader,
            'cntr_type' => $dAsign->cntr_type,
            'booking' => $dAsign->booking,
            'cntr_seal' => $dAsign->cntr_seal,

        ];

        //Enviar mail
        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');
        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
        $carga = Carga::whereNull('deleted_at')->where('booking', '=', $dAsign->booking)->first();

        if ($sbx[0]->sandbox == 0) {

            // --- 1) Traer customer (por username) y cliente (por client_id) ---
            $customerUser = DB::table('users')->where('username', '=', $carga->user)->first();
            $clienteUser  = DB::table('users')->where('cliente_id', '=', $carga->client_id)->first();

            // --- 2) Armar TO (customer + cliente + lo que ya tengas en $toEmails) ---
            $to = [];
            pushIfEmail($to, $customerUser->email ?? null);
            pushIfEmail($to, $clienteUser->email  ?? null);
            $to = array_merge($to, parseEmailList($toEmails ?? ''));
            $to = array_values(array_unique($to));

            if (empty($to)) {
                Log::warning("Sin destinatarios TO en 'cargaAsignada' para carga ID {$carga->id} (booking {$carga->booking}). Uso fallback.");
                $to[] = 'soporte@rail.ar';
            }

            // --- 3) Armar CC (cc_emails de ambos + $ccEmails extra si lo venías usando) ---
            $cc = array_merge(
                parseEmailList($customerUser->cc_emails ?? ''),
                parseEmailList($clienteUser->cc_emails  ?? ''),
                parseEmailList($ccEmails ?? '')
            );
            $cc = array_values(array_unique($cc));

            // --- 4) Armar BCC (acepta string o array) ---
            $bcc = array_values(array_unique(parseEmailList($inboxEmail ?? '')));

            // --- 5) Envío ---
            Mail::to($to)
                ->when(!empty($cc),  fn($m) => $m->cc($cc))
                ->when(!empty($bcc), fn($m) => $m->bcc($bcc))
                ->send(new cargaAsignada($data, $date));

            // --- 6) Logs y status (tu lógica original) ---
            $logapi = new logapi();
            $logapi->user    = $dAsign->user;
            $logapi->detalle = 'AsignaUnidadCarga-User:' . $dAsign->user
                . '|Transporte:' . $dAsign->transport
                . '|Chofer:' . $dAsign->driver
                . '|Tractor:' . $dAsign->truck
                . '|Semi:' . $dAsign->truck_semi
                . ' | TO:' . implode(', ', $to)
                . ' | CC:' . implode(', ', $cc)
                . ' | BCC:' . implode(', ', $bcc);
            $logapi->save();

            $status = new statu();
            $status->status      = 'Asignado Chofer:' . $dAsign->driver . '|Tractor:' . $dAsign->truck . '|Semi:' . $dAsign->truck_semi;
            $status->avisado     = 1;
            $status->main_status = 'ASIGNADA';
            $status->cntr_number = $dAsign->cntr_number;
            $status->user_status = $dAsign->user;
            $status->save();

            return 'ok';
        } else {

            $customer = DB::table('users')
                ->where('username', '=', $carga->user)
                ->value('email');
            $toEmails = array_merge([$customer], (array) $toEmails);

            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaAsignada($data, $date));


            $logapi = new logapi();
            $logapi->user = $dAsign->user;
            $logapi->detalle = '+ Sandbox + to: ' . $to . 'AsignaUnidadCarga-User:' . $dAsign->user . ' |Transporte:' . $dAsign->transport . '|Chofer:' . $dAsign->driver . '|Tractor:' . $dAsign->truck . '|Semi:' . $dAsign->truck_semi;
            $logapi->save();

            $status = new statu();
            $status->status = 'Asignado Chofer:' . $dAsign->driver . '|Tractor:' . $dAsign->truck . '|Semi:' . $dAsign->truck_semi;
            $status->avisado = 1;
            $status->main_status = 'ASIGNADA';
            $status->cntr_number = $dAsign->cntr_number;
            $status->user_status = $dAsign->user;
            $status->save();

            return 'ok';
        }
    }

    public function cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath) //TRABAJANDO
    {

        $logapi = new logapi();
        $logapi->user = $user;
        $logapi->detalle = 'Envio Mail_' . $tipo;
        $logapi->save();
        $date = Carbon::now('-03:00');

        //Enviar mail
        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');
        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
        $carga = Carga::whereNull('deleted_at')->where('booking', '=', $booking)->first();



        if ($tipo == 'problema') {

            $qd = DB::table('status')
                ->select(
                    'status.id',
                    'status.status',
                    'cntr.cntr_type',
                    'carga.trader',
                    'carga.type',
                    'carga.load_place',
                    'carga.unload_place',
                    'carga.custom_place',
                    'carga.custom_place_impo',
                    'carga.ref_customer',
                    'cntr.confirmacion',
                    'asign.transport',
                    'asign.transport_agent',
                    'asign.truck',
                    'asign.truck_semi',
                    'asign.driver',
                    'drivers.documento'
                )
                ->join('cntr', 'cntr.cntr_number', '=', 'status.cntr_number')
                ->join('carga', 'carga.booking', '=', 'cntr.booking')
                ->leftJoin('asign', 'asign.cntr_number', '=', 'status.cntr_number')
                ->leftJoin('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->where('status.cntr_number', '=', $cntr)->latest('id')->first();
            $description = $qd->status;
            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'confirmacion' => $qd->confirmacion,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'date' => $date,
                'status' => 'con Problema',
                'cntr_type' => $qd->cntr_type,
                'load_place'        => $qd->load_place,
                'unload_place'      => $qd->unload_place,
                'custom_place'      => $qd->custom_place,
                'custom_place_impo' => $qd->custom_place_impo,
                'trader' => $qd->trader,
                'type' => $qd->type,
                'ref_customer' => $qd->ref_customer,
                'transport' => $qd->transport,
                'transport_agent' => $qd->transport_agent,
                'driver' => $qd->driver,
                'truck' => $qd->truck,
                'truck_semi' => $qd->truck_semi,
                'documento' => $qd->documento,
            ];

            if ($sbx[0]->sandbox == 0) {

                // --- 1) Traer customer (por username) y cliente (por client_id) ---
                $customerUser = DB::table('users')->where('username', '=', $carga->user)->first();
                $clienteUser  = DB::table('users')->where('cliente_id', '=', $carga->client_id)->first();

                // --- 2) Armar TO (customer + cliente + lo que ya tengas en $toEmails) ---
                $to = [];
                pushIfEmail($to, $customerUser->email ?? null);
                pushIfEmail($to, $clienteUser->email  ?? null);
                $to = array_merge($to, parseEmailList($toEmails ?? ''));
                $to = array_values(array_unique($to));

                if (empty($to)) {
                    Log::warning("Sin destinatarios TO en 'cargaAsignada' para carga ID {$carga->id} (booking {$carga->booking}). Uso fallback.");
                    $to[] = 'soporte@rail.ar';
                }

                // --- 3) Armar CC (cc_emails de ambos + $ccEmails extra si lo venías usando) ---
                $cc = array_merge(
                    parseEmailList($customerUser->cc_emails ?? ''),
                    parseEmailList($clienteUser->cc_emails  ?? ''),
                    parseEmailList($ccEmails ?? '')
                );
                $cc = array_values(array_unique($cc));

                // --- 4) Armar BCC (acepta string o array) ---
                $bcc = array_values(array_unique(parseEmailList($inboxEmail ?? '')));

                // --- 5) Envío ---
                Mail::to($to)
                    ->when(!empty($cc),  fn($m) => $m->cc($cc))
                    ->when(!empty($bcc), fn($m) => $m->bcc($bcc))
                    ->send(new CargaConProblemas($datos, $statusArchivoPath));

                // --- 6) Logs y status (tu lógica original) ---

                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new CargaConProblemas($datos, $statusArchivoPath));

                // --- 6) Logs y status (tu lógica original) ---
                $logapi = new logapi();
                $logapi->user    = $customerUser->username;
                $logapi->detalle = 'Carga con problemas:' . $cntr
                    . '|Transporte:' . $datos['transport']
                    . '|Chofer:' . $datos['driver']
                    . '|Tractor:' . $datos['truck']
                    . '|Semi:' . $datos['truck_semi']
                    . ' | TO:' . implode(', ', $to)
                    . ' | CC:' . implode(', ', $cc)
                    . ' | BCC:' . implode(', ', $bcc);
                $logapi->save();
                
                return 'ok';
            } else {

               
                return 'ok';
            }
        } elseif ($tipo == 'stacking') {

            $qd = DB::table('status')
                ->select(
                    'status.main_status',
                    'status.id',
                    'status.status',
                    'cntr.cntr_type',
                    'carga.trader',
                    'carga.type',
                    'carga.ref_customer',
                    'carga.load_place',
                    'carga.unload_place',
                    'carga.custom_place',
                    'carga.custom_place_impo',
                    'cntr.confirmacion',
                    'asign.transport',
                    'asign.transport_agent',
                    'asign.truck',
                    'asign.truck_semi',
                    'asign.driver',
                    'drivers.documento'
                )
                ->join('cntr', 'cntr.cntr_number', '=', 'status.cntr_number')
                ->join('carga', 'carga.booking', '=', 'cntr.booking')
                ->leftJoin('asign', 'asign.cntr_number', '=', 'status.cntr_number')
                ->leftJoin('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->where('status.cntr_number', '=', $cntr)->latest('status.id')->first();
            $description = $qd->status;
            $status = $qd->main_status;

            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'confirmacion' => $qd->confirmacion,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'load_place'        => $qd->load_place,
                'unload_place'      => $qd->unload_place,
                'custom_place'      => $qd->custom_place,
                'custom_place_impo' => $qd->custom_place_impo,
                'date' => $date,
                'cntr_type' => $qd->cntr_type,
                'trader' => $qd->trader,
                'type' => $qd->type,
                'ref_customer' => $qd->ref_customer,
                'transport' => $qd->transport,
                'transport_agent' => $qd->transport_agent,
                'driver' => $qd->driver,
                'truck' => $qd->truck,
                'truck_semi' => $qd->truck_semi,
                'documento' => $qd->documento,
            ];

            if ($sbx[0]->sandbox == 0) {
                // --- 1) Traer customer (por username) y cliente (por client_id) ---
                $customerUser = DB::table('users')->where('username', '=', $carga->user)->first();
                $clienteUser  = DB::table('users')->where('cliente_id', '=', $carga->client_id)->first();

                // --- 2) Armar TO (customer + cliente + lo que ya tengas en $toEmails) ---
                $to = [];
                pushIfEmail($to, $customerUser->email ?? null);
                pushIfEmail($to, $clienteUser->email  ?? null);
                $to = array_merge($to, parseEmailList($toEmails ?? ''));
                $to = array_values(array_unique($to));

                if (empty($to)) {
                    Log::warning("Sin destinatarios TO en 'cargaAsignada' para carga ID {$carga->id} (booking {$carga->booking}). Uso fallback.");
                    $to[] = 'soporte@rail.ar';
                }

                // --- 3) Armar CC (cc_emails de ambos + $ccEmails extra si lo venías usando) ---
                $cc = array_merge(
                    parseEmailList($customerUser->cc_emails ?? ''),
                    parseEmailList($clienteUser->cc_emails  ?? ''),
                    parseEmailList($ccEmails ?? '')
                );
                $cc = array_values(array_unique($cc));

                // --- 4) Armar BCC (acepta string o array) ---
                $bcc = array_values(array_unique(parseEmailList($inboxEmail ?? '')));

                // --- 5) Envío ---
                Mail::to($to)
                    ->when(!empty($cc),  fn($m) => $m->cc($cc))
                    ->when(!empty($bcc), fn($m) => $m->bcc($bcc))
                    ->send(new IngresadoStacking($datos, $statusArchivoPath));

                // --- 6) Logs y status (tu lógica original) ---
                $logapi = new logapi();
                $logapi->user    = $customerUser->username;
                $logapi->detalle = 'Carga ingresada Stackign:' . $cntr
                    . '|Transporte:' . $datos['transport']
                    . '|Chofer:' . $datos['driver']
                    . '|Tractor:' . $datos['truck']
                    . '|Semi:' . $datos['truck_semi']
                    . ' | TO:' . implode(', ', $to)
                    . ' | CC:' . implode(', ', $cc)
                    . ' | BCC:' . implode(', ', $bcc);
                $logapi->save();
                
                return 'ok';
                
            } else {
                
                return 'ok';
            }
        } elseif ($tipo == 'terminada') {

            $qd = DB::table('status')
                ->select(
                    'status.id',
                    'status.status',
                    'cntr.cntr_type',
                    'carga.trader',
                    'carga.type',
                    'carga.ref_customer',
                    'carga.load_place',
                    'carga.unload_place',
                    'carga.custom_place',
                    'carga.custom_place_impo',
                    'cntr.confirmacion',
                    'asign.transport',
                    'asign.transport_agent',
                    'asign.truck',
                    'asign.truck_semi',
                    'asign.driver',
                    'drivers.documento'
                )
                ->join('cntr', 'cntr.cntr_number', '=', 'status.cntr_number')
                ->join('carga', 'carga.booking', '=', 'cntr.booking')
                ->leftJoin('asign', 'asign.cntr_number', '=', 'status.cntr_number')
                ->leftJoin('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->where('status.cntr_number', '=', $cntr)->latest('id')->first();
            $description = $qd->status;
            $datos = [
                'cntr' => $cntr,
                'description' =>  $description,
                'user' => $user,
                'empresa' => $empresa,
                'booking' => $booking,
                'date' => $date,
                'status' => 'con Problema',
                'cntr_type' => $qd->cntr_type,
                'confirmacion' => $qd->confirmacion,
                'trader' => $qd->trader,
                'type' => $qd->type,
                'ref_customer' => $qd->ref_customer,
                'transport' => $qd->transport,
                'transport_agent' => $qd->transport_agent,
                'driver' => $qd->driver,
                'truck' => $qd->truck,
                'truck_semi' => $qd->truck_semi,
                'documento' => $qd->documento,
                'load_place'        => $qd->load_place,
                'unload_place'      => $qd->unload_place,
                'custom_place'      => $qd->custom_place,
                'custom_place_impo' => $qd->custom_place_impo,
            ];

            if ($sbx[0]->sandbox == 0) {
                // --- 1) Traer customer (por username) y cliente (por client_id) ---
                $customerUser = DB::table('users')->where('username', '=', $carga->user)->first();
                $clienteUser  = DB::table('users')->where('cliente_id', '=', $carga->client_id)->first();

                // --- 2) Armar TO (customer + cliente + lo que ya tengas en $toEmails) ---
                $to = [];
                pushIfEmail($to, $customerUser->email ?? null);
                pushIfEmail($to, $clienteUser->email  ?? null);
                $to = array_merge($to, parseEmailList($toEmails ?? ''));
                $to = array_values(array_unique($to));

                if (empty($to)) {
                    Log::warning("Sin destinatarios TO en 'cargaAsignada' para carga ID {$carga->id} (booking {$carga->booking}). Uso fallback.");
                    $to[] = 'soporte@rail.ar';
                }

                // --- 3) Armar CC (cc_emails de ambos + $ccEmails extra si lo venías usando) ---
                $cc = array_merge(
                    parseEmailList($customerUser->cc_emails ?? ''),
                    parseEmailList($clienteUser->cc_emails  ?? ''),
                    parseEmailList($ccEmails ?? '')
                );
                $cc = array_values(array_unique($cc));

                // --- 4) Armar BCC (acepta string o array) ---
                $bcc = array_values(array_unique(parseEmailList($inboxEmail ?? '')));

                // --- 5) Envío ---
                Mail::to($to)
                    ->when(!empty($cc),  fn($m) => $m->cc($cc))
                    ->when(!empty($bcc), fn($m) => $m->bcc($bcc))
                    ->send(new cargaTerminada($datos, $statusArchivoPath));

                // --- 6) Logs y status (tu lógica original) ---
                $logapi = new logapi();
                $logapi->user    = $customerUser->username;
                $logapi->detalle = 'Carga con Terminada:' . $cntr
                    . '|Transporte:' . $datos['transport']
                    . '|Chofer:' . $datos['driver']
                    . '|Tractor:' . $datos['truck']
                    . '|Semi:' . $datos['truck_semi']
                    . ' | TO:' . implode(', ', $to)
                    . ' | CC:' . implode(', ', $cc)
                    . ' | BCC:' . implode(', ', $bcc);
                $logapi->save();
                
                return 'ok';
                
            } else {
               
                return 'ok';
            }
        } else {

            $qd = DB::table('status')
                ->join('cntr', function ($j) {
                    $j->on('cntr.cntr_number', '=', 'status.cntr_number')
                        ->where('cntr.main_status', '<>', 'TERMINADA');
                })
                ->join('carga', 'carga.booking', '=', 'cntr.booking')
                ->leftJoin('asign', function ($j) {
                    $j->on('asign.cntr_number', '=', 'status.cntr_number')
                        ->on('asign.booking', '=', 'cntr.booking'); // evita mezclar bookings
                })
                ->leftJoin('drivers', 'drivers.nombre', '=', 'asign.driver')
                ->where('status.cntr_number', $cntr)           // usa tu variable $cntr
                // ->where('carga.booking', $booking)          // (opcional) aún más seguro
                ->orderByDesc('status.id')
                ->first([ // <-- antes era get([...])
                    'status.main_status',
                    'status.id',
                    'status.status',
                    'cntr.cntr_type',
                    'carga.trader',
                    'carga.type',
                    'carga.ref_customer',
                    'carga.load_place',
                    'carga.unload_place',
                    'carga.custom_place',
                    'carga.custom_place_impo',
                    'cntr.confirmacion',
                    'asign.transport',
                    'asign.transport_agent',
                    'asign.truck',
                    'asign.truck_semi',
                    'asign.driver',
                    'drivers.documento',
                ]);

            if (!$qd) {
                // no hay status para ese contenedor (y booking)
                throw new \RuntimeException('No se encontró status para el contenedor indicado.');
            }

            $description = $qd->status;
            $status      = $qd->main_status;

            $datos = [
                'cntr'              => $cntr,
                'description'       => $description,
                'confirmacion'      => $qd->confirmacion,
                'user'              => $user,
                'empresa'           => $empresa,
                'booking'           => $booking,
                'date'              => $date,
                'status'            => $status,
                'load_place'        => $qd->load_place,
                'unload_place'      => $qd->unload_place,
                'custom_place'      => $qd->custom_place,
                'custom_place_impo' => $qd->custom_place_impo,
                'cntr_type'         => $qd->cntr_type,
                'trader'            => $qd->trader,
                'type'              => $qd->type,
                'ref_customer'      => $qd->ref_customer,
                'transport'         => $qd->transport,
                'transport_agent'   => $qd->transport_agent,
                'driver'            => $qd->driver,
                'truck'             => $qd->truck,
                'truck_semi'        => $qd->truck_semi,
                'documento'         => $qd->documento,
                'load_place'        => $qd->load_place,
                'unload_place'      => $qd->unload_place,
                'custom_place'      => $qd->custom_place,
                'custom_place_impo' => $qd->custom_place_impo,
            ];

            if ($sbx[0]->sandbox == 0) {
               // --- 1) Traer customer (por username) y cliente (por client_id) ---
               $customerUser = DB::table('users')->where('username', '=', $carga->user)->first();
               $clienteUser  = DB::table('users')->where('cliente_id', '=', $carga->client_id)->first();

               // --- 2) Armar TO (customer + cliente + lo que ya tengas en $toEmails) ---
               $to = [];
               pushIfEmail($to, $customerUser->email ?? null);
               pushIfEmail($to, $clienteUser->email  ?? null);
               $to = array_merge($to, parseEmailList($toEmails ?? ''));
               $to = array_values(array_unique($to));

               if (empty($to)) {
                   Log::warning("Sin destinatarios TO en 'cargaAsignada' para carga ID {$carga->id} (booking {$carga->booking}). Uso fallback.");
                   $to[] = 'soporte@rail.ar';
               }

               // --- 3) Armar CC (cc_emails de ambos + $ccEmails extra si lo venías usando) ---
               $cc = array_merge(
                   parseEmailList($customerUser->cc_emails ?? ''),
                   parseEmailList($clienteUser->cc_emails  ?? ''),
                   parseEmailList($ccEmails ?? '')
               );
               $cc = array_values(array_unique($cc));

               // --- 4) Armar BCC (acepta string o array) ---
               $bcc = array_values(array_unique(parseEmailList($inboxEmail ?? '')));

               // --- 5) Envío ---
               Mail::to($to)
                   ->when(!empty($cc),  fn($m) => $m->cc($cc))
                   ->when(!empty($bcc), fn($m) => $m->bcc($bcc))
                   ->send(new CamnioStatus($datos, $statusArchivoPath));
                   
               // --- 6) Logs y status (tu lógica original) ---
               $logapi = new logapi();
               $logapi->user    = $customerUser->username;
               $logapi->detalle = 'Carga Cambio Status:' . $datos['status']
                   . '|Transporte:' . $datos['transport']
                   . '|Chofer:' . $datos['driver']
                   . '|Tractor:' . $datos['truck']
                   . '|Semi:' . $datos['truck_semi']
                   . ' | TO:' . implode(', ', $to)
                   . ' | CC:' . implode(', ', $cc)
                   . ' | BCC:' . implode(', ', $bcc);
               $logapi->save();
               
               return 'ok';
                
            } else {
                
                return 'ok';
            }
        }
    }

    public function avisoNuevaCarga($idCarga, $user) //OK
    {

        $user = DB::table('users')->join('particular_soft_configurations', 'users.configCompany', '=', 'particular_soft_configurations.name')->where('users.username', '=', $user)->get();

        $qcarga = DB::table('carga')
            ->select(
                'carga.id',
                'carga.booking',
                'carga.trader',
                'carga.importador',
                'carga.user',
                'carga.ref_customer',
                'carga.shipper',
                'carga.load_date',
                'carga.load_place',
                'carga.custom_place',
                'carga.custom_agent',
                'carga.custom_place_impo',
                'carga.custom_agent_impo',
                'carga.oceans_line',
                'carga.vessel',
                'carga.voyage',
                'carga.cut_off_fis',
                'carga.unload_place',
                'carga.final_point',
                'carga.commodity',
                'carga.observation_customer',
                'cntr.retiro_place',
                'cntr.cntr_type',
                'carga.type',
                'carga.senasa',
                'carga.senasa_string',
                'carga.tara',
                'carga.tara_string',
                'carga.bl_hbl',
                'carga.cma_t_o',

            )
            ->join('cntr', 'carga.booking', '=', 'cntr.booking')->where('carga.id', '=', $idCarga)->get();
        $cantidad = $qcarga->count();
        $carga = $qcarga[0];
        $date = Carbon::now('-03:00');

        $fecha = Carbon::parse($carga->load_date);
        //$loadDate = $fecha->format('d/m/Y');
        $loadDate = $fecha->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY');

        $datos = [
            'id' => $carga->id,
            'operacion' => $carga->ref_customer,
            'trader' => $carga->trader,
            'importador' => $carga->importador,
            'booking' => $carga->booking,
            'loadDate' => $loadDate,
            'depositoRetiro' => $carga->retiro_place,
            'shipper' => $carga->shipper,
            'loadPlace' => $carga->load_place,
            'customPlace' => $carga->custom_place,
            'customPlaceImpo' => $carga->custom_place_impo,
            'customAgent' => $carga->custom_agent,
            'customAgentImpo' => $carga->custom_agent_impo,
            'armador' => $carga->oceans_line,
            'vessel' => $carga->vessel,
            'voyage' => $carga->voyage,
            'cutOffFisico' => $carga->cut_off_fis,
            'loadPort' => $carga->unload_place,
            'finalPoint' => $carga->final_point,
            'commodity' => $carga->commodity,
            'obeservaciones' => $carga->observation_customer,
            'cantidad' => $cantidad,
            'cntr_type' => $carga->cntr_type,
            'user' => $user[0]->username,
            'date' => $date,
            'type' => $carga->type,
            'bl_hbl' => $carga->bl_hbl,
            'senasa' => $carga->senasa,
            'senasa_string' => $carga->senasa_string,
            'tara' => $carga->tara,
            'tara_string' => $carga->tara_string,
            'cma_t_o' => $carga->cma_t_o,

        ];

        $sbx = DB::table('variables')->select('sandbox')->get();
        $inboxEmail = env('INBOX_EMAIL');

        $mailsTrafico = DB::table('particular_soft_configurations')->first();
        $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
        $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);

        if ($sbx[0]->sandbox == 0) {

            $customer = DB::table('users')
                ->where('username', '=', $carga->user)
                ->value('email');
            $toEmails = array_merge([$customer], (array) $toEmails);

            //Se envia el email al equipo de trafico y al customer que genero la carga. Copia a la gzarate
            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new avisoNewCarga($datos));
            $logApi = new logapi();
            $logApi->user = $user[0]->username;
            $logApi->detalle = "envio email to(Traffic y customer carga)->cc(['gzarate@totaltradegroup.com'])";
            $logApi->save();
            return 'ok';
        } elseif ($sbx[0]->sandbox == 2) {

            Mail::to(['customer@qa.botzero.com.ar', 'abel.mazzitelli@gmail.com'])->cc(['copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])->bcc($inboxEmail)->send(new avisoNewCarga($datos));
            $logApi = new logapi();
            $logApi->user = $user[0]->username;
            $logApi->detalle = "envio email to(['customer@qa.botzero.com.ar', 'abel.mazzitelli@gmail.com'])->cc(['copiaequipodemo5@botzero.com.ar', 'copiaequipodemo6@botzero.com.ar'])";
            $logApi->save();
            return 'ok';
        } else {

            $customer = DB::table('users')
                ->where('username', '=', $carga->user)
                ->value('email');
            $toEmails = array_merge([$customer], (array) $toEmails);

            //Se envia el email al equipo de trafico y al customer que genero la carga. Copia a la gzarate
            Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new avisoNewCarga($datos));
            $logApi = new logapi();
            $logApi->user = $user[0]->username;
            $logApi->detalle = "envio email to(['equipoDemo1@botzero.com.ar', 'equipodemo2@botzero.com.ar','equipodemo2@botzero.com.ar'])->cc(['equipodemo2@botzero.com.ar','copiaequipodemo5@botzero.com.ar','copiaequipodemo6@botzero.com.ar'])";
            $logApi->save();
            return 'ok';
        }
    }
}
