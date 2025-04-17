<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\profit;

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
        //
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
}
