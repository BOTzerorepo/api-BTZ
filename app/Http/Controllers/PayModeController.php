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
        $payMode = new PayMode();
        $payMode->title = $request['title'];
        $payMode->description= $request['description'];
        $payMode->user = $request['user'];
        $payMode->empresa = $request['empresa'];
        $payMode->save();

        return $payMode;
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
        $payMode = PayMode::findOrFail($id);
        $payMode->title = $request['title'];
        $payMode->description= $request['description'];
        $payMode->user = $request['user'];
        $payMode->empresa = $request['empresa'];
        $payMode->save();

        return $payMode;
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
        if($existe){
            return 'No se elimino el Modo de pago';
        }else{
            return 'Se elimino el Modo de pago';
        };
    }
}
