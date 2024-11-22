<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class instructivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user)

    {

        $user = DB::table('users')->where('username', '=', $user)->first();

        if ($user->permiso == 'Traffic') {

            $instructivos = DB::table('asign')->where('file_instruction', '!=', null)->select('asign.*', 'cntr.confirmacion')->join('cntr', 'cntr.cntr_number', 'asign.cntr_number')->where('asign.company', '=', $user->empresa)->orderBy('asign.created_at', 'desc')->get();
            return $instructivos;
        } else {


            $instructivos = DB::table('asign')->where('file_instruction', '!=', null)->select('asign.*', 'cntr.confirmacion')->join('cntr', 'cntr.cntr_number', 'asign.cntr_number')->where('asign.company', '=', $user->empresa)->orderBy('asign.created_at', 'desc')->get();
            return $instructivos;
        }
    }
    public function indexTransport($transport)
    {

        $transportIds = explode(',', $transport);
        $razonSocialList = Transport::whereIn('id', $transportIds)->pluck('razon_social');

        $instructivos = DB::table('asign')->where('file_instruction', '!=', null)->select('asign.*', 'cntr.confirmacion')->join('cntr', 'cntr.cntr_number', 'asign.cntr_number')->whereIn('asign.transport', $razonSocialList)->orderBy('asign.created_at', 'desc')->get();
        return $instructivos;
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
    

    public function destroy($username, $id)
    {
        // Buscar al usuario por nombre de usuario
        $user = DB::table('users')->where('username', $username)->first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }
        if ($user->permiso !== 'Traffic') {
            return response()->json(['message' => 'Se requieren permisos de Tráfico.'], 403);
        }

        // Buscar el registro en asign y obtener el nombre del archivo
        $asign = DB::table('asign')->where('id', $id)->first();
        if (!$asign) {
            return response()->json(['message' => 'Registro no encontrado.'], 404);
        }

        if (!$asign->file_instruction) {
            return response()->json(['message' => 'El instructivo ya no existe en el registro.'], 404);
        }

        $booking = $asign->booking; // Asegúrate de que $asign tenga esta propiedad
        $cntr_number = $asign->cntr_number;

        // Construir la ruta completa del archivo
        $filePath = storage_path('instructivos/' . $booking . '/' . $cntr_number . '/' . $asign->file_instruction);

        // Intentar eliminar el archivo físico si existe
        if (file_exists($filePath)) {
            try {
                unlink($filePath);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error al eliminar el archivo físico.', 'error' => $e->getMessage()], 500);
            }
        }

        // Actualizar la base de datos para eliminar la referencia al archivo
        $updated = DB::table('asign')->where('id', $id)->update(['file_instruction' => null]);

        if ($updated) {
            return response()->json(['message' => 'Instructivo eliminado correctamente.']);
        } else {
            return response()->json(['message' => 'Error al actualizar el registro.'], 500);
        }
    }
}
