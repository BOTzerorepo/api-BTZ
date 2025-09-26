<?php

namespace App\Http\Controllers;

use App\Models\CustomerLoadPlace;
use App\Mail\cargaAduana;
use App\Mail\cargaCargando;
use App\Mail\cargaDescarga;
use App\Mail\ubicacion;
use App\Mail\cargaFueraAduana;
use App\Mail\cargaFueraCargando;
use App\Models\logapi;
use App\Models\pruebasModel;
use App\Models\statu;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\cntr;
use App\Models\Carga;
use Google\Service\SQLAdmin\Flag;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Support\Facades\Log;

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
    public function accionLugarDeCarga($idTrip)
    {
        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr_number', 'booking', 'confirmacion')->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];
        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        Log::info('El ultimo status del contenedor ' . $cntr->cntr_number . ' es: ' . $qd->main_status);

        if ($qd->main_status != 'CARGANDO') {
            Log::info('El status es distinto a CARGANDO, se procede a cambiarlo y enviar mail.');
            DB::table('status')->insert([
                'status' => 'Camión se encuentra en un radio de 50 mts del Lugar de Carga.',
                'main_status' => 'CARGANDO',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);
            DB::table('cntr')
                ->where('cntr_number', $cntr->cntr_number)
                ->update([
                    'main_status' => 'CARGANDO',
                    'status_cntr' => 'Camión se encuentra en un radio de 200 mts del Lugar de Carga.'
                ]);


            $cntrs = cntr::where('booking', $cntr->booking)->get();
            // Obtener el status del primer registro
            $primerCntrStatus = $cntrs->first()->main_status;

            // Verificar si todos los registros tienen el mismo status
            $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                return $cntr->main_status == $primerCntrStatus;
            });

            // Si todos los registros tienen el mismo status, actualizar el status de la carga
            if ($equal) {
                Carga::where('booking', $cntr->booking)->update(['status' => $primerCntrStatus]);
            }

            $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
            $description = $qd->status;

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
            $empresa = $qempresa[0]->empresa;

            $datos = [
                'cntr' => $cntr->cntr_number,
                'confirmacion' => $cntr->confirmacion,
                'description' =>  $description,
                'user' => $qd->user_status,
                'empresa' => $empresa,
                'booking' => $cntr->booking,
                'date' => $date
            ];

            //Enviar mail
            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');
            $mailsTrafico = DB::table('particular_soft_configurations')->first();
            $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
            $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();

            $cliente = DB::table('users')
                ->where('cliente_id', '=', $carga->client_id)
                ->first();

            if ($sbx[0]->sandbox == 0) {

                if (!$cliente) {
                    // Logueás un warning para debug
                    Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                    // Podés definir un mail fallback para no perder la notificación
                    $clienteEmail = 'soporte@rail.ar';
                } else {
                    $clienteEmail = $cliente->email;
                }
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaCargando($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to:" . implode(', ', $toEmails);
                $logApi->save();
            } elseif ($sbx[0]->sandbox == 2) {

                Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaCargando($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to: abel.mazzitelli@gmail.com";
                $logApi->save();
            } else {
                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaCargando($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to: copia@botzero.com.ar";
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
                    'status' => 'Camión se encuentra en un radio de 50 mts del Lugar de Carga.',
                    'main_status' => 'CARGANDO',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'CARGANDO',
                        'status_cntr' => 'Camión se encuentra en un radio de 200 mts del Lugar de Carga.'
                    ]);
                $cntrs = cntr::where('booking', $cntr->booking)->get();
                // Obtener el status del primer registro
                $primerCntrStatus = $cntrs->first()->main_status;

                // Verificar si todos los registros tienen el mismo status
                $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                    return $cntr->main_status == $primerCntrStatus;
                });

                // Si todos los registros tienen el mismo status, actualizar el status de la carga
                if ($equal) {
                    Carga::where('booking', $cntr->booking)->update(['status' => $primerCntrStatus]);
                }

                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'confirmacion' => $cntr->confirmacion,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                //Enviar mail
                $sbx = DB::table('variables')->select('sandbox')->get();
                $inboxEmail = env('INBOX_EMAIL');
                $mailsTrafico = DB::table('particular_soft_configurations')->first();
                $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
                $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
                $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();
                $cliente = DB::table('users')
                    ->where('cliente_id', '=', $carga->client_id)
                    ->first();

                if ($sbx[0]->sandbox == 0) {

                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }
                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);
                    Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to:" . implode(', ', $toEmails);
                    $logApi->save();
                } elseif ($sbx[0]->sandbox == 2) {

                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to: abel.mazzitelli@gmail.com";
                    $logApi->save();
                } else {
                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }
                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);
                    Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to: copia@botzero.com.ar";
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
    private function cmaChangeFlag(string $cntrNumber, int $flag = 1, int $timeout = 30): array
    {
        Log::info("CMA: Cambiando flag a {$flag} para contenedor {$cntrNumber}");

        $base = rtrim(env('API_CMA_BOTZERO'), '/');
        if (!$base) {
            Log::error('CMA: API_CMA_BOTZERO no configurado');
            return ['ok' => false, 'http' => 500, 'error' => 'API_CMA_BOTZERO no configurado'];
        }


        $url     = "{$base}/cma/changeFlag/{$flag}/{$cntrNumber}";
        $headers = ['Content-Type' => 'application/json'];

        try {
            $client  = new Client(['http_errors' => false, 'timeout' => $timeout]);
            $request = new Psr7Request('GET', $url, $headers);

            $res   = $client->send($request);
            $code  = $res->getStatusCode();
            $body  = (string) $res->getBody();
            $data  = json_decode($body, true);
            $return = $data;
            if (!is_array($data) || (($data['ok'] ?? false) !== true)) {
                $status = $data['http'] ?? $code;
                Log::alert("CMA Error changeFlag ({$flag}) {$cntrNumber}: {$status}", ['body' => $body]);
                return ['ok' => false, 'http' => $status, 'data' => $data ?? $body];
            }

            Log::info("CMA OK changeFlag({$flag}) {$cntrNumber}");
            return ['ok' => true, 'http' => $data['http'] ?? $code, 'data' => $data];
        } catch (\Throwable $e) {
            Log::error("CMA Exception changeFlag({$flag}) {$cntrNumber}: {$e->getMessage()}");
            return ['ok' => false, 'http' => 500, 'error' => $e->getMessage()];
        }
        // ---------- POST a n8n ----------
        try {
            $payload = [
                'function'   => __FUNCTION__, // te manda el nombre de la función actual
                'contenedor' => $contenedor->cntr_number,
                'cma_t_o'    => $contenedor->cma_t_o,
                'lat'        => $punto->latitude,
                'lon'        => $punto->longitude,
                'respuesta'  => $r, // lo que devolvió CMA
            ];

            $postRes = $client->post('https://n8n.rail.ar/webhook/reporte-cma', [
                'headers' => $headers,
                'json'    => $payload,
            ]);

            Log::info('Posteado a n8n: ' . $postRes->getBody());
        } catch (\Exception $e) {
            Log::error('Error enviando a n8n: ' . $e->getMessage());
        }
    }
    public function accionLugarAduana($idTrip) // OK
    {

        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr.cntr_number', 'cntr.booking', 'cntr.confirmacion', 'carga.cma_t_o')
            ->join('carga', 'cntr.booking', '=', 'carga.booking')
            ->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];
        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        if ($cntr->cma_t_o != null) {
            $result = $this->cmaChangeFlag($cntr->cntr_number, 2);
        }

        if ($qd->main_status != 'EN ADUANA') {

            DB::table('status')->insert([
                'status' => ' Camión se encuentra en un radio de 200 mts de la aduana Asignada.',
                'main_status' => 'EN ADUANA',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);

            DB::table('cntr')
                ->where('cntr_number', $cntr->cntr_number)
                ->update([
                    'main_status' => 'EN ADUANA',
                    'status_cntr' => 'Camión se encuentra en un radio de 200 mts de la Aduana asignada.'
                ]);
            $cntrs = cntr::where('booking', $cntr->booking)->get();
            // Obtener el status del primer registro
            $primerCntrStatus = $cntrs->first()->main_status;

            // Verificar si todos los registros tienen el mismo status
            $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                return $cntr->main_status == $primerCntrStatus;
            });

            // Si todos los registros tienen el mismo status, actualizar el status de la carga
            if ($equal) {
                Carga::where('booking', $cntr->booking)->update(['status' => $primerCntrStatus]);
            }
            $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
            $description = $qd->status;

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
            $empresa = $qempresa[0]->empresa;

            $datos = [
                'cntr' => $cntr->cntr_number,
                'description' =>  $description,
                'confirmacion' => $cntr->confirmacion,
                'user' => $qd->user_status,
                'empresa' => $empresa,
                'booking' => $cntr->booking,
                'date' => $date
            ];

            //Enviar mail
            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');
            $mailsTrafico = DB::table('particular_soft_configurations')->first();
            $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
            $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();
            $cliente = DB::table('users')
                ->where('cliente_id', '=', $carga->client_id)
                ->first();

            if ($sbx[0]->sandbox == 0) {

                if (!$cliente) {
                    // Logueás un warning para debug
                    Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                    // Podés definir un mail fallback para no perder la notificación
                    $clienteEmail = 'soporte@botzero.com.ar';
                } else {
                    $clienteEmail = $cliente->email;
                }
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaAduana($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaAduana to:" . implode(', ', $toEmails);
                $logApi->save();
            } elseif ($sbx[0]->sandbox == 2) {

                Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaAduana($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaAduana to: 'copia@botzero.com.ar'";
                $logApi->save();
            } else {
                if (!$cliente) {
                    // Logueás un warning para debug
                    Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                    // Podés definir un mail fallback para no perder la notificación
                    $clienteEmail = 'soporte@botzero.com.ar';
                } else {
                    $clienteEmail = $cliente->email;
                }
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaAduana($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaAduana to: 'copia@botzero.com.ar'";
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
                    'status' => ' Camión se encuentra en un radio de 50 mts de la aduana Asignada.',
                    'main_status' => 'EN ADUANA',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'EN ADUANA',
                        'status_cntr' => 'Camión se encuentra en un radio de 200 mts de la Aduana asignada.'
                    ]);
                $cntrs = cntr::where('booking', $cntr->booking)->get();
                // Obtener el status del primer registro
                $primerCntrStatus = $cntrs->first()->main_status;

                // Verificar si todos los registros tienen el mismo status
                $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                    return $cntr->main_status == $primerCntrStatus;
                });

                // Si todos los registros tienen el mismo status, actualizar el status de la carga
                if ($equal) {
                    Carga::where('booking', $cntr->booking)->update(['status' => $primerCntrStatus]);
                }
                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'confirmacion' => $cntr->confirmacion,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                //Enviar mail
                $sbx = DB::table('variables')->select('sandbox')->get();
                $inboxEmail = env('INBOX_EMAIL');
                $mailsTrafico = DB::table('particular_soft_configurations')->first();
                $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
                $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
                $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();
                $cliente = DB::table('users')
                    ->where('cliente_id', '=', $carga->client_id)
                    ->first();


                if ($sbx[0]->sandbox == 0) {
                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }
                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                    Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to:" . implode(', ', $toEmails);
                    $logApi->save();
                } elseif ($sbx[0]->sandbox == 2) {

                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to: 'copia@botzero.com.ar'";
                    $logApi->save();
                } else {
                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }
                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                    Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaAduana($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to: 'copia@botzero.com.ar'";
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
    public function accionLugarDescarga($idTrip) // OK
    {


        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr_number', 'booking', 'confirmacion')->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];
        $contenedor = DB::table('cntr')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->join('carga', 'cntr.booking', '=', 'carga.booking')
            ->where('cntr.id_cntr', $idTrip)
            ->select('cntr.*', 'asign.*', 'carga.*')
            ->first();

        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $contenedor->cntr_number)->latest('id')->first();
        $description = $qd->status;


        if ($contenedor->cma_t_o != null) {

            $base    = rtrim(env('API_CMA_BOTZERO'), '/');
            $client = new Client();
            $headers = ['Content-Type' => 'application/json'];
            $request = new Psr7Request('GET', "{$base}/cma/actArrAtCusLoc/{$contenedor->cntr_number}/{$contenedor->cma_t_o}", $headers);
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);
            Log::info('Respuesta CMA - Act Arr At Cus Loc: ' . $respuesta);

        }
        if ($qd->main_status != 'STACKING') {

            DB::table('status')->insert([
                'status' => 'Camión se encuentra en un radio de 50 mts del Lugar de Descarga.',
                'main_status' => 'STACKING',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);

            DB::table('cntr')
                ->where('cntr_number', $cntr->cntr_number)
                ->update([
                    'main_status' => 'STACKING',
                    'status_cntr' => 'Camión se encuentra en un radio de 200 mts del Lugar de Descarga.'
                ]);
            $cntrs = cntr::where('booking', $cntr->booking)->get();
            // Obtener el status del primer registro
            $primerCntrStatus = $cntrs->first()->main_status;

            // Verificar si todos los registros tienen el mismo status
            $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                return $cntr->main_status == $primerCntrStatus;
            });

            // Si todos los registros tienen el mismo status, actualizar el status de la carga
            if ($equal) {
                Carga::where('booking', $cntr->booking)->update(['status' => $primerCntrStatus]);
            }
            $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
            $description = $qd->status;

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
            $empresa = $qempresa[0]->empresa;

            $datos = [
                'cntr' => $cntr->cntr_number,
                'description' =>  $description,
                'confirmacion' => $cntr->confirmacion,
                'user' => $qd->user_status,
                'empresa' => $empresa,
                'booking' => $cntr->booking,
                'date' => $date
            ];
            
            //Enviar mail
            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');
            $mailsTrafico = DB::table('particular_soft_configurations')->first();
            $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
            $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();
            $cliente = DB::table('users')
                ->where('cliente_id', '=', $carga->client_id)
                ->first();

            if ($sbx[0]->sandbox == 0) {
                if (!$cliente) {
                    // Logueás un warning para debug
                    Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                    // Podés definir un mail fallback para no perder la notificación
                    $clienteEmail = 'soporte@botzero.com.ar';
                } else {
                    $clienteEmail = $cliente->email;
                }
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaDescarga($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaDescarga to:" . implode(', ', $toEmails);
                $logApi->save();
            } elseif ($sbx[0]->sandbox == 2) {

                Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaDescarga($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email Instructivo to: 'copia@botzero.com.ar'";
                $logApi->save();
            } else {
                if (!$cliente) {
                    // Logueás un warning para debug
                    Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                    // Podés definir un mail fallback para no perder la notificación
                    $clienteEmail = 'soporte@botzero.com.ar';
                } else {
                    $clienteEmail = $cliente->email;
                }
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email Instructivo to: 'copia@botzero.com.ar'";
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
                    'status' => 'Camión se encuentra en un radio de 50 mts del Lugar de Descarga.',
                    'main_status' => 'STACKING',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'STACKING',
                        'status_cntr' => 'Camión se encuentra en un radio de 200 mts del Lugar de Descarga.'
                    ]);
                $cntrs = cntr::where('booking', $cntr->booking)->get();
                // Obtener el status del primer registro
                $primerCntrStatus = $cntrs->first()->main_status;

                // Verificar si todos los registros tienen el mismo status
                $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                    return $cntr->main_status == $primerCntrStatus;
                });

                // Si todos los registros tienen el mismo status, actualizar el status de la carga
                if ($equal) {
                    Carga::where('booking', $cntr->booking)->update(['status' => $primerCntrStatus]);
                }
                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'confirmacion' => $cntr->confirmacion,
                    'description' =>  $description,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];




                //Enviar mail
                $sbx = DB::table('variables')->select('sandbox')->get();
                $inboxEmail = env('INBOX_EMAIL');
                $mailsTrafico = DB::table('particular_soft_configurations')->first();
                $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
                $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
                $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();
                $cliente = DB::table('users')
                    ->where('cliente_id', '=', $carga->client_id)
                    ->first();


                if ($sbx[0]->sandbox == 0) {
                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }
                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                    Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaDescarga($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaDescarga to:" . implode(', ', $toEmails);
                    $logApi->save();
                } elseif ($sbx[0]->sandbox == 2) {
                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaDescarga($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email Instructivo to: 'copia@botzero.com.ar'";
                    $logApi->save();
                } else {
                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }
                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email Instructivo to: 'copia@botzero.com.ar'";
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
    public function accionFueraLugarDeCarga($idTrip) //ok
    {
       
        $date = Carbon::now('-03:00');
        $qc = DB::table('cntr')->select('cntr_number', 'booking', 'confirmacion')->where('id_cntr', '=', $idTrip)->get();
        $cntr = $qc[0];
        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        /* ++++++++++++++++++++++++++ ACCION CMA (no corta el flujo) +++++++++++++++++++++++++++ */

        $cmaResults = ['steps' => [], 'errors' => []];

        $place = DB::table('carga as c')
            ->join('customer_load_places as clp', 'c.load_place', '=', 'clp.description')
            ->where('c.booking', $cntr->booking)
            ->select('c.cma_t_o', 'clp.latitud', 'clp.longitud')
            ->first();

        if (!$place || empty($place->cma_t_o)) {
            Log::warning('CMA: sin cma_t_o para booking ' . ($cntr->booking ?? 'N/D'));
        } else {

            Log::info('-----| CARGA CMA | ------');

            $cma_t_o = $place->cma_t_o;
            $lat     = $place->latitud;
            $lon     = $place->longitud;

            $base    = rtrim(env('API_CMA_BOTZERO'), '/');
            $headers = ['Content-Type' => 'application/json'];

            $client = new Client([
                'http_errors' => false, // no lance excepción en 4xx/5xx
                'timeout'     => 30,
            ]);

            // Helper para enviar y loguear sin cortar
            $send = function (string $label, string $url) use ($client, $headers, &$cmaResults) {
                log::info('Usamos:' . $label);
                try {
                    $req   = new Psr7Request('GET', $url, $headers);
                    $res   = $client->send($req);
                    $body  = (string) $res->getBody();
                    $data  = json_decode($body, true);
                    return $data;

                    if (!is_array($data) || (($data['ok'] ?? false) !== true)) {
                        $status = $data['http'] ?? $res->getStatusCode();
                        Log::alert("CMA Error {$label}: {$status}", ['body' => $body]);
                        $cmaResults['errors'][] = ['step' => $label, 'status' => $status, 'body' => $body];
                    } else {
                        $cmaResults['steps'][$label] = $data;
                        Log::info('-----| CARGA CMA | Status: ok ------' . $cmaResults['steps'][$label] = $data);
                    }
                } catch (\Throwable $e) {
                    Log::error("CMA Exception {$label}: " . $e->getMessage());
                    $cmaResults['errors'][] = ['step' => $label, 'exception' => $e->getMessage()];
                }
            };

            // 1) ACT departure at customer location
            $send(
                'actDepCustLoc',
                "{$base}/cma/actDepCustLoc/{$cntr->cntr_number}/{$cma_t_o}"
            );

            // 2) EST arrival at customer location
            $send(
                'estArrAtCusLoc',
                "{$base}/cma/estArrAtCusLoc/{$cma_t_o}/{$cntr->cntr_number}/{$lat}/{$lon}"
            );

            // 3) cambio de flag (1)
            $send(
                'changeFlag1',
                "{$base}/cma/changeFlag/1/{$cntr->cntr_number}"
            );
            
            // Si querés un resumen en logs al final del bloque:
            if (!empty($cmaResults['errors'])) {
                Log::warning('CMA finalizado con errores', $cmaResults['errors']);
            } else {
                Log::info('CMA finalizado OK', array_keys($cmaResults['steps']));
            }
        }


        /* +++++++++++++++++++++++++++ FIN ACCION CMA  */

        if ($qd->main_status != 'SALIENDO CARGAR' && $qd->avisado == 1) {

            DB::table('status')->insert([
                'status' => 'El camión ha salido del área de carga y se encuentra a más de 200 metros del lugar.',
                'main_status' => 'SALIENDO CARGAR',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);
            DB::table('cntr')
                ->where('cntr_number', $cntr->cntr_number)
                ->update([
                    'main_status' => 'SALIENDO CARGAR',
                    'status_cntr' => 'El camión ha salido del área de carga y se encuentra a más de 200 metros del lugar.'
                ]);

            $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
            $description = $qd->status;

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
            $empresa = $qempresa[0]->empresa;

            $datos = [
                'cntr' => $cntr->cntr_number,
                'confirmacion' => $cntr->confirmacion,
                'description' =>  $description,
                'user' => $qd->user_status,
                'empresa' => $empresa,
                'booking' => $cntr->booking,
                'date' => $date
            ];

            //Enviar mail
            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');
            $mailsTrafico = DB::table('particular_soft_configurations')->first();
            $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
            $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();
            $cliente = DB::table('users')
                ->where('cliente_id', '=', $carga->client_id)
                ->first();
            if ($sbx[0]->sandbox == 0) {
                if (!$cliente) {
                    // Logueás un warning para debug
                    Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                    // Podés definir un mail fallback para no perder la notificación
                    $clienteEmail = 'soporte@botzero.com.ar';
                } else {
                    $clienteEmail = $cliente->email;
                }
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);
                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaFueraCargando($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to:" . implode(', ', $toEmails);
                $logApi->save();
            } elseif ($sbx[0]->sandbox == 2) {

                Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaFueraCargando($datos));

                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to: abel.mazzitelli@gmail.com";
                $logApi->save();
            } else {

                if (!$cliente) {
                    // Logueás un warning para debug
                    Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                    // Podés definir un mail fallback para no perder la notificación
                    $clienteEmail = 'soporte@botzero.com.ar';
                } else {
                    $clienteEmail = $cliente->email;
                }
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);
                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaFueraCargando($datos));



                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaCargando to: copia@botzero.com.ar";
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
                    'status' => 'El camión ha salido del área de carga y se encuentra a más de 200 metros del lugar.',
                    'main_status' => 'SALIENDO CARGAR',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'SALIENDO CARGAR',
                        'status_cntr' => 'El camión ha salido del área de carga y se encuentra a más de 200 metros del lugar.'
                    ]);

                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'confirmacion' => $cntr->confirmacion,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                //Enviar mail
                $sbx = DB::table('variables')->select('sandbox')->get();
                $inboxEmail = env('INBOX_EMAIL');
                $mailsTrafico = DB::table('particular_soft_configurations')->first();
                $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
                $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
                $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();
                $cliente = DB::table('users')
                    ->where('cliente_id', '=', $carga->client_id)
                    ->first();


                if ($sbx[0]->sandbox == 0) {
                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }
                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                    Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaFueraCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to:" . implode(', ', $toEmails);
                    $logApi->save();
                } elseif ($sbx[0]->sandbox == 2) {

                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaFueraCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to: abel.mazzitelli@gmail.com";
                    $logApi->save();
                } else {
                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }
                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                    Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaFueraCargando($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaCargando to: copia@botzero.com.ar";
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
    public function accionFueraLugarAduana($idTrip) //ok
    {

        $date = Carbon::now('-03:00');
        $cntr = DB::table('cntr')->select('cntr_number', 'booking', 'confirmacion')
            ->join('carga', 'cntr.booking', '=', 'carga.booking')
            ->select('cntr.cntr_number', 'cntr.booking', 'cntr.confirmacion', 'carga.cma_t_o')
            ->where('cntr.id_cntr', '=', $idTrip)
            ->first();


        // cual es el ultimo status.
        $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
        $description = $qd->status;

        if ($cntr->cma_t_o != null) {

            $place = DB::table('carga as c')
                ->join('aduanas as ad', 'c.custom_place', '=', 'ad.description')
                ->where('c.booking', $cntr->booking)
                ->select('c.cma_t_o', 'ad.lat', 'ad.lon')
                ->first();
            if (!$place) {

                $place = DB::table('carga as c')
                    ->join('aduanas as ad', 'c.custom_place_impo', '=', 'ad.description')
                    ->where('c.booking', $cntr->booking)
                    ->select('c.cma_t_o', 'ad.lat', 'ad.lon')
                    ->first();
            }

            $base    = rtrim(env('API_CMA_BOTZERO'), '/');
            $client = new Client();
            $headers = ['Content-Type' => 'application/json'];
            $request = new Psr7Request('GET', "{$base}/cma/estArrAtCusLoc/{$cntr->cma_t_o}/{$cntr->cntr_number}/{$place->lat}/{$place->lon}", $headers);
            $res = $client->sendAsync($request)->wait();
            $respuesta = $res->getBody();
            $r = json_decode($respuesta, true);

            Log::info('CMA - Est Arr At Cus Loc: ' . $respuesta);
           // ++++++++++++++ ACCION CMA +++++++++++++++ */
        $result = $this->cmaChangeFlag($cntr->cntr_number, 3);
        Log::info('CMA - Change Flag 3: ' . json_encode($result));  
        /* ++++++++++++++ FIN ACCION CMA +++++++++++ */

            
        }

        


        if ($qd->main_status != 'YENDO A DESCARGAR' && $qd->avisado == 1) {

            DB::table('status')->insert([
                'status' => 'El camión ha salido de la aduana asignada y se encuentra a más de 200 metros del lugar.',
                'main_status' => 'YENDO A DESCARGAR',
                'cntr_number' => $cntr->cntr_number,
                'user_status' => 'AUTOMATICO',
            ]);

            DB::table('cntr')
                ->where('cntr_number', $cntr->cntr_number)
                ->update([
                    'main_status' => 'YENDO A DESCARGAR',
                    'status_cntr' => 'El camión ha salido de la aduana asignada y se encuentra a más de 200 metros del lugar.'
                ]);
            $cntrs = cntr::where('booking', $cntr->booking)->get();
            // Obtener el status del primer registro
            $primerCntrStatus = $cntrs->first()->main_status;

            // Verificar si todos los registros tienen el mismo status
            $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                return $cntr->main_status == $primerCntrStatus;
            });

            // Si todos los registros tienen el mismo status, actualizar el status de la carga
            if ($equal) {
                Carga::where('booking', $cntr->booking)->update(['status' => $primerCntrStatus]);
            }
            $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
            $description = $qd->status;

            $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
            $empresa = $qempresa[0]->empresa;

            $datos = [
                'cntr' => $cntr->cntr_number,
                'description' =>  $description,
                'confirmacion' => $cntr->confirmacion,
                'user' => $qd->user_status,
                'empresa' => $empresa,
                'booking' => $cntr->booking,
                'date' => $date
            ];

            //Enviar mail
            $sbx = DB::table('variables')->select('sandbox')->get();
            $inboxEmail = env('INBOX_EMAIL');
            $mailsTrafico = DB::table('particular_soft_configurations')->first();
            $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
            $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
            $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();
            $cliente = DB::table('users')
                ->where('cliente_id', '=', $carga->client_id)
                ->first();

            if ($sbx[0]->sandbox == 0) {
                if (!$cliente) {
                    // Logueás un warning para debug
                    Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                    // Podés definir un mail fallback para no perder la notificación
                    $clienteEmail = 'soporte@botzero.com.ar';
                } else {
                    $clienteEmail = $cliente->email;
                }
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaFueraAduana($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaAduana to:" . implode(', ', $toEmails);
                $logApi->save();
            } elseif ($sbx[0]->sandbox == 2) {

                Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaFueraAduana($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaAduana to: abel.mazzitelli@gmail.com";
                $logApi->save();
            } else {
                if (!$cliente) {
                    // Logueás un warning para debug
                    Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                    // Podés definir un mail fallback para no perder la notificación
                    $clienteEmail = 'soporte@botzero.com.ar';
                } else {
                    $clienteEmail = $cliente->email;
                }
                $customer = DB::table('users')
                    ->where('username', '=', $carga->user)
                    ->value('email');
                $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaFueraAduana($datos));
                $logApi = new logapi();
                $logApi->user = 'No Informa';
                $logApi->detalle = "envio email cargaAduana to: 'copia@botzero.com.ar'";
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
                    'status' => 'El camión ha salido de la aduana asignada y se encuentra a más de 200 metros del lugar.',
                    'main_status' => 'YENDO A DESCARGAR',
                    'cntr_number' => $cntr->cntr_number,
                    'user_status' => 'AUTOMATICO',
                ]);

                DB::table('cntr')
                    ->where('cntr_number', $cntr->cntr_number)
                    ->update([
                        'main_status' => 'YENDO A DESCARGAR',
                        'status_cntr' => 'El camión ha salido de la aduana asignada y se encuentra a más de 200 metros del lugar.'
                    ]);
                $cntrs = cntr::where('booking', $cntr->booking)->get();
                // Obtener el status del primer registro
                $primerCntrStatus = $cntrs->first()->main_status;

                // Verificar si todos los registros tienen el mismo status
                $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                    return $cntr->main_status == $primerCntrStatus;
                });

                // Si todos los registros tienen el mismo status, actualizar el status de la carga
                if ($equal) {
                    Carga::where('booking', $cntr->booking)->update(['status' => $primerCntrStatus]);
                }
                $qd  = DB::table('status')->where('cntr_number', '=', $cntr->cntr_number)->latest('id')->first();
                $description = $qd->status;

                $qempresa = DB::table('carga')->select('empresa')->where('booking', '=', $cntr->booking)->get();
                $empresa = $qempresa[0]->empresa;

                $datos = [
                    'cntr' => $cntr->cntr_number,
                    'description' =>  $description,
                    'confirmacion' => $cntr->confirmacion,
                    'user' => $qd->user_status,
                    'empresa' => $empresa,
                    'booking' => $cntr->booking,
                    'date' => $date
                ];

                //Enviar mail
                $sbx = DB::table('variables')->select('sandbox')->get();
                $inboxEmail = env('INBOX_EMAIL');
                $mailsTrafico = DB::table('particular_soft_configurations')->first();
                $toEmails = explode(',', $mailsTrafico->to_mail_trafico_Team);
                $ccEmails = explode(',', $mailsTrafico->cc_mail_trafico_Team);
                $carga = Carga::whereNull('deleted_at')->where('booking', '=', $cntr->booking)->first();
                $cliente = DB::table('users')
                    ->where('cliente_id', '=', $carga->client_id)
                    ->first();

                if ($sbx[0]->sandbox == 0) {
                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }

                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                    Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaFueraAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to: " . implode(', ', $toEmails);
                    $logApi->save();
                } elseif ($sbx[0]->sandbox == 2) {

                    Mail::to('abel.mazzitelli@gmail.com')->bcc($inboxEmail)->send(new cargaFueraAduana($datos));
                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to: abel.mazzitelli@gmail.com";
                    $logApi->save();
                } else {
                    if (!$cliente) {
                        // Logueás un warning para debug
                        Log::warning("Cliente no encontrado para carga ID {$carga->id} (booking {$carga->booking})");

                        // Podés definir un mail fallback para no perder la notificación
                        $clienteEmail = 'soporte@botzero.com.ar';
                    } else {
                        $clienteEmail = $cliente->email;
                    }

                    $customer = DB::table('users')
                        ->where('username', '=', $carga->user)
                        ->value('email');
                    $toEmails = array_merge([$customer, $clienteEmail], (array) $toEmails);

                    Mail::to($toEmails)->cc($ccEmails)->bcc($inboxEmail)->send(new cargaFueraAduana($datos));

                    $logApi = new logapi();
                    $logApi->user = 'No Informa';
                    $logApi->detalle = "envio email cargaAduana to: 'copia@botzero.com.ar'";
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
    public function index()
    {
        try {
            $customerLoadPlaces = CustomerLoadPlace::orderBy('description', 'ASC')->get();
            return response()->json([
                'data' => $customerLoadPlaces,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function indexCompany($company)
    {
        try {
            $customerLoadPlaces = CustomerLoadPlace::where('company', $company)->orderBy('description', 'ASC')->get();
            return response()->json([
                'data' => $customerLoadPlaces,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function issetLugarDeCarga(Request $request)
    {
        $description = $request->input('loadplace');
        $loadPlace = DB::table('customer_load_places')->where('description', '=', $description)->count();
        return $loadPlace;
    }
    public function issetLugarDeDescarga(Request $request)
    {
        $description = $request->input('unloadplace');
        $unloadPlace = DB::table('customer_unload_places')->where('description', '=', $description)->count();
        return $unloadPlace;
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'km_from_town' => 'nullable|string',
                'remarks' => 'nullable|string|max:255',
                'latitud' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
                'longitud' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
                'link_maps' => 'nullable|string|max:255',
                'user' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
            ]);

            $customerLoadPlace = CustomerLoadPlace::create($validated);

            return response()->json([
                'message' => 'Lugar de carga creada con éxito',
                'data' => $customerLoadPlace
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores si algo falla
            return response()->json([
                'message' => 'No se pudo crear el Lugar de carga',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        $customerLoadPlace = DB::table('customer_load_places')->where('id', '=', $id)->get();
        return $customerLoadPlace;
    }
    public function edit(CustomerLoadPlace $customerLoadPlace)
    {
        //
    }
    public function update(Request $request,  $id)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'km_from_town' => 'nullable|string',
                'remarks' => 'nullable|string|max:255',
                'latitud' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
                'longitud' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
                'link_maps' => 'nullable|string|max:255',
                'user' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
            ]);
            $customerLoadPlace = CustomerLoadPlace::findOrFail($id);
            $customerLoadPlace->update($validated);

            return response()->json([
                'message' => 'Lugar de carga actualizado con éxito',
                'data' => $customerLoadPlace
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el Lugar de carga',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            CustomerLoadPlace::destroy($id);

            $existe = CustomerLoadPlace::find($id);
            if ($existe) {
                return response()->json([
                    'message' => 'No se eliminó el Lugar de Carga. Inténtalo de nuevo.',
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Lugar de Carga eliminado con éxito.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el Lugar de Carga.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
