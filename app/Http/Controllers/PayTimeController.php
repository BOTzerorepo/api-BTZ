<?php

namespace App\Http\Controllers;

use App\Models\PayTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payTimes = DB::table('pay_times')->get();       
        return $payTimes;
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
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'user' => 'required|string',
                'empresa' => 'required|string',
            ]);

            $payTime = PayTime::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'user' => $validated['user'],
                'empresa' => $validated['empresa'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plazo de pago creado correctamente.',
                'data' => $payTime
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el modo de pago.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PayTime  $payTime
     * @return \Illuminate\Http\Response
     */
    public function show(PayTime $payTime)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PayTime  $payTime
     * @return \Illuminate\Http\Response
     */
    public function edit(PayTime $payTime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PayTime  $payTime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'user' => 'required|string',
                'empresa' => 'required|string',
            ]);

            $payTime = PayTime::findOrFail($id);
            $payTime->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'user' => $validated['user'],
                'empresa' => $validated['empresa'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plazo de pago actualizado correctamente.',
                'data' => $payTime
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el modo de pago.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PayTime  $payTime
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PayTime::destroy($id);

        $existe = PayTime::find($id);
        if($existe){
            return 'No se elimino el Plazo de pago';
        }else{
            return 'Se elimino el Plazo de pago';
        };
    }
}
