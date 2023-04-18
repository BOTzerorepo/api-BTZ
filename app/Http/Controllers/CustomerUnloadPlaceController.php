<?php

namespace App\Http\Controllers;

use App\Models\CustomerUnloadPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerUnloadPlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerUnloadPlaces = DB::table('customer_unload_places')->get();       
        return $customerUnloadPlaces;
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
        $customerUnloadPlace = new CustomerUnloadPlace();
        $customerUnloadPlace->description = $request['description'];
        $customerUnloadPlace->address= $request['address'];
        $customerUnloadPlace->city = $request['city'];
        $customerUnloadPlace->country = $request['country'];
        $customerUnloadPlace->km_from_town = $request['km_from_town'];
        $customerUnloadPlace->remarks = $request['remarks'];
        $customerUnloadPlace->latitud = $request['latitud'];
        $customerUnloadPlace->longitud = $request['longitud'];
        $customerUnloadPlace->link_maps = $request['link_maps'];
        $customerUnloadPlace->user = $request['user'];
        $customerUnloadPlace->company = $request['company'];
        $customerUnloadPlace->save();

        return $customerUnloadPlace;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerUnloadPlace  $customerUnloadPlace
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        $customerUnloadPlace = DB::table('customer_unload_places')->where('id','=',$id)->get();       
        return $customerUnloadPlace;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerUnloadPlace  $customerUnloadPlace
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerUnloadPlace $customerUnloadPlace)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerUnloadPlace  $customerUnloadPlace
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $customerUnloadPlace = CustomerUnloadPlace::findOrFail($id);
        $customerUnloadPlace->description = $request['description'];
        $customerUnloadPlace->address= $request['address'];
        $customerUnloadPlace->city = $request['city'];
        $customerUnloadPlace->country = $request['country'];
        $customerUnloadPlace->km_from_town = $request['km_from_town'];
        $customerUnloadPlace->remarks = $request['remarks'];
        $customerUnloadPlace->latitud = $request['latitud'];
        $customerUnloadPlace->longitud = $request['longitud'];
        $customerUnloadPlace->link_maps = $request['link_maps'];
        $customerUnloadPlace->user = $request['user'];
        $customerUnloadPlace->company = $request['company'];
        $customerUnloadPlace->save();

        return $customerUnloadPlace;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerUnloadPlace  $customerUnloadPlace
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        CustomerUnloadPlace::destroy($id);

        $existe = CustomerUnloadPlace::find($id);
        if($existe){
            return 'No se elimino el Lugar de Descarga';
        }else{
            return 'Se elimino el Lugar de Descarga';
        };
    }
}
