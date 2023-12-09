<?php

namespace App\Http\Controllers;

use App\Models\statu;
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
        try {
            //------------GENERAL--------------------
            $request->validate([
                'description' => 'required',
                'user' => 'required',
                'company' => 'required',
                'booking' => 'required',
                'statusGral' => 'required',
                'statusArchivo' => 'required|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx',
            ]);

            //Datos que recibe del front
            $cntr = $request['cntr'];
            $description = $request['description'];
            $user = $request['user'];
            $empresa = $request['company'];
            $booking = $request['booking'];
            $statusGral = $request['statusGral'];
            $statusArchivo = $request->file('statusArchivo');
            
            // Realiza la consulta buscando el cntr
            $cntrModel = cntr::where('cntr_number', $cntr)->firstOrFail();
            $id_cntr = $cntrModel->id_cntr;
            
            //------------GENERAL--------------------

            if ($statusGral == "TERMINADA") {

                // ACTUALIZA STATUS
                $status = new Status([
                    'status' => $description,
                    'main_status' => $statusGral,
                    'cntr_number' => $cntr,
                    'user_status' => $user,
                ]);
            
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
            
                 // Buscar el registro en la tabla 'carga' con la booking proporcionada
                $carga = Carga::where('booking', $booking)->first();
                $idCarga = $carga->id;
              
                // Devolver una respuesta JSON con información de éxito
                return response()->json([
                    'id' => $idCarga,
                    'message' => 'Se modificó el satus a: ' . $statusGral,
                ], 200);    
            }
        } catch (ModelNotFoundException $e) {
            // Manejar la excepción específica para ModelNotFoundException
            $errores[] = 'No se encontró el registro. Detalles: ' . $e->getMessage();
            return response()->json(['errores' => $errores, 'id_cntr' => $id_cntr], 404);
        } catch (ValidationException $e) {
            // Manejar la excepción específica para ValidationException
            $errores[] = 'Error de validación. Detalles: ' . $e->getMessage();
            return response()->json(['errores' => $errores, 'id_cntr' => $id_cntr], 422);
        } catch (Exception $e) {
            // Manejar otras excepciones genéricas
            $errores[] = 'Error general. Detalles: ' . $e->getMessage();
            return response()->json(['errores' => $errores, 'id_cntr' => $id_cntr], 500);
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