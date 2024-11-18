<?php

namespace App\Http\Controllers;

use App\Models\CustomerCnee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerCneeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerCnees = DB::table('customer_cnees')->get();
        return $customerCnees;
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

            $customerCnee = CustomerCnee::create($validated);

            return response()->json([
                'message' => 'Consignee creada con éxito',
                'data' => $customerCnee
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores si algo falla
            return response()->json([
                'message' => 'No se pudo crear el Consignee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerCnee  $customerCnee
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerCnee $customerCnee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerCnee  $customerCnee
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerCnee $customerCnee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerCnee  $customerCnee
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
            $customerCnee = CustomerCnee::findOrFail($id);
            $customerCnee->update($validated);

            return response()->json([
                'message' => 'Consignee actualizado con éxito',
                'data' => $customerCnee
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el Consignee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerCnee  $customerCnee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CustomerCnee::destroy($id);

        $existe = CustomerCnee::find($id);
        if ($existe) {
            return 'No se elimino la Customer Cnee';
        } else {
            return 'Se elimino la Customer Cnee';
        };
    }
}
