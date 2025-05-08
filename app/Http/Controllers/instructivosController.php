<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\asign;
use App\Models\cntr;
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
        $user = DB::table('users')->where('username', $username)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Usuario no encontrado.'], 404);
        }
        if ($user->permiso !== 'Traffic') {
            return response()->json(['status' => 'error', 'message' => 'Se requieren permisos de Tráfico.'], 403);
        }
        $asign = DB::table('asign')->where('cntr_number', $id)->first();
        if (!$asign) {
            return response()->json(['status' => 'error', 'message' => 'Registro no encontrado.'], 404);
        }
        if (!$asign->file_instruction) {
            return response()->json(['status' => 'error', 'message' => 'El instructivo ya no existe en el registro.'], 404);
        }

        $filePath = base_path('public/instructivos/' . $asign->booking . '/' . $asign->cntr_number . '/' . $asign->file_instruction);

        if (file_exists($filePath)) {
            try {
                unlink($filePath);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Error al eliminar el archivo físico.', 'error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'El archivo no existe en el sistema.'], 404);
        }

        $updated = DB::table('asign')->where('cntr_number', $id)->update(['file_instruction' => null]);

        if ($updated) {
            return response()->json(['status' => 'success', 'message' => 'Instructivo eliminado correctamente.'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Error al actualizar el registro.'], 500);
        }
    }

    public function saveIntruction($cntrNumber, Request $request)
    {
        try {
            $validated = $request->validate([
                'agent_port' => 'nullable|string',
                'sub_empresa' => 'nullable|string',
                'out_usd' => 'nullable|numeric',
                'company_invoice_out' => 'nullable|string',
                'observation_out' => 'nullable|string',

                'mail' => 'nullable|email|max:255',
                'user' => 'nullable|string|max:255',
                'empresa' => 'nullable|string|max:255',
            ]);
            // Actualizamos la asignacion. 
            asign::where('cntr_number', $cntrNumber)
                ->update([
                    'agent_port' => $request->input('agent_port'),
                    'sub_empresa' => $request->input('sub_empresa'),
                ]);

            cntr::where('cntr_number', $cntrNumber)
                ->update([
                    'out_usd' => $request->input('out_usd'),
                    'company_invoice_out' => $request->input('company_invoice_out'),
                    'observation_out' => $request->input('observation_out'),
                ]);

            DB::table('profit')->insert([
                'out_usd' => $request->input('out_usd'),
                'cntr_number' => $request->input('cntr_number'),
                'out_razon_social' => $request->input('razon_social'),
                'user' => $request->input('user'),
                'out_detalle' => 'Flete Terrestre'
            ]);

            return response()->json([
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
}
