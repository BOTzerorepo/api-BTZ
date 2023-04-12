<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agencies = DB::table('agencies')->get();       
        return $agencies;
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
        $agency = new Agency();
        $agency->description = $request['description'];
        $agency->razon_social = $request['razon_social'];
        $agency->tax_id= $request['tax_id'];
        $agency->puerto = $request['puerto'];
        $agency->contact_phone = $request['contact_phone'];
        $agency->contact_name = $request['contact_name'];
        $agency->contact_mail = $request['contact_mail'];
        $agency->user = $request['user'];
        $agency->empresa = $request['empresa'];
        $agency->observation_gral = $request['observation_gral'];
        $agency->save();

        return $agency;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agency  $agency
     * @return \Illuminate\Http\Response
     */
    public function show(Agency $agency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Agency  $agency
     * @return \Illuminate\Http\Response
     */
    public function edit(Agency $agency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Agency  $agency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);
        $agency->description = $request['description'];
        $agency->razon_social = $request['razon_social'];
        $agency->tax_id= $request['tax_id'];
        $agency->puerto = $request['puerto'];
        $agency->contact_phone = $request['contact_phone'];
        $agency->contact_name = $request['contact_name'];
        $agency->contact_mail = $request['contact_mail'];
        $agency->user = $request['user'];
        $agency->empresa = $request['empresa'];
        $agency->observation_gral = $request['observation_gral'];
        $agency->save();

        return $agency;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Agency  $agency
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        Agency::destroy($id);

        $existe = Agency::find($id);
        if($existe){
            return 'No se elimino la Agencia';
        }else{
            return 'Se elimino la Agencia';
        };
    }
}
