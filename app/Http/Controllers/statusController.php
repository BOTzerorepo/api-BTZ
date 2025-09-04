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

    // Traigo la carga por booking (booking es UNIQUE en 'carga')
    $carga = Carga::where('booking', $booking)->firstOrFail(); // si no hay, 404 directo
    $idCarga = $carga->id;

    DB::beginTransaction();
    try {
        //------------VALIDACIÓN--------------------
        $request->validate([
            'user'        => 'required',
            'empresa'     => 'required',
            'booking'     => 'required',
            'statusGral'  => 'required',
            'description' => 'required',
            'cntr'        => 'required',
        ]);

        // Datos del front
        $description = $request['description'];
        $statusGral  = $request['statusGral'];
        $user        = $request['user'];
        $cntr        = $request['cntr'];
        $empresa     = $request['empresa'];

        //------------INSERT STATUS-----------------
        $status = new statu([
            'status'      => $description,
            'main_status' => $statusGral,
            'cntr_number' => $cntr,
            'user_status' => $user,
        ]);
        $status->save();

        //------------ARCHIVO (opcional)------------
        $statusArchivoPath = null;
        if ($request->hasFile('statusArchivo')) {
            $statusArchivo = $request->file('statusArchivo');
            $folder = 'status/' . $idCarga;

            $nombreArchivo = $status->id . '.' . $statusArchivo->getClientOriginalExtension();
            Storage::disk('public')->putFileAs($folder, $statusArchivo, $nombreArchivo);

            $statusArchivoPath = $folder . '/' . $nombreArchivo;
            $status->documento = $idCarga . '/' . $nombreArchivo;
            $status->extension = $statusArchivo->getClientOriginalExtension();
            $status->save();
        }

        // helper para actualizar CNTR por (booking, cntr_number)
        $updateCntrAndCarga = function () use ($booking, $cntr, $description, $statusGral) {
            // ⚠️ clave: desambiguar por booking + cntr_number
            $cntrModel = cntr::where('booking', $booking)
                ->where('cntr_number', $cntr)
                ->orderByDesc('id_cntr') // por si quedó histórico
                ->firstOrFail(); // si no existe, corta con 404 (capturado abajo)

            $cntrModel->main_status = $statusGral;
            $cntrModel->status_cntr = $description;
            $cntrModel->save();

            // actualizar status de la carga si TODOS los cntr del booking quedaron iguales
            $cntrs = cntr::where('booking', $booking)->get();
            if ($cntrs->isNotEmpty()) {
                $primerCntrStatus = $cntrs->first()->main_status;
                $equal = $cntrs->every(function ($c) use ($primerCntrStatus) {
                    return $c->main_status == $primerCntrStatus;
                });
                if ($equal) {
                    Carga::where('booking', $booking)->update(['status' => $primerCntrStatus]);
                }
            }

            return $cntrModel; // lo devolvemos para reutilizar user_to
        };

        //------------ENVÍO MAIL + LÓGICA POR STATUS------
        $emailController = new emailController();

        if ($statusGral === "TERMINADA") {
            $tipo = 'terminada';
            $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath);

            if ($response === 'ok') {
                $cntrModel = $updateCntrAndCarga();

                // Notificación
                $user_to = $cntrModel->user_cntr ?? null;
                DB::table('notification')->insert([
                    'title'          => 'Carga ' . $cntr . ' con Problemas',
                    'description'    => $description,
                    'user_to'        => $user_to,
                    'status'         => 'No Leido',
                    'sta_carga'      => 'CON PROBLEMA',
                    'user_create'    => $user,
                    'company_create' => $empresa,
                    'cntr_number'    => $cntr,
                    'booking'        => $booking,
                ]);

                DB::commit();
                return response()->json([
                    'id'      => $idCarga,
                    'mensaje' => 'Se modificó el status a: ' . $statusGral . ' y se avisó por correo al cliente',
                ], 200);
            } else {
                DB::rollBack();
                return response()->json(['errores' => 'Algo salió mal, por favor intente nuevamente.', 'id' => $idCarga], 500);
            }
        } elseif ($statusGral === "CON PROBLEMA") {
            $tipo = 'problema';
            $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath);

            if ($response === 'ok') {
                $cntrModel = $updateCntrAndCarga();

                // Notificación
                $user_to = $cntrModel->user_cntr ?? null;
                DB::table('notification')->insert([
                    'title'          => 'Carga ' . $cntr . ' con Problemas',
                    'description'    => $description,
                    'user_to'        => $user_to,
                    'status'         => 'No Leido',
                    'sta_carga'      => 'CON PROBLEMA',
                    'user_create'    => $user,
                    'company_create' => $empresa,
                    'cntr_number'    => $cntr,
                    'booking'        => $booking,
                ]);

                DB::commit();
                return response()->json([
                    'id'      => $idCarga,
                    'mensaje' => 'Se modificó el status a: ' . $statusGral . ' y se avisó por correo al cliente',
                ], 200);
            } else {
                DB::rollBack();
                return response()->json(['errores' => 'Algo salió mal, por favor intente nuevamente.', 'id' => $idCarga], 500);
            }
        } elseif ($statusGral === "STACKING") {
            $tipo = 'stacking';
            $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath);

            if ($response === 'ok') {
                $cntrModel = $updateCntrAndCarga();

                // Cambiar estado del chofer: buscamos por (booking, cntr_number)
                $port = Carga::where('booking', $booking)->value('unload_place');
                $chofer = Asign::where('booking', $booking)
                    ->where('cntr_number', $cntr)
                    ->value('driver');
                if ($chofer) {
                    Driver::where('nombre', $chofer)->update([
                        'status_chofer' => 'libre',
                        'place'         => $port
                    ]);
                }

                DB::commit();
                return response()->json([
                    'id'      => $idCarga,
                    'mensaje' => 'Se modificó el status a: ' . $statusGral . ' y se avisó por correo al cliente',
                ], 200);
            } else {
                DB::rollBack();
                return response()->json(['errores' => 'Algo salió mal, por favor intente nuevamente.', 'id' => $idCarga], 500);
            }
        } else {
            // Cualquier otro cambio de estado
            $tipo = 'cambio';
            $response = $emailController->cambiaStatus($cntr, $empresa, $booking, $user, $tipo, $statusArchivoPath);

            if ($response === 'ok') {
                $updateCntrAndCarga();

                DB::commit();
                return response()->json([
                    'id'      => $idCarga,
                    'mensaje' => 'Se modificó el status a: ' . $statusGral . ' y se avisó por correo al cliente',
                ], 200);
            } else {
                DB::rollBack();
                return response()->json(['errores' => 'Algo salió mal, por favor intente nuevamente.', 'id' => $idCarga], 500);
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


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
