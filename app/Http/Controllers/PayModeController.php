<?php

namespace App\Http\Controllers;

use App\Models\PayMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayModeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payModes = DB::table('pay_modes')->get();
        return $payModes;
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

            $payMode = PayMode::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'user' => $validated['user'],
                'empresa' => $validated['empresa'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Modo de pago creado correctamente.',
                'data' => $payMode
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
     * @param  \App\Models\PayMode  $payMode
     * @return \Illuminate\Http\Response
     */
    public function show(PayMode $payMode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PayMode  $payMode
     * @return \Illuminate\Http\Response
     */
    public function edit(PayMode $payMode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PayMode  $payMode
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

            $payMode = PayMode::findOrFail($id);
            $payMode->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'user' => $validated['user'],
                'empresa' => $validated['empresa'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Modo de pago actualizado correctamente.',
                'data' => $payMode
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
     * @param  \App\Models\PayMode  $payMode
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PayMode::destroy($id);

        $existe = PayMode::find($id);
        if ($existe) {
            return 'No se elimino el Modo de pago';
        } else {
            return 'Se elimino el Modo de pago';
        };
    }
}
