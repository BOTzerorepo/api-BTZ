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
    public function indexTraffic()
    {
        $trailer = trailer::all();
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
            $validated = $request->validate([
                'type' => 'required|string|max:255',
                'domain' => 'required|string|unique:trucks,domain',
                'year' => 'required|numeric',
                'chasis' => 'nullable|string|max:255',
                'poliza' => 'nullable|string|max:255',
                'vto_poliza' => 'nullable|date',
                'doc_poliza' => 'nullable|string|max:255',
                'user' => 'required|string|max:255',
                'transport_id' => 'nullable|numeric',
                'transporte' => 'nullable|numeric',
                'customer_id' => 'required|numeric',
                'fletero_id' => 'nullable|numeric',
            ]);
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
                'customer_id' => $request['customer_id']
            ]);

            return response()->json([
                'message' => 'Trailer creado correctamente ' . $request['domain'],
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
            $validated = $request->validate([
                'type' => 'required|string|max:255',
                'domain' => "required|string|unique:trucks,domain,$trailer->id",
                'year' => 'required|numeric',
                'chasis' => 'nullable|string|max:255',
                'poliza' => 'nullable|string|max:255',
                'vto_poliza' => 'nullable|date',
                'doc_poliza' => 'nullable|string|max:255',
                'user' => 'required|string|max:255',
                'transport_id' => 'nullable|numeric',
                'transporte' => 'nullable|numeric',
                'customer_id' => 'required|numeric',
                'fletero_id' => 'nullable|numeric',
            ]);
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
            $customerId = User::where('username', '=', $request['user'])->first();
            $trailer->update([
                'type' => $request['type'],
                'domain' => $request['domain'],
                'chasis' => $request['chasis'],
                'poliza' => $request['poliza'],
                'vto_poliza' => $request['vto_poliza'],
                'year' => $request['year'],
                'user_id' => $customerId->id,
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
        try {
            trailer::destroy($trailer->id);
            $existe = trailer::find($trailer->id);
            if ($existe) {
                return response()->json([
                    'message' => 'No se eliminó el Trailer. Inténtalo de nuevo.',
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Trailer eliminado con éxito.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el Trailer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}