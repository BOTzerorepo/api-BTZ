<?php

namespace App\Http\Controllers;

use App\Models\statu;
use App\Models\asign;
use App\Models\Driver;
use App\Models\cntr;
use App\Models\Carga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Http\Controllers\emailController;

class statusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return statu::all();
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
    public function updateStatusCarga(Request $request)
    {

        $booking = $request['booking'];
        $carga = Carga::where('booking', $booking)->first();
        $idCarga = $carga->id;

        try {
            //------------GENERAL--------------------
            $request->validate([
                'user' => 'required',
                'empresa' => 'required',
                'booking' => 'required',
                'statusGral' => 'required',
                'description' => 'required',
            ]);

            //Datos que recibe del front
            $cntr = $request['cntr'];
            $description = $request['description'];
            $user = $request['user'];
            $empresa = $request['empresa'];
            $booking = $request['booking'];
            $statusGral = $request['statusGral'];
            //$statusArchivo = $request->file('statusArchivo');
            
            if ($request->hasFile('statusArchivo')) {
                $statusArchivo = $request->file('statusArchivo');
                $folder = 'status/'. $idCarga ;
            
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }
                // Genera un nombre único basado en el idCarga y statusGral
                $nombreArchivo =  $statusGral . '.' . $statusArchivo->getClientOriginalExtension();
                // Mueve el archivo a la ubicación específica con el nombre único
                $statusArchivo->storeAs($folder, $nombreArchivo);
                // Resto del código si es necesario
                // Después de guardar el archivo
                $statusArchivoPath = $folder . '/' . $nombreArchivo;
            }else{
                $statusArchivoPath = null;
            }
            //------------GENERAL--------------------

            if ($statusGral == "TERMINADA") {
                
                // ACTUALIZA STATUS
                $status = new statu([
                    'status' => $description,
                    'main_status' => $statusGral,
                    'cntr_number' => $cntr,
                    'user_status' => $user,
                ]);
                $status->save();

                // Realiza la consulta buscando el cntr
                $cntrModel = cntr::where('cntr_number', $cntr)->firstOrFail();
                $id_cntr = $cntrModel->id_cntr;
                // SI ESTA TODO OK --> LOGICA DE STATUS GENERAL
                // ACTULIZAMOS EL STATUS EN EL CNTR
                $cntrModel->main_status = $statusGral;
                $cntrModel->status_cntr = $description;
                $cntrModel->save();

                // REVISAMOS COMO ESTAN LOS DEMAS CNTR
                $cntrs = cntr::where('booking', $booking)->get();
            
                // Obtener el status del primer registro
                $primerCntrStatus = $cntrs->first()->main_status;

                // Verificar si todos los registros tienen el mismo status
                $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                    return $cntr->main_status == $primerCntrStatus;
                });

                // Si todos los registros tienen el mismo status, actualizar el status de la carga
                if ($equal) {
                    Carga::where('booking', $booking)->update(['status' => $primerCntrStatus]);
                }
              
                // Devolver una respuesta JSON con información de éxito
                return response()->json([
                    'id' => $idCarga,
                    'message' => 'Se modificó el satus a: ' . $statusGral,
                ], 200);

            }elseif ($statusGral == "CON PROBLEMA") {
                // SI TIENE PROBLEMAS.
                // ACTUALIZA STATUS
                $status = new statu([
                    'status' => $description,
                    'main_status' => $statusGral,
                    'cntr_number' => $cntr,
                    'user_status' => $user,
                ]);
                $tipo = 'problema';
                $status->save();
            
                // ENVIAMOS MAIL
                // Crear una instancia del controlador
                $emailController = new emailController();
                // Llamar directamente a la función mailStatus
                $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath);

                if ($response == 'ok') {

                    // si todo esta ok, Acualizamos el estado del CNTR
                    $cntrModel = cntr::where('cntr_number', $cntr)->firstOrFail();
                    $cntrModel->main_status = $statusGral;
                    $cntrModel->status_cntr = $description;
                    $cntrModel->save();

                    // Luego revisamos el status de los demás contenedores de la Carga. 
                    // Buscar todos los registros Cntr asociados a la booking
                    $cntrs = cntr::where('booking', $booking)->get();

                    // Obtener el status del primer registro
                    $primerCntrStatus = $cntrs->first()->main_status;

                    // Verificar si todos los registros tienen el mismo status
                    $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                        return $cntr->main_status == $primerCntrStatus;
                    });

                    // Si todos los registros tienen el mismo status, actualizar el status de la carga
                    if ($equal) {
                        Carga::where('booking', $booking)->update(['status' => $primerCntrStatus]);
                    }

                    // ARMAMOS NOTIFICACION 
                    $cntrModel = cntr::where('cntr_number', $cntr)->first();
                    if ($cntrModel) {
                        $user_to = $cntrModel->user_cntr;
                    }
            
                    DB::table('notification')->insert([
                      'title' => 'Carga ' . $cntr . ' con Problemas',
                      'description' => $description,
                      'user_to' => $user_to,
                      'status' => 'No Leido',
                      'sta_carga' => 'CON PROBLEMA',
                      'user_create' => $user,
                      'company_create' => $empresa,
                      'cntr_number' => $cntr,
                      'booking' => $booking,
                    ]);
                
                    // Devolver una respuesta JSON con información de éxito
                    return response()->json([
                        'id' => $idCarga,
                        'message' => 'Se modificó el satus a: ' . $statusGral .' y avisado por Correo al Cliente' ,
                    ], 200);

                } else {
                    $errores[] = 'Algo salió mal, por favor vuelta a intentar la acción.';
                    return response()->json(['errores' => $errores, 'id' => $idCarga], 500);

                }
            }elseif ($statusGral == "STACKING") {
                // si la carga está en Staking, Actualizamos el Status en la tabla Status
                $status = new statu([
                    'status' => $description,
                    'main_status' => $statusGral,
                    'cntr_number' => $cntr,
                    'user_status' => $user,
                ]);
                $tipo = 'stacking';
                $status->save();
            
                // ENVIAMOS MAIL
                // Crear una instancia del controlador
                $emailController = new emailController();
                // Llamar directamente a la función mailStatus
                $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath);

                if ($response == 'ok') {

                    // si todo esta ok, Acualizamos el estado del CNTR
                    $cntrModel = cntr::where('cntr_number', $cntr)->firstOrFail();
                    $cntrModel->main_status = $statusGral;
                    $cntrModel->status_cntr = $description;
                    $cntrModel->save();

                    // Luego revisamos el status de los demás contenedores de la Carga. 
                    // Buscar todos los registros Cntr asociados a la booking
                    $cntrs = cntr::where('booking', $booking)->get();

                    // Obtener el status del primer registro
                    $primerCntrStatus = $cntrs->first()->main_status;

                    // Verificar si todos los registros tienen el mismo status
                    $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                        return $cntr->main_status == $primerCntrStatus;
                    });

                    // Si todos los registros tienen el mismo status, actualizar el status de la carga
                    if ($equal) {
                        Carga::where('booking', $booking)->update(['status' => $primerCntrStatus]);
                    }

                    // cambiamos el estado del Chofer
                    $port = Carga::where('booking', $booking)->value('unload_place');
            
                    // Obtener el chofer desde la asignación
                    $chofer = Asign::where('booking', $booking)
                    ->where('cntr_number', $cntr)
                    ->value('driver');
                    
                    // Actualizar el estado del chofer en la tabla 'drivers'
                    Driver::where('nombre', $chofer)->update(['status_chofer' => 'libre', 'place' => $port]);
                    
                    // Devolver una respuesta JSON con información de éxito
                    return response()->json([
                    'id' => $idCarga,
                    'message' => 'Se modificó el satus a: ' . $statusGral .' y avisado por Correo al Cliente' ,
                    ], 200);
            
                } else {
                    $errores[] = 'Algo salió mal, por favor vuelta a intentar la acción.';
                    return response()->json(['errores' => $errores, 'id' => $idCarga], 500);
                }
            }else {

                // Insertamos Status en la tabla de Status
                $status = new statu([
                    'status' => $description,
                    'main_status' => $statusGral,
                    'cntr_number' => $cntr,
                    'user_status' => $user,
                ]);
                $tipo = 'cambio';
                $status->save();
            
                // ENVIAMOS MAIL
                // Crear una instancia del controlador
                $emailController = new emailController();
                // Llamar directamente a la función mailStatus
                $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath);

                if ($response == 'ok') {
                
                    // si todo esta ok, Acualizamos el estado del CNTR
                    $cntrModel = cntr::where('cntr_number', $cntr)->firstOrFail();
                    $cntrModel->main_status = $statusGral;
                    $cntrModel->status_cntr = $description;
                    $cntrModel->save();
            
                    // Luego revisamos el status de los demás contenedores de la Carga. 
                    // Buscar todos los registros Cntr asociados a la booking
                    $cntrs = cntr::where('booking', $booking)->get();

                    // Obtener el status del primer registro
                    $primerCntrStatus = $cntrs->first()->main_status;

                    // Verificar si todos los registros tienen el mismo status
                    $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                        return $cntr->main_status == $primerCntrStatus;
                    });

                    // Si todos los registros tienen el mismo status, actualizar el status de la carga
                    if ($equal) {
                        Carga::where('booking', $booking)->update(['status' => $primerCntrStatus]);
                    }
            
                    return response()->json([
                        'id' => $idCarga,
                        'message' => 'Se modificó el satus a: ' . $statusGral .' y avisado por Correo al Cliente' ,
                    ], 200);
            
                } else {
                    $errores[] = 'Algo salió mal, por favor vuelta a intentar la acción.';
                    return response()->json(['errores' => $errores, 'id' => $idCarga], 500);
                }
            }
        } catch (ModelNotFoundException $e) {
            // Manejar la excepción específica para ModelNotFoundException
            $errores[] = 'No se encontró el registro. Detalles: ' . $e->getMessage();
            return response()->json(['errores' => $errores, 'id' => $idCarga], 404);
        } catch (ValidationException $e) {
            // Manejar la excepción específica para ValidationException
            $errores[] = 'Error de validación. Detalles: ' . $e->getMessage();
            return response()->json(['errores' => $errores, 'id' => $idCarga], 422);
        } catch (Exception $e) {
            // Manejar otras excepciones genéricas
            $errores[] = 'Error general. Detalles: ' . $e->getMessage();
            return response()->json(['errores' => $errores, 'id' => $idCarga], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showLast($id)
    {
       return statu::find($id);
    }
    public function showHistory($cntr)
    {
      
       return DB::table('status')->where('cntr_number','=',$cntr)->orderBy('created_at','DESC')->get();
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