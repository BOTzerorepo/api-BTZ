<?php

namespace App\Http\Controllers;

use App\Models\CustomerShipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerShipperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customersShipper = DB::table('customer_shippers')->get();
        return $customersShipper;
    }

    public function indexCompany($company)
    {
        $customersShipper = DB::table('customer_shippers')->where('company', '=', $company)->get();
        return $customersShipper;
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
        try {
            $validated = $request->validate([
                'razon_social' => 'required|string|max:255',
                'tax_id' => 'required|numeric|digits_between:8,12',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'postal_code' => 'nullable|numeric|digits_between:2,6',
                'create_user' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:255',
            ]);

            $customerShipper = CustomerShipper::create($validated);

            return response()->json([
                'message' => 'Shipper creada con éxito',
                'data' => $customerShipper
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores si algo falla
            return response()->json([
                'message' => 'No se pudo crear Shipper',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerShipper  $customerShipper
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerShipper = DB::table('customer_shippers')->where('id', '=', $id)->get();
        return $customerShipper;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerShipper  $customerShipper
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerShipper $customerShipper)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerShipper  $customerShipper
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'razon_social' => 'required|string|max:255',
                'tax_id' => 'required|numeric|digits_between:8,12',
                'address' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'postal_code' => 'nullable|numeric|digits_between:2,6',
                'create_user' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:255',
            ]);
            $customerShipper = CustomerShipper::findOrFail($id);
            $customerShipper->update($validated);

            return response()->json([
                'message' => 'Shipper actualizado con éxito',
                'data' => $customerShipper
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el Shipper',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerShipper  $customerShipper
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            CustomerShipper::destroy($id);
            $existe = CustomerShipper::find($id);
            if ($existe) {
                return response()->json([
                    'message' => 'No se eliminó el Shipper. Inténtalo de nuevo.',
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Shipper eliminado con éxito.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el Shipper.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
