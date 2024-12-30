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
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Http\Controllers\emailController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\cargaTerminada;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\Transport;


use function GuzzleHttp\json_encode;

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
    public function indexActive()
    {
        $cargasActivas = DB::table('cntr')
        ->join('carga', 'cntr.booking', '=', 'carga.booking')
        ->join('asign', 'asign.cntr_number', '=', 'cntr.cntr_number')
        ->join('status', 'status.cntr_number', '=', 'cntr.cntr_number')
        ->leftjoin('trucks', 'trucks.domain', '=', 'asign.truck')
        ->select(
            'cntr.id_cntr',
            'carga.ref_customer',
            'cntr.booking',
            'cntr.cntr_number',
            'cntr.cntr_type',
            'cntr.confirmacion',
            'cntr.main_status',
            'cntr.status_cntr',
            'asign.driver',
            'asign.truck',
            'asign.truck_semi',
            'asign.transport',
            'trucks.alta_aker',
            DB::raw('MAX(status.id) as latest_status_id') // Selecciona el último status basado en el id
        )
        ->where('cntr.main_status', '!=', 'TERMINADA')
        ->where('carga.deleted_at', '=', null)
        ->groupBy(
            'cntr.id_cntr',
            'carga.ref_customer',
            'cntr.booking',
            'cntr.cntr_number',
            'cntr.cntr_type',
            'cntr.confirmacion',
            'cntr.main_status',
            'cntr.status_cntr',
            'asign.driver',
            'asign.truck',
            'asign.truck_semi',
            'asign.transport',
            'trucks.alta_aker'

        )
        ->get();

        return $cargasActivas;

    }
    public function indexTransportActive($ids)
{
    // Convertir la cadena de IDs separados por comas en un array
    $idArray = explode(',', $ids);

    // Buscar los transportes cuyos IDs estén en la lista
    $transportes = Transport::whereIn('id', $idArray)->get();

    // Crear un array para almacenar las razones sociales
    $rzTransportes = $transportes->pluck('razon_social')->toArray();

    // Realizar la consulta de las cargas activas para todos los transportes
    $cargasActivas = DB::table('cntr')
        ->join('carga', 'cntr.booking', '=', 'carga.booking')
        ->leftJoin('asign', 'asign.cntr_number', '=', 'cntr.cntr_number')
        ->leftJoin('status', 'status.cntr_number', '=', 'cntr.cntr_number')
        ->leftJoin('trucks', 'trucks.domain', '=', 'asign.truck')
        ->whereIn('asign.transport', $rzTransportes) // Filtrar por las razones sociales de los transportes
        ->select(
            'cntr.id_cntr',
            'carga.ref_customer',
            'cntr.booking',
            'cntr.cntr_number',
            'cntr.cntr_type',
            'cntr.confirmacion',
            'cntr.main_status',
            'cntr.status_cntr',
            'asign.driver',
            'asign.truck',
            'asign.truck_semi',
            'asign.transport',
            'asign.file_instruction',
            'trucks.alta_aker',
            DB::raw('MAX(status.id) as latest_status_id') // Seleccionar el último status basado en el id
        )
        ->where('cntr.main_status', '!=', 'TERMINADA')
        ->groupBy(
            'cntr.id_cntr',
            'carga.ref_customer',
            'cntr.booking',
            'cntr.cntr_number',
            'cntr.cntr_type',
            'cntr.confirmacion',
            'cntr.main_status',
            'cntr.status_cntr',
            'asign.driver',
            'asign.truck',
            'asign.truck_semi',
            'asign.transport',
            'trucks.alta_aker',
            'asign.file_instruction'
        )
        ->get();

    return $cargasActivas;
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
        


        DB::beginTransaction();

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
            $description = $request['description'];
            $statusGral = $request['statusGral'];
            $user = $request['user'];
            $cntr = $request['cntr'];
           
            $empresa = $request['empresa'];
            //$statusArchivo = $request->file('statusArchivo');
            
            // ACTUALIZA STATUS
            $status = new statu([
                'status' => $description,
                'main_status' => $statusGral,
                'cntr_number' => $cntr,
                'user_status' => $user,
            ]);
                  
            // Guarda el modelo para obtener el ID
            $status->save();

            if ($request->hasFile('statusArchivo')) {

                $statusArchivo = $request->file('statusArchivo');
                $folder = 'status/'. $idCarga;
            
                // Genera un nombre único basado en el idCarga y statusGral
                $nombreArchivo =  $status->id . '.' . $statusArchivo->getClientOriginalExtension();
                // Almacena el archivo en storage/app/public/status/idCarga/
                Storage::disk('public')->putFileAs($folder, $statusArchivo, $nombreArchivo);
                // Resto del código si es necesario
                // Después de guardar el archivo
                $statusArchivoPath = $folder . '/' . $nombreArchivo;
                $status->documento = $idCarga . '/' .$nombreArchivo;
                $status->extension = $statusArchivo->getClientOriginalExtension();
               

            }else{
                $statusArchivoPath = null;

            }
            $status->save();

            

            //------------GENERAL--------------------
            if ($statusGral == "TERMINADA") {


                // ACTUALIZA STATUS
                $tipo = 'terminada';

                $emailController = new emailController();
                // Llamar directamente a la función mailStatus
                $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath);
                if ($response == 'ok') {

                    $cntrModel = cntr::where('cntr_number', $cntr)->firstOrFail();
                    $cntrModel->main_status = $statusGral;
                    $cntrModel->status_cntr = $description;
                    $cntrModel->save();

                    $cntrs = cntr::where('booking', $booking)->get();
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

                    DB::commit();
                    // Devolver una respuesta JSON con información de éxito
                    return response()->json([
                        'id' => $idCarga,
                        'errores' => 'Se modificó el satus a: ' . $statusGral . ' y avisado por Correo al Cliente',
                    ], 200);
                } else {
                    DB::rollBack();
                    return response()->json(['errores' => 'Algo salió mal, por favor vuelta a intentar la acción.', 'id' => $idCarga], 500);
                }
                
            } elseif ($statusGral == "CON PROBLEMA") {
                // SI TIENE PROBLEMAS.
                // ACTUALIZA STATUS
                $tipo = 'problema';
            
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
                
                    DB::commit();
                    // Devolver una respuesta JSON con información de éxito
                    return response()->json([
                        'id' => $idCarga,
                        'errores' => 'Se modificó el satus a: ' . $statusGral .' y avisado por Correo al Cliente' ,
                    ], 200);

                } else {
                    DB::rollBack();
                    return response()->json(['errores' => 'Algo salió mal, por favor vuelta a intentar la acción.', 'id' => $idCarga], 500);
                }
            }elseif ($statusGral == "STACKING") {
                // si la carga está en Staking, Actualizamos el Status en la tabla Status

                $tipo = 'stacking';
            
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
                    
                    DB::commit();
                    // Devolver una respuesta JSON con información de éxito
                    return response()->json([
                    'id' => $idCarga,
                    'errores' => 'Se modificó el satus a: ' . $statusGral .' y avisado por Correo al Cliente' ,
                    ], 200);
            
                } else {
                    DB::rollBack();
                    return response()->json(['errores' => 'Algo salió mal, por favor vuelta a intentar la acción.', 'id' => $idCarga], 500);
                }
            }else {
                // Insertamos Status en la tabla de Status         
                $tipo = 'cambio';
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
                    DB::commit();
                    return response()->json([
                        'id' => $idCarga,
                        'errores' => 'Se modificó el satus a: ' . $statusGral .' y avisado por Correo al Cliente' ,
                    ], 200);
            
                } else {
                    DB::rollBack();
                    return response()->json(['errores' => 'Algo salió mal, por favor vuelta a intentar la acción.', 'id' => $idCarga], 500);
                }
            }
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            // Manejar la excepción específica para ModelNotFoundException
            //$errores[] = 'No se encontró el registro. Detalles: ' . $e->getMessage();
            return response()->json(['errores' => 'No se encontró el registro. Detalles: ' . $e->getMessage(), 'id' => $idCarga], 404);
        } catch (ValidationException $e) {
            DB::rollBack();
            // Manejar la excepción específica para ValidationException
            //$errores[] = 'Error de validación. Detalles: ' . $e->getMessage();
            return response()->json(['errores' => 'Error de validación. Detalles: ' . $e->getMessage(), 'id' => $idCarga], 422);
        } catch (Exception $e) {
            DB::rollBack();
            // Manejar otras excepciones genéricas
            $errores[] = 'Error general. Detalles: ' . $e->getMessage();
            return response()->json(['errores' => 'Error general. Detalles: ' . $e->getMessage(), 'id' => $idCarga], 500);
        }
    }

    public function obtenerDocumentosCarga($booking)
    {
        $carga = Carga::where('booking', $booking)->first();

        if (!$carga) {
            return response()->json(['mensaje' => 'No se encontró la carga asociada al booking'], 404);
        }

        $idCarga = $carga->id;
        $folder = 'status/' . $idCarga;

        // Verificar si la carpeta existe
        if (Storage::exists($folder)) {
            // Obtener la lista de archivos en la carpeta
            $archivos = Storage::files($folder);

            // Crear un array para almacenar el contenido de los archivos
            $archivosConContenido = [];

            // Obtener el contenido de cada archivo
            foreach ($archivos as $archivo) {
                try {
                    $contenido = Storage::get($archivo);
                    $contenidoUtf8 = mb_convert_encoding($contenido, 'UTF-8', 'UTF-8');
                    $archivosConContenido[] = ['nombre' => $archivo, 'contenido' => $contenidoUtf8];
                } catch (FileNotFoundException $e) {
                    // Manejar la excepción si el archivo no se encuentra
                    Log::error('Archivo no encontrado: ' . $archivo);
                }
            }

            return response()->json(['archivos' => $archivosConContenido]);
        } else {
            return response()->json(['mensaje' => 'La carpeta no existe o no tiene archivos'], 404);
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