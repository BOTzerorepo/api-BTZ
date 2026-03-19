<?php

namespace App\Http\Controllers;

use App\Models\asign;
use App\Models\cntr;
use App\Models\statu;
use App\Models\InterestPoint;
use App\Models\Transport;
use App\Models\truck;
use App\Models\Carga;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\crearpdfController;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\CntrService;

class cntrController extends Controller
{
    public function __construct(private CntrService $cntrService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try {
            $result = $this->cntrService->store($request->all());
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errores' => 'Algo salió mal: ' . $e->getMessage()
            ], 500);
        }
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


    private function deleteDirectory($dir)
    {
        if (File::exists($dir)) {
            File::deleteDirectory($dir);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'cntr_number' => 'required|string|max:255',
            'cntr_seal' => 'nullable|string|max:255',
            'confirmacion' => 'required|boolean',
        ]);

        // Tomo estos valores temprano para no depender luego
        $newCntrNumber = $request->input('cntr_number');
        $newSeal = $request->input('cntr_seal');
        $newConfirm = (bool) $request->input('confirmacion');

        try {

            $cntr = cntr::findOrFail($id);
            $cntrOld = $cntr->cntr_number;

            $asign = DB::table('asign')->where('cntr_number', $newCntrNumber)->first();

            $idCarga = DB::table('carga')->where('booking', $cntr->booking)->value('id');

            $cntr->cntr_number = $newCntrNumber;
            $cntr->cntr_seal = $newSeal;
            $cntr->confirmacion = $newConfirm;
            $cntr->save();

            $changeAsign = asign::where('cntr_number', $cntrOld)->update(['cntr_number' => $newCntrNumber]);
            Log::debug("Asign actualizada de $cntrOld a $newCntrNumber");
            Log::debug("Esto es asign $changeAsign");

            statu::where('cntr_number', $cntrOld)->update(['cntr_number' => $newCntrNumber]);

            //Eliminar el archivo intructivo y generar uno nuevo 
            if ($changeAsign) {
                /* $dirPath = base_path('public/storage/instructivos/' . $asign->booking . '/' . $cntrOld);
                Log::debug("Eliminando directorio de instructivo: $dirPath");

                $this->deleteDirectory($dirPath); */

                DB::table('asign')->where('cntr_number', $request['cntr_number'])->update(['file_instruction' => null]);
                Log::debug("Instructivo eliminado en base de datos para CNTR: " . $request['cntr_number']);
                // Llamar a la función carga() del controlador crearpdfController
                DB::commit();
                try {
                    $crearpdfController = app(crearpdfController::class);
                    $crearpdfController->carga($request['cntr_number']);

                    Log::debug("Instructivo regenerado para CNTR: " . $request['cntr_number']);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error al ejecutar el método carga en crearpdfController: ' . $e->getMessage());
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }
            DB::commit();
            return response()->json([
                'detail' => $cntr,
                'idCarga' => $idCarga
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($cntrId)
    {
        try {
            $result = $this->cntrService->destroy((int) $cntrId);
            return response()->json([
                'message' => 'CNTR eliminado con éxito.',
                'id' => $result['id'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el CNTR.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function issetCntr($cntr)
    {
        $cntrCount = cntr::where('cntr_number', $cntr)->where('main_status', '!=', 'TERMINADA')->get();
        $asignCount = asign::where('cntr_number', $cntr)->get();

        $count = $cntrCount->count() + $asignCount->count();

        // Prepara la respuesta en formato JSON
        $response = [
            'count' => $count,
            'details' => $cntr
        ];

        // Devuelve la respuesta en formato JSON
        return response()->json($response);
    }
    public function issetAsign($dominio)
    {

        $asign = cntr::where('cntr.main_status', '!=', 'TERMINADA')
            ->where('asign.truck', $dominio)
            ->join('asign', 'asign.cntr_number', '=', 'cntr.cntr_number')
            ->join('trucks', 'asign.truck', '=', 'trucks.domain')
            ->get();

        $count = $asign->count();

        // Prepara la respuesta en formato JSON
        $response = [
            'count' => $count,
            'details' => $asign
        ];

        // Devuelve la respuesta en formato JSON
        return response()->json($response);
    }

    public function point(Request $request)
    {
        $cntrId = $request->input('cntr_id');
        $points = $request->input('points'); // Array de puntos de interés

        // Primero, desvincular los puntos de interés actuales para este CNTR
        $cntr = Cntr::find($cntrId);
        $cntr->pointsOfInterest()->detach();

        // Ahora, volver a adjuntarlos con el nuevo orden
        foreach ($points as $order => $pointId) {
            $cntr->pointsOfInterest()->attach($pointId, ['order' => $order + 1]);
        }

        return redirect()->route('your_route_name')->with('success', 'Puntos de interés guardados con éxito');
    }

    public function datosConfirmar($cntrId)
    {
        try {
            // CNTR
            $cntr = cntr::whereNull('deleted_at')->findOrFail($cntrId);

            // Asignación
            $asign = asign::whereNull('deleted_at')
                ->where('cntr_number', $cntr->cntr_number)
                ->firstOrFail();

            // TRANSPORTE (opcional)
            $transport = null;
            $transportAssigned = !empty(trim((string) $asign->transport));

            if ($transportAssigned) {
                // match tolerante por razón social (si usás SoftDeletes, Eloquent ya filtra)
                $transport = Transport::query()
                    ->whereRaw('LOWER(TRIM(razon_social)) = LOWER(TRIM(?))', [trim($asign->transport)])
                    ->first();

                // si guardaste un ID en "transport", soportalo también como fallback
                if (!$transport && is_numeric($asign->transport)) {
                    $transport = Transport::find((int) $asign->transport);
                }
            }

            // TRUCK (opcional)
            $truck = null;
            $truckAssigned = !empty(trim((string) $asign->truck));
            if ($truckAssigned) {
                $truck = truck::where('domain', $asign->truck)->first(); // sin firstOrFail()
            }

            // CARGA (requerida)
            $carga = Carga::whereNull('deleted_at')
                ->where('booking', $cntr->booking)
                ->firstOrFail();

            return response()->json([
                'cntr' => $cntr,
                'asign' => $asign,
                'transport' => $transport,        // null si no hay asignado o no matchea
                'truck' => $truck,            // null si no hay asignado
                'carga' => $carga,
                // flags para el front
                'meta' => [
                    'transport_assigned' => $transportAssigned,
                    'truck_assigned' => $truckAssigned,
                    'transport_found' => (bool) $transport,
                    'truck_found' => (bool) $truck,
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'No se encontraron datos requeridos',
                'detalle' => $e->getMessage(),
            ], 404);
        } catch (\Throwable $e) {
            Log::error('datosConfirmar error', ['e' => $e]);
            return response()->json([
                'error' => 'Error al obtener los datos',
                'msg' => $e->getMessage(),
            ], 500);
        }
    }

    public function datosCntrNumber($cntrNumber)
    {
        try {
            // Obtener el CNTR
            $cntr = cntr::where('cntr_number', $cntrNumber)
                ->whereNull('deleted_at')
                ->firstOrFail();


            // Obtener el asign asociado al CNTR
            $asign = asign::whereNull('deleted_at')
                ->where('cntr_number', $cntr->cntr_number)
                ->firstOrFail();

            // Obtener el transporte asociado a la asignación
            $transport = Transport::whereNull('deleted_at')
                ->where('razon_social', $asign->transport)
                ->firstOrFail();

            $truck = truck::where('domain', $asign->truck)
                ->first();
            // Obtener la carga asociada al CNTR

            $carga = Carga::whereNull('deleted_at')
                ->where('booking', $cntr->booking)
                ->firstOrFail();

            // Preparar la respuesta en formato JSON
            $response = [
                'cntr' => $cntr,
                'asign' => $asign,
                'transport' => $transport,
                'carga' => $carga,
                'truck' => $truck
            ];

            // Devolver la respuesta en formato JSON
            return response()->json($response);
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra
            return response()->json([
                'error' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function statusResumenCompany(Request $request)
    {
        try {
            $estados = [
                'ASIGNADA',
                'NO ASIGNADA',
                'YENDO A CARGAR',
                'CARGANDO',
                'SALIENDO CARGAR',
                'YENDO A DESCARGAR',
                'EN ADUANA',
                'STACKING'
            ];

            $user = $request->input('user');
            $company = null;

            if ($user) {
                $userObj = DB::table('users')
                    ->where('users.username', $user)
                    ->first();
                if ($userObj) {
                    $company = $userObj->empresa;
                }
            } else {
                $company = $request->input('company');
            }

            $counts = [];
            $detalles = [];


            foreach ($estados as $estado) {
                if ($estado === 'NO ASIGNADA') {
                    $counts[$estado] = cntr::withoutTrashed()
                        ->when($user, fn($q) => $q->where('user_cntr', $user))
                        ->whereIn('main_status', ['NO ASIGNADA', 'NO ASIGNED'])
                        ->where('company', $company)
                        ->count();

                    $detalles[$estado] = cntr::with(['carga', 'asign'])
                        ->withoutTrashed()
                        ->when($user, fn($q) => $q->where('user_cntr', $user))
                        ->whereIn('main_status', ['NO ASIGNADA', 'NO ASIGNED'])
                        ->where('company', $company)
                        ->get()
                        ->map(function ($item) {
                            $item->main_status = 'NO ASIGNADA'; // Unifica visualmente
                            return $item;
                        });
                } else {
                    $counts[$estado] = cntr::withoutTrashed()
                        ->when($user, fn($q) => $q->where('user_cntr', $user))
                        ->where('main_status', $estado)
                        ->where('company', $company)
                        ->count();

                    $detalles[$estado] = cntr::with(['carga', 'asign'])
                        ->withoutTrashed()
                        ->when($user, fn($q) => $q->where('user_cntr', $user))
                        ->where('main_status', $estado)
                        ->where('company', $company)
                        ->get();
                }
            }

            // ACTIVOS (todos excepto TERMINADA)
            $counts['ACTIVOS'] = cntr::withoutTrashed()
                ->when($user, fn($q) => $q->where('user_cntr', $user))
                ->where('main_status', '!=', 'TERMINADA')
                ->where('company', $company)
                ->count();

            $detalles['ACTIVOS'] = cntr::with('carga')
                ->withoutTrashed()
                ->when($user, fn($q) => $q->where('user_cntr', $user))
                ->where('main_status', '!=', 'TERMINADA')
                ->where('company', $company)
                ->select('cntr_number', 'cntr_type', 'main_status', 'booking')
                ->get()
                ->map(function ($item) {
                    return [
                        'cntr_number' => $item->cntr_number,
                        'cntr_type' => $item->cntr_type,
                        'shipper' => $item->carga->shipper ?? null,
                        'main_status' => $item->main_status === 'NO ASIGNED' ? 'NO ASIGNADA' : $item->main_status,
                    ];
                });

            return response()->json([
                'counts' => $counts,
                'detalles' => $detalles,
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

    public function storeCalifications(Request $request)
    {
        try {
            $request->validate([
                'calificacion_carga' => 'nullable|numeric',
                'calification_transport' => 'nullable|numeric',
                'feedback_customer' => 'nullable|string',
                'calification_driver' => 'nullable|numeric',
                'booking' => 'nullable|string',
                'cntr_number' => 'nullable|string',
                'user' => 'nullable|string',
            ]);

            // Insertar calificación del chofer
            DB::table('calification_driver')->insert([
                'calification_driver' => $request->calification_driver,
                'cntr_number' => $request->cntr_number,
                'booking' => $request->booking,
                'user' => $request->user,
                'created_at' => now(),
            ]);

            // Insertar calificación del transporte
            DB::table('calification_transport')->insert([
                'calification_transport' => $request->calification_transport,
                'cntr_number' => $request->cntr_number,
                'booking' => $request->booking,
                'user' => $request->user,
                'Created_at' => now(),
            ]);

            // Actualizar calificación de carga en la tabla cntr
            Cntr::where('cntr_number', $request->cntr_number)
                ->update(['calificacion_carga' => $request->calificacion_carga]);

            return response()->json([
                'message' => 'Calificaciones guardadas correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar las calificaciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyPhysical($id, $user)
    {
        $user = User::where('username', '=', $user)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario inválido.'
            ], 401);
        }

        //Permisos: SOLO estos pueden borrar CNTR
        $allowed = ['Traffic', 'Master', 'Customer'];
        if (!in_array($user->permiso, $allowed, true)) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado.'
            ], 403);
        }

        //Traigo el CNTR
        $cntr = DB::table('cntr')
            ->select('cntr.*')
            ->where('cntr.id_cntr', '=', $id)
            ->whereNull('cntr.deleted_at')
            ->first();

        if (!$cntr) {
            return response()->json([
                'success' => false,
                'message' => 'Contenedor no encontrado.'
            ], 404);
        }

        //Validación: no borrar si está confirmado
        if ((int) ($cntr->confirmacion ?? 0) === 1) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar: el contenedor está confirmado.'
            ], 409);
        }

        DB::beginTransaction();
        try {
            // 1) Borrado físico de asignación/es (por cntr_number)
            DB::table('asign')
                ->where('cntr_number', '=', $cntr->cntr_number)
                ->delete();

            // 2) Borrado físico del contenedor
            DB::table('cntr')
                ->where('id_cntr', '=', $id)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contenedor y asignación eliminados correctamente.',
                'data' => [
                    'id_cntr' => (int) $id,
                    'cntr_number' => $cntr->cntr_number,
                    'booking' => $cntr->booking,
                ]
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error eliminando contenedor/asignación.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
