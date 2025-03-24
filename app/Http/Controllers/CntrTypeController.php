<?php

namespace App\Http\Controllers;

use App\Models\CntrType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class CntrTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $cntrTypes = CntrType::all();
            return response()->json([
                'data' => $cntrTypes,
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
        DB::beginTransaction();
        try {
            // Validación de datos
            $request->validate([
                'title' => 'required',
                'teu' => 'required',
                'weight' => 'required',
                'height' => 'required',
                'width' => 'required',
                'longitud' => 'required',
                'observation' => 'required',
                'user' => 'required',
                'company' => 'required',
            ]);
            // Crear un nuevo cntr
            $cntrType = new CntrType();
            $cntrType->title = $request['title'];
            $cntrType->teu= $request['teu'];
            $cntrType->weight = $request['weight'];
            $cntrType->height = $request['height'];
            $cntrType->width = $request['width'];
            $cntrType->longitud = $request['longitud'];
            $cntrType->observation = $request['observation'];
            $cntrType->user = $request['user'];
            $cntrType->company = $request['company'];
            $cntrType->save();

            DB::commit();

            //Devuelvo que se creo correctamente el codigo 
            return response()->json(['message' => 'Tipo Cntr creado exitosamente.', 'message_type' => 'success', 'cntrType'=>$cntrType], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->errors();

            return response()->json(['message' => 'No se pudo crear el tipo Cntr.', 'message_type' => 'danger', 'errores' => $errors], 206);

        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            // Manejar otras excepciones si es necesario
            return response()->json(['message' => $errorMessage , 'message_type' => 'danger',], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CntrType  $cntrType
     * @return \Illuminate\Http\Response
     */
    public function show(CntrType $cntrType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CntrType  $cntrType
     * @return \Illuminate\Http\Response
     */
    public function edit(CntrType $cntrType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CntrType  $cntrType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $cntrType = CntrType::findOrFail($id);
        $cntrType->title = $request['title'];
        $cntrType->teu= $request['teu'];
        $cntrType->weight = $request['weight'];
        $cntrType->height = $request['height'];
        $cntrType->width = $request['width'];
        $cntrType->longitud = $request['longitud'];
        $cntrType->observation = $request['observation'];
        $cntrType->user = $request['user'];
        $cntrType->company = $request['company'];
        $cntrType->save();

        return $cntrType;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CntrType  $cntrType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        CntrType::destroy($id);

        $existe = CntrType::find($id);
        if($existe){
            return 'No se elimino el Tipo de Cntr';
        }else{
            return 'Se elimino el Tipo de Cntr';
        };
    }
}
