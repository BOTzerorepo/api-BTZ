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
use Illuminate\Http\UploadedFile;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Client;

class statusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $statusType = DB::table('status_type')->get();

            return response()->json([
                'success' => true,
                'message' => 'Tipos de estados obtenidos correctamente.',
                'data' => $statusType
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de estados.',
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function indexActiveCompany(Request $request)
    {
        $company = $request->input('company');
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
            ->where('carga.empresa', '=', $company)
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
            ->whereNull('carga.deleted_at')
            ->where('cntr.main_status', '!=', 'TERMINADA')
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
            
            // ACTUALIZA STATUS
            $status = new statu([
                'status' => $description,
                'main_status' => $statusGral,
                'cntr_number' => $cntr,
                'user_status' => $user,
            ]);
                  
            // Guarda el modelo para obtener el ID
            $status->save();
            $savedPath = null;
            $ext = null;

            if ($request->hasFile('statusArchivo') && $request->file('statusArchivo') instanceof UploadedFile) {
                $statusArchivo = $request->file('statusArchivo');
            
                // valida básico (ajustá según tu caso)
                $request->validate([
                    'statusArchivo' => 'file|max:10240', // 10MB
                ]);
            
                $idCarga = (string)$idCarga; // por las dudas
                $folder = "status/{$idCarga}";
            
                // Nombre determinístico por id status + extensión “real”
                $ext = $statusArchivo->extension() ?: $statusArchivo->getClientOriginalExtension() ?: 'bin';
                $nombreArchivo = $status->id . '.' . $ext;
            
                try {
                    // Asegurá que el folder exista en el disco 'public'
                    if (!Storage::disk('public')->exists($folder)) {
                        Storage::disk('public')->makeDirectory($folder);
                    }
            
                    // Guardar usando el helper del UploadedFile
                    $savedPath = $statusArchivo->storeAs($folder, $nombreArchivo, 'public');
            
                    if (!$savedPath) {
                        Log::error('No se pudo almacenar el archivo', [
                            'folder' => $folder,
                            'nombre' => $nombreArchivo,
                        ]);
                        return response('No se pudo guardar el archivo.', 500);
                    }
            
                    // Guardá en DB un path consistente
                    $status->documento = $savedPath;            
                    $status->extension = $ext;
            
                } catch (\Throwable $e) {
                    Log::error('Error subiendo statusArchivo', [
                        'folder' => $folder,
                        'nombre' => $nombreArchivo,
                        'ex' => $e->getMessage(),
                    ]);
                    return response('Error subiendo el archivo.', 500);
                }
            } else {
                $status->documento = null;
                $status->extension = null;
            }
            
            $status->save();
                   
            //------------GENERAL--------------------
            if ($statusGral == "TERMINADA") {

                $tipo = 'terminada';
                $emailController = new emailController();
                $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $savedPath);
                
                if ($response == 'ok') {
                    $cntrModel = cntr::where('cntr_number', $cntr)->firstOrFail();
                    $cntrModel->main_status = $statusGral;
                    $cntrModel->status_cntr = $description;
                    $cntrModel->save();

                    $cntrs = cntr::where('booking', $booking)->get();
                    $primerCntrStatus = $cntrs->first()->main_status;

                    $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                        return $cntr->main_status == $primerCntrStatus;
                    });

                    if ($equal) {
                        Carga::where('booking', $booking)->update(['status' => $primerCntrStatus]);
                    }

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
                    return response()->json([
                        'id' => $idCarga,
                        'errores' => 'Se modificó el satus a: ' . $statusGral . ' y avisado por Correo al Cliente',
                    ], 200);
                } else {
                    DB::rollBack();
                    return response()->json(['errores' => 'Algo salió mal, por favor vuelta a intentar la acción.', 'id' => $idCarga], 500);
                }
                
            } elseif ($statusGral == "CON PROBLEMA") {
                $tipo = 'problema';
            
                $emailController = new emailController();
                $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $savedPath);

                if ($response == 'ok') {
                    $cntrModel = cntr::where('cntr_number', $cntr)->firstOrFail();
                    $cntrModel->main_status = $statusGral;
                    $cntrModel->status_cntr = $description;
                    $cntrModel->save();

                    $cntrs = cntr::where('booking', $booking)->get();
                    $primerCntrStatus = $cntrs->first()->main_status;

                    $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                        return $cntr->main_status == $primerCntrStatus;
                    });

                    if ($equal) {
                        Carga::where('booking', $booking)->update(['status' => $primerCntrStatus]);
                    }

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
                    return response()->json([
                        'id' => $idCarga,
                        'errores' => 'Se modificó el satus a: ' . $statusGral .' y avisado por Correo al Cliente' ,
                    ], 200);

                } else {
                    DB::rollBack();
                    return response()->json(['errores' => 'Algo salió mal, por favor vuelta a intentar la acción.', 'id' => $idCarga], 500);
                }
            } elseif ($statusGral == "STACKING") {
                $tipo = 'stacking';
            
                $emailController = new emailController();
                $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $savedPath);
                
                if ($response == 'ok') {
                    $cntrModel = cntr::where('cntr_number', $cntr)->firstOrFail();
                    $cntrModel->main_status = $statusGral;
                    $cntrModel->status_cntr = $description;
                    $cntrModel->save();

                    $cntrs = cntr::where('booking', $booking)->get();
                    $primerCntrStatus = $cntrs->first()->main_status;

                    $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                        return $cntr->main_status == $primerCntrStatus;
                    });

                    if ($equal) {
                        Carga::where('booking', $booking)->update(['status' => $primerCntrStatus]);
                    }

                    $port = Carga::where('booking', $booking)->value('unload_place');
            
                    $chofer = Asign::where('booking', $booking)
                    ->where('cntr_number', $cntr)
                    ->value('driver');
                    
                    Driver::where('nombre', $chofer)->update(['status_chofer' => 'libre', 'place' => $port]);
                    
                    DB::commit();
                    return response()->json([
                    'id' => $idCarga,
                    'errores' => 'Se modificó el satus a: ' . $statusGral .' y avisado por Correo al Cliente' ,
                    ], 200);
            
                } else {
                    DB::rollBack();
                    return response()->json(['errores' => 'Algo salió mal, por favor vuelta a intentar la acción.', 'id' => $idCarga], 500);
                }
            } else {

                // =========== AQUI ESTA LA MODIFICACIÓN ===========
                if ($statusGral == "YENDO A CARGAR") {
                    
                    $tO = DB::table('cntr')
                        ->select('carga.cma_t_o', 'cntr.cntr_number')
                        ->join('carga', 'cntr.booking', '=', 'carga.booking')
                        ->where('cntr.cntr_number', $cntr)
                        ->first();

                    $valor = json_decode(\json_encode($tO->cma_t_o), true);
                    
                    if (!empty($valor)) {
                        $client = new Client();
                        $headers = [
                            'Content-Type' => 'application/json',
                            'Accept'       => 'application/json'
                        ];

                        // Obtenemos la URL del .env
                        $cmaBaseUrl = rtrim(env('API_CMA_BOTZERO', 'http://172.17.0.1:8084'), '/');
                        
                        // Nos aseguramos de no duplicar el "/api" si alguien lo pone en el .env por accidente
                        if (substr($cmaBaseUrl, -4) === '/api') {
                            $cmaBaseUrl = substr($cmaBaseUrl, 0, -4);
                        }

                        $urlCma = $cmaBaseUrl . '/api/cma/estDepCustLoc/' . $cntr . '/' . $valor;

                        try {
                            // Petición interna con timeout
                            $res = $client->request('GET', $urlCma, [
                                'headers' => $headers,
                                'timeout' => 5 
                            ]);
                            
                            $data = json_decode($res->getBody(), true);
                            Log::info("CMA-API Success (Internal): Datos sincronizados para $cntr en $urlCma");

                        } catch (\Exception $e) {
                            // La red de seguridad: evita el rollback de tu base de datos si la API falla
                            Log::warning("CMA-API falló o ignorado en $urlCma. Detalles: " . $e->getMessage());
                            $data = [];
                        }
                    }
                }    
                // =================================================

                $tipo = 'cambio';
                $emailController = new emailController();
                $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $savedPath );

                if ($response == 'ok') {
                    $cntrModel = cntr::where('cntr_number', $cntr)->orderByDesc('id_cntr')->firstOrFail();
                    $cntrModel->main_status = $statusGral;
                    $cntrModel->status_cntr = $description;
                    $cntrModel->save();
            
                    $cntrs = cntr::where('booking', $booking)->get();
                    $primerCntrStatus = $cntrs->first()->main_status;

                    $equal = $cntrs->every(function ($cntr) use ($primerCntrStatus) {
                        return $cntr->main_status == $primerCntrStatus;
                    });

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
            return response()->json(['errores' => 'No se encontró el registro. Detalles: ' . $e->getMessage(), 'id' => $idCarga], 404);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errores' => 'Error de validación. Detalles: ' . $e->getMessage(), 'id' => $idCarga], 422);
        } catch (Exception $e) {
            DB::rollBack();
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

    public function showLast($id)
    {
        try {
            $statu = statu::find($id);

            return response()->json([
                'success' => true,
                'message' => 'Tipos de estados obtenidos correctamente.',
                'data' => $statu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de estados.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showHistory($cntr)
    {
        try {
            $status = DB::table('status')->where('cntr_number', '=', $cntr)->orderBy('created_at', 'DESC')->get();

            return response()->json([
                'success' => true,
                'message' => 'Tipos de estados obtenidos correctamente.',
                'data' => $status
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de estados.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}