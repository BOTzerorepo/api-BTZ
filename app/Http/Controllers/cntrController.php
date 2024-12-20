<?php

namespace App\Http\Controllers;

use App\Models\asign;
use App\Models\cntr;
use App\Models\statu;
use App\Models\InterestPoint;
use App\Models\Transport;
use App\Models\truck;
use App\Models\Carga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\crearpdfController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class cntrController extends Controller
{
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
        $booking = $request['booking'];
        $qb = DB::table('cntr')->where('booking', '=', $booking)->get();
        $numero = $qb->count() + 1;

        if ($request['cntr_number']) {

            $cntr_number = $request['cntr_number'];
        } else {

            $cntr_number = $booking . $numero;
        }

        $cntr = new cntr();
        $cntr->booking = $booking;
        $cntr->cntr_number = $cntr_number;
        $cntr->cntr_seal = $request['cntr_seal'];
        $cntr->cntr_type = $request['cntr_type'];
        $cntr->retiro_place = $qb[0]->retiro_place;
        $cntr->confirmacion = $request['confirmacion'];
        $cntr->user_cntr = $request['user_cntr'];
        $cntr->company = $request['company'];
        $cntr->save();

        if ($cntr) {

            $asign = new asign();
            $asign->cntr_number = $cntr_number;
            $asign->booking = $booking;
            $asign->save();

            $idCarga = DB::table('carga')->where('booking', '=', $cntr->booking)->select('carga.id')->get();

            if ($asign->id) {
                return response()->json([
                    'detail' => $cntr, // Aquí accedemos directamente al objeto $cntr
                    'idCarga' => $idCarga[0]->id // Aquí accedemos al primer elemento del array $idCarga
                ], 200);
            } else {
                return response()->json(['errores' => 'Algo salió mal, hubo un errro en la asignación', 'id' => $idCarga[0]->id], 500);
            }
        } else {

            return response()->json(['errores' => 'Algo salió mal: el contenedor ya existe o faltó algun dato.', 'cntr_number' => $cntr_number], 500);
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

        DB::beginTransaction();

        try {
            $cntr = cntr::findOrFail($id);
            $cntrOld = $cntr->cntr_number;

            $asign = DB::table('asign')->where('cntr_number', $cntr->cntr_number)->first();
            $idCarga = DB::table('carga')->where('booking', $cntr->booking)->value('id');

            $cntr->cntr_number = $request['cntr_number'];
            $cntr->cntr_seal = $request['cntr_seal'];
            $cntr->confirmacion = $request['confirmacion'];
            $cntr->save();

            asign::where('cntr_number', $cntrOld)->update(['cntr_number' => $request['cntr_number']]);
            statu::where('cntr_number', $cntrOld)->update(['cntr_number' => $request['cntr_number']]);

            //Eliminar el archivo intructivo y generar uno nuevo 
            if ($asign && $asign->file_instruction) {
                $dirPath = base_path('public/instructivos/' . $asign->booking . '/' . $cntrOld);

                $this->deleteDirectory($dirPath);

                DB::table('asign')->where('cntr_number', $cntrOld)->update(['file_instruction' => null]);
                // Llamar a la función carga() del controlador crearpdfController
                DB::commit();
                try {
                    $crearpdfController = app(crearpdfController::class);
                    $crearpdfController->carga($request['cntr_number']);
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
    public function destroy($id)
    {
        //
    }
    public function issetCntr($cntr)
    {
        $cntrCount = cntr::where('cntr_number', $cntr)->get();
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
            // Obtener el CNTR
            $cntr = cntr::whereNull('deleted_at')->findOrFail($cntrId);

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
}
