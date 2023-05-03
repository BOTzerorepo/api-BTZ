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
        $payTime = new PayTime();
        $payTime->title = $request['title'];
        $payTime->description= $request['description'];
        $payTime->user = $request['user'];
        $payTime->empresa = $request['empresa'];
        $payTime->save();

        return $payTime;
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
        $payTime = PayTime::findOrFail($id);
        $payTime->title = $request['title'];
        $payTime->description= $request['description'];
        $payTime->user = $request['user'];
        $payTime->empresa = $request['empresa'];
        $payTime->save();

        return $payTime;
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
