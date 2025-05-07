<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\profit;
use App\Models\cntr;
use App\Models\Carga;
use Illuminate\Support\Facades\DB;

class ProfitController extends Controller
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
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $profit = profit::find($id);

            if (!$profit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profit no encontrado',
                ], 404);
            }

            $cntrNumber = $profit->cntr_number;

            // Eliminar el profit
            $profit->delete();

            // Recalcular el profit total después de eliminar
            $in_usd = profit::where('cntr_number', $cntrNumber)->sum('in_usd');
            $out_usd = profit::where('cntr_number', $cntrNumber)->sum('out_usd');
            $profit_total = $in_usd - $out_usd;

            $cntr = cntr::where('cntr_number', $cntrNumber)->first();
            if ($cntr) {
                $cntr->update(['profit' => $profit_total]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profit eliminado correctamente',
                'id_cntr' => $cntr->id_cntr,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $profit = profit::find($id);
            $cntr = cntr::where('cntr_number', $$profit->cntr_number)->first();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el profit',
                'id_cntr' => $cntr->id_cntr,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function profitSumaCntr($cntrNumber)
    {
        try {
            // Sumar ingresos
            $in_usd = profit::where('cntr_number', $cntrNumber)->sum('in_usd');

            // Sumar egresos
            $out_usd = profit::where('cntr_number', $cntrNumber)->sum('out_usd');

            // Calcular ganancia
            $profit = $in_usd - $out_usd;

            return response()->json([
                'success' => true,
                'in_usd' => $in_usd,
                'out_usd' => $out_usd,
                'profit' => $profit,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al calcular el profit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function profitCntr($cntrNumber)
    {
        try {
            $in_usd_positivo = profit::where('cntr_number', $cntrNumber)->where('in_usd', '!=', '0')->get();
            $in_usd_nulo = profit::where('cntr_number', $cntrNumber)->where('in_usd', '==', '0')->get();

            return response()->json([
                'success' => true,
                'in_usd_positivo' => $in_usd_positivo,
                'in_usd_nulo' => $in_usd_nulo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al calcular el profit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function agregarInOn($cntrNumber, Request $request)
    {
        $request->validate([
            'in_usd' => 'nullable|numeric',
            'in_razon_social' => 'nullable|string',
            'in_detalle' => 'nullable|string',
            'out_usd' => 'nullable|string',
            'out_razon_social' => 'nullable|string',
            'out_detalle' => 'nullable|string',
            'user' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {

            if ($request->in_usd == null) {
                $in_usd = 0;
            } else {
                $in_usd = $request->in_usd;
            }

            if ($request->out_usd == null) {
                $out_usd = 0;
            } else {
                $out_usd = $request->out_usd;
            }
            // Guardar el ingreso en la tabla profit
            profit::create([
                'cntr_number' => $cntrNumber,
                'in_usd' => $in_usd,
                'in_razon_social' => $request->in_razon_social,
                'in_detalle' => $request->in_detalle,
                'out_usd' => $out_usd,
                'out_razon_social' => $request->out_razon_social,
                'out_detalle' => $request->out_detalle,
                'user' => $request->user,
            ]);

            $in_usd = profit::where('cntr_number', $cntrNumber)->sum('in_usd');
            $out_usd = profit::where('cntr_number', $cntrNumber)->sum('out_usd');

            $profit_total = $in_usd - $out_usd;

            // Actualizar tabla cntr
            $cntr = cntr::where('cntr_number', $cntrNumber)->first();
            if (!$cntr) {
                return response()->json(['message' => 'CNTR no encontrado'], 404);
            }

            $cntr->update(['profit' => $profit_total]);

            DB::commit();

            return response()->json([
                'message' => 'Ingreso agregado correctamente',
                'id_cntr' => $cntr->id_cntr,
                'success' => true
            ]);
        } catch (\Exception $e) {
            $cntr = cntr::where('cntr_number', $cntrNumber)->first();
            return response()->json([
                'success' => false,
                'id_cntr' => $cntr->id_cntr,
                'message' => 'No se pudo agregar ingreso',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function actualizarInOn(Request $request, $id)
    {
        $request->validate([
            'in_usd' => 'nullable|numeric',
            'in_razon_social' => 'nullable|string',
            'in_detalle' => 'nullable|string',
            'out_usd' => 'nullable|numeric',
            'out_razon_social' => 'nullable|string',
            'out_detalle' => 'nullable|string',
            'user' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            $profit = profit::find($id);
            if (!$profit) {
                return response()->json(['message' => 'Profit no encontrado'], 404);
            }

            if ($request->in_usd == null) {
                $in_usd = 0;
            } else {
                $in_usd = $request->in_usd;
            }

            if ($request->out_usd == null) {
                $out_usd = 0;
            } else {
                $out_usd = $request->out_usd;
            }

            // Actualizamos los campos
            $profit->update([
                'in_usd' => $in_usd,
                'in_razon_social' => $request->in_razon_social,
                'in_detalle' => $request->in_detalle,
                'out_usd' => $out_usd,
                'out_razon_social' => $request->out_razon_social,
                'out_detalle' => $request->out_detalle,
                'user' => $request->user,
            ]);

            // Recalcular el profit total
            $cntrNumber = $profit->cntr_number;

            $in_usd = profit::where('cntr_number', $cntrNumber)->sum('in_usd');
            $out_usd = profit::where('cntr_number', $cntrNumber)->sum('out_usd');
            $profit_total = $in_usd - $out_usd;

            $cntr = cntr::where('cntr_number', $cntrNumber)->first();
            if (!$cntr) {
                return response()->json(['message' => 'CNTR no encontrado'], 404);
            }

            $cntr->update(['profit' => $profit_total]);

            DB::commit();

            return response()->json([
                'message' => 'Profit actualizado correctamente',
                'id_cntr' => $cntr->id_cntr,
                'success' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar el profit',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
