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
        $truck = truck::where('customer_id', '=', $customer)->get();

        return $truck;
    }
    public function indexTotal()
    {
        $trucks = truck::all();
        return $trucks;
    }
    public function indexTransport($transport)
    {
        // Convertir la cadena de transportes a un array
        $transportIds = explode(',', $transport);

        // Obtener todos los trucks asociados a los transportes pasados
        $trucks = Truck::whereIn('transport_id', $transportIds) // Usar whereIn para manejar múltiples transportes
            ->with(['transport', 'fletero'])
            ->get();

        // Mapear los resultados para devolver los datos formateados
        $trucksWithNames = $trucks->map(function ($truck) {
            return [
                'id' => $truck->id,
                'model' => $truck->model,
                'type' => $truck->type,
                'alta_aker' => $truck->alta_aker,
                'year' => $truck->year,
                'domain' => $truck->domain,
                'chasis' => $truck->chasis,
                'poliza' => $truck->poliza,
                'vto_poliza' => $truck->vto_poliza,
                'transport_id' => $truck->transport_id,
                'user' => $truck->user,
                'fletero_id' => $truck->fletero_id,
                'transport_name' => $truck->transport ? $truck->transport->razon_social : null, // Nombre del transporte asociado
                'fletero_name' => $truck->fletero ? $truck->fletero->razon_social : null, // Nombre del fletero asociado
            ];
        });

        // Devolver la lista de trucks con los nombres de transporte y fletero
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
        try {
            $validated = $request->validate([
                'model' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'domain' => 'required|string|unique:trucks,domain',
                'alta_aker' => 'nullable|numeric',
                'satelital' => 'nullable|string|max:255',
                'id_satelital' => 'nullable|numeric',
                'act_owner' => 'nullable|numeric',
                'year' => 'required|numeric',
                'device_truck' => 'required|numeric',
                'satelital_location' => 'required|numeric',
                'user' => 'required|string|max:255',
                'customer_id' => 'required|numeric',
                'fletero_id' => 'nullable|numeric',
                'transport_ids' => 'required|numeric',
                'chasis' => 'nullable|string|max:255',
                'poliza' => 'nullable|string|max:255',
                'vto_poliza' => 'nullable|date',
                'doc_poliza' => 'nullable|string|max:255',
            ]);

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
                'transport_id' => $request->transport_ids,
                'user' => $request->user,
                'customer_id' => $customerId,
                'fletero_id' => $request->fletero_id
            ]);

            // Llamada a servicio satelital (ejemplo)
            $resultado = $this->serviceSatelital->issetDominio($request->domain);

            return response()->json([
                'message' => 'Camion creado exitosamente.',
                'data' => $truck,
                'resultado' => $resultado,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el camión.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\truck  $truck
     * @return \Illuminate\Http\Response
     */
    public function show($truck)
    {
        $trucks = truck::where('transport_id', '=', $truck)->get();
        return $trucks;
    }

    public function showTransport($truck)
    {
        /* Hay que recibir el id del Transporte */
        $trucks = truck::where('transport_id', '=', $truck)->get();
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
        try {
            $validated = $request->validate([
                'model' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'domain' => "required|string|unique:trucks,domain,$truck->id",
                'alta_aker' => 'nullable|numeric',
                'satelital' => 'nullable|string|max:255',
                'id_satelital' => 'nullable|numeric',
                'act_owner' => 'nullable|numeric',
                'year' => 'required|numeric',
                'device_truck' => 'required|numeric',
                'satelital_location' => 'required|numeric',
                'user' => 'required|string|max:255',
                'customer_id' => 'required|numeric',
                'fletero_id' => 'nullable|numeric',
                'transport_ids' => 'required|numeric',
                'chasis' => 'nullable|string|max:255',
                'poliza' => 'nullable|string|max:255',
                'vto_poliza' => 'nullable|date',
                'doc_poliza' => 'nullable|string|max:255',
            ]);
            
            $asign = DB::table('asign')->where('truck', $truck->domain)->first();

            if ($asign) {
                DB::table('asign')
                    ->where('truck', $truck->domain)
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
                'transport_id' => $request->transport_ids,
                'user' => $request->user,
                'customer_id' => $customerId,
                'fletero_id' => $request->fletero_id // Asociar con Fletero
            ]);

            $resultado = $this->serviceSatelital->issetDominio($request->domain);

            return response()->json([
                'message' => 'Camión actualizado exitosamente.',
                'data' => $truck,
                'resultado' => $resultado,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el camión.',
                'error' => $e->getMessage(),
            ], 500);
        }
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

        try {
            truck::destroy($id);
            $existe = truck::find($id);
            if ($existe) {
                return response()->json([
                    'message' => 'No se eliminó el Tractor. Inténtalo de nuevo.',
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Tractor eliminado con éxito.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el Tractor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function issetTruck($domain)
    {

        $truck = DB::table('trucks')
            ->leftJoin('transports', 'transports.id', '=', 'trucks.transport_id')
            ->select('trucks.id', 'trucks.domain', 'trucks.model', 'transports.razon_social')
            ->where('trucks.domain', '=', $domain)->get();
        $count = $truck->count();

        return response()->json([
            'count' => $count,
            'detail' => $truck
        ]);
    }
}
