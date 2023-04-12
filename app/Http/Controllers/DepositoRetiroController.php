<?php

namespace App\Http\Controllers;

use App\Models\DepositoRetiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositoRetiroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $depositoRetiros = DB::table('deposito_retiros')->get();         
        return $depositoRetiros;
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
        $depositoRetiro = new DepositoRetiro();
        $depositoRetiro->title = $request['title'];
        $depositoRetiro->address = $request['address'];
        $depositoRetiro->country= $request['country'];
        $depositoRetiro->city = $request['city'];
        $depositoRetiro->km_from_town = $request['km_from_town'];
        $depositoRetiro->latitud = $request['latitud'];
        $depositoRetiro->longitud = $request['longitud'];
        $depositoRetiro->link_maps = $request['link_maps'];
        $depositoRetiro->user = $request['user'];
        $depositoRetiro->empresa = $request['empresa'];
        $depositoRetiro->save();

        return $depositoRetiro;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function show(DepositoRetiro $depositoRetiro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function edit(DepositoRetiro $depositoRetiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $depositoRetiro = DepositoRetiro::findOrFail($id);
        $depositoRetiro->title = $request['title'];
        $depositoRetiro->address = $request['address'];
        $depositoRetiro->country= $request['country'];
        $depositoRetiro->city = $request['city'];
        $depositoRetiro->km_from_town = $request['km_from_town'];
        $depositoRetiro->latitud = $request['latitud'];
        $depositoRetiro->longitud = $request['longitud'];
        $depositoRetiro->link_maps = $request['link_maps'];
        $depositoRetiro->user = $request['user'];
        $depositoRetiro->empresa = $request['empresa'];
        $depositoRetiro->save();

        return $depositoRetiro;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        DepositoRetiro::destroy($id);

        $existe = DepositoRetiro::find($id);
        if($existe){
            return 'No se elimino la Agencia';
        }else{
            return 'Se elimino la Agencia';
        };
    }
}
