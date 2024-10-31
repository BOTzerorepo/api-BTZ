<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoretrailerRequest;
use App\Http\Requests\UpdatetrailerRequest;
use App\Models\trailer;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TrailerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($customer)
    {
        $trailer = trailer::where('customer_id', '=', $customer)->get();
        return $trailer;
    }
    public function indexTransport($transport)
    {
        // Convertir $transport en un array si contiene varios IDs separados por comas
        $idArray = explode(',', $transport);

        // Buscar los trailers cuyos transport_id coincidan con cualquiera de los IDs en el array
        $trailers = Trailer::whereIn('transport_id', $idArray)->get();

        return $trailers;
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
     * @param  \App\Http\Requests\StoretrailerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoretrailerRequest $request)
    {
        try {
            if ($request['transporte'] != null) {
                $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
                $idTranport = $transport->id;
                $transport = $request['transporte'];
            } elseif (isset($request['transporte'])) {
                $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
                $idTranport = $transport->id;
                $transport = $request['transporte'];
            } else {
                $qtr = Transport::where('id', '=', $request['transport_id'])->first();
                $transport = $qtr->razon_social;
                $idTranport = $request['transport_id'];
            }

            $customerId = User::where('username', '=', $request['user'])->first();
        
            $trailer = trailer::create([
                'type' => $request['type'],
                'chasis' => $request['chasis'],
                'poliza' => $request['poliza'],
                'vto_poliza' => $request['vto_poliza'],
                'domain' => $request['domain'],
                'year' => $request['year'],
                'user_id' => $customerId->id,
                'fletero_id' => $request['id_fletero'],
                'transport_id' => $idTranport,
                'customer_id' => $customerId->customer_id
            ]);

            return response()->json([
                'message' => 'Trailer editado correctamente ' . $request['domain'],
                'data' => $trailer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el Trailer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\trailer  $trailer
     * @return \Illuminate\Http\Response
     */
    public function show(trailer $trailer)
    {
        $trailer = trailer::find($trailer);
        return $trailer;
    }

    public function showTrailer($transporte)
    {

        $trailers = trailer::where('transport_id', '=', $transporte)->get();
        return $trailers;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\trailer  $trailer
     * @return \Illuminate\Http\Response
     */
    public function edit(trailer $trailer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatetrailerRequest  $request
     * @param  \App\Models\trailer  $trailer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatetrailerRequest $request, trailer $trailer)
    {
        try {
            // Verificación y asignación de transporte
            if ($request['transporte'] != null) {
                $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
                $idTranport = $transport->id;
                $transport = $request['transporte'];
            } elseif (isset($request['transporte'])) {
                $transport = Transport::where('razon_social', '=', $request['transporte'])->first();
                $idTranport = $transport->id;
                $transport = $request['transporte'];
            } else {
                $qtr = Transport::where('id', '=', $request['transport_id'])->first();
                $transport = $qtr->razon_social;
                $idTranport = $request['transport_id'];
            }
            $trailer->update([
                'type' => $request['type'],
                'domain' => $request['domain'],
                'chasis' => $request['chasis'],
                'poliza' => $request['poliza'],
                'vto_poliza' => $request['vto_poliza'],
                'year' => $request['year'],
                'user_id' => $request['user_id'],
                'transport_id' => $idTranport,
                'fletero_id' => $request['id_fletero'],
            ]);

            return response()->json([
                'message' => 'Trailer editado correctamente ' . $request['domain'],
                'data' => $trailer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el Trailer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\trailer  $trailer
     * @return \Illuminate\Http\Response
     */
    public function destroy(trailer $trailer)
    {
        $id = $trailer->id;
        trailer::destroy($id);

        $existe = trailer::find($id);

        if ($existe) {
            return 'No se elimino el Trailer';
        } else {
            return 'Se elimino el Trailer';
        };
    }
}
