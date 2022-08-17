<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoretruckRequest;
use App\Http\Requests\UpdatetruckRequest;
use App\Models\truck;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
    * @OA\Get(
    *     path="/api/users",
    *     summary="Mostrar usuarios",
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los usuarios."
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="Ha ocurrido un error."
    *     )
    * )
    */
class TruckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($customer)
    {
        $truck = DB::table('trucks')->where('customer_id','=',$customer)->get();

        return $truck;
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
     * @param  \App\Http\Requests\StoretruckRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoretruckRequest $request)
    {

        $customerId = DB::table('users')->select('customer_id')->where('username','=',$request['user'])->get(0); 
        $cId =  $customerId[0]->customer_id;

        $truck = new truck();
        $truck->model = $request['model'];
        $truck->type = $request['type'];
        $truck->domain = $request['domain'];
        $truck->year = $request['year'];
        $truck->device_truck = $request['device_truck'];
        $truck->satelital_location = $request['satelital_location'];
        $truck->transport_id = $request['transport_id'];
        $truck->user = $request['user'];
        $truck->customer_id = $cId;
        $truck->save();

        return $truck;
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function show(truck $truck)
    {
        $truck = truck::find($truck);
        return $truck;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function edit(truck $truck)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatetruckRequest  $request
     * @param  \App\Models\truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatetruckRequest $request, truck $truck)
    {



        $customerId = DB::table('users')->select('customer_id')->where('username','=',$request['user'])->get(0); 
        $cId =  $customerId[0]->customer_id;

        $truck->model = $request['model'];
        $truck->type = $request['type'];
        $truck->domain = $request['domain'];
        $truck->year = $request['year'];
        $truck->device_truck = $request['device_truck'];
        $truck->satelital_location = $request['satelital_location'];
        $truck->transport_id = $request['transport_id'];
        $truck->user = $request['user'];
        $truck->customer_id = $cId;
        $truck->save();

        return $truck;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function destroy(truck $truck)
    {
        $id = $truck->id;
        truck::destroy($id);

        $existe = truck::find($id);
        
        if($existe){

            return 'No se elimino el Tractor';

        }else{

            return 'Se elimino el Tractor';

        };
    }
}
