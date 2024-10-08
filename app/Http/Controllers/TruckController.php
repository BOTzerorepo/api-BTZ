<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoretruckRequest;
use App\Http\Requests\UpdatetruckRequest;
use App\Mail\nuevoTranporte;
use App\Models\asign;
use App\Models\truck;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TruckController extends Controller
{

    protected $serviceSatelital;

    public function __construct(ServiceSatelital $serviceSatelital)
    {
        $this->serviceSatelital = $serviceSatelital;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($customer)
    {
        $truck = truck::where('customer_id','=',$customer)->get();

        return $truck;
    }
    public function indexTransport($transport)
    {
        $trucks = Truck::where('transport_id', '=', $transport)
        ->with(['transport', 'fletero']) 
        ->get();

        $trucksWithNames = $trucks->map(function ($truck) {
            
            return [
                'id' => $truck->id,
                'model' => $truck->model,
                'type' => $truck->type,
                'alta_aker' => $truck->alta_aker,
                'year' => $truck->year,
                'domain' => $truck->domain,
                'model' => $truck->model,
                'chasis' => $truck->chasis,
                'poliza' => $truck->poliza,
                'vto_poliza' => $truck->vto_poliza,
                'type' => $truck->type,
                'transport_id' => $truck->transport_id,
                'user' => $truck->user,
                'fletero_id' => $truck->fletero_id,
                'transport_name' => $truck->transport ? $truck->transport->razon_social : null, // Nombre del transporte asociado
                'fletero_name' => $truck->fletero ? $truck->fletero->razon_social : null, // Nombre del fletero asociado
            ];
        });

        return $trucksWithNames;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoretruckRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTruckRequest $request)
    {
        //  $customerId = DB::table('users')->where('username', $request->user)->value('customer_id');
        $customerId = 2;


        $truck = Truck::create([
            'model' => $request->model,
            'chasis' => $request->chasis,
            'poliza' => $request->poliza,
            'vto_poliza' => $request->vto_poliza,
            'type' => $request->type,
            'domain' => $request->domain,
            'year' => $request->year,
            'device_truck' => $request->device_truck,
            'satelital_location' => $request->satelital_location,
            'transport_id' => $request->transport_id,
            'user' => $request->user,
            'customer_id' => $customerId,
            'fletero_id' => $request->fletero_id 
        ]);

        $resultado = $this->serviceSatelital->issetDominio($request->domain);

        return response()->json([
            'message' => 'Truck created successfully.',
            'data' => $truck,
            'resultado' => $resultado,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function show($truck)
    {
        $trucks = truck::where('transport_id','=',$truck)->get(); 
        return $trucks;
    }

    public function showTransport($truck)
    {
        /* Hay que recibir el id del Transporte */
        $trucks = truck::where('transport_id','=',$truck)->get(); 
        return $trucks;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatetruckRequest  $request
     * @param  \App\Models\truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTruckRequest $request, Truck $truck)
    {
        $od = $truck->domain;

        $asign = DB::table('asign')->where('truck', $od)->first();

        if ($asign) {
            DB::table('asign')
            ->where('truck', $od)
                ->update(['truck' => $request->domain]);
        }

        $customerId = DB::table('users')->where('username', $request->user)->value('customer_id');

        $truck->update([
            'model' => $request->model,
            'type' => $request->type,
            'domain' => $request->domain,
            'chasis' => $request->chasis,
            'poliza' => $request->poliza,
            'vto_poliza' => $request->vto_poliza,
            'year' => $request->year,
            'device_truck' => $request->device_truck,
            'satelital_location' => $request->satelital_location,
            'transport_id' => $request->transport_id,
            'user' => $request->user,
            'customer_id' => $customerId,
            'fletero_id' => $request->fletero_id // Asociar con Fletero
        ]);

        $this->serviceSatelital->issetDominio($request->domain);

        return response()->json([
            'message' => 'Truck updated successfully.',
            'data' => $truck,
        ], 200);
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
    public function issetTruck($domain)
    {

        $truck = DB::table('trucks')
        ->leftJoin('transports','transports.id','=','trucks.transport_id')
        ->select('trucks.id', 'trucks.domain', 'trucks.model','transports.razon_social')
        ->where('trucks.domain', '=', $domain)->get();
        $count = $truck->count();

        return response()->json([
            'count' => $count,
            'detail' => $truck
        ]);
    }
}
