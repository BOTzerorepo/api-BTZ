<?php

namespace App\Http\Controllers;

use App\Models\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            
            $customers = Customer::orderBy('registered_name', 'ASC')->get();
            return response()->json([
                'data' => $customers,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function indexCompany($company)
    {
        try {
            $customers = Customer::where('company', $company)->orderBy('registered_name', 'ASC')->get();
            return response()->json([
                'data' => $customers,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
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
                'registered_name' => 'required|string|max:255',
                'tax_id' => 'required|numeric|digits_between:8,12',
                'contact_name' => 'required|string|max:255',
                'contact_phone' => 'required|numeric|digits_between:7,12',
                'contact_mail' => 'required|email|max:255',
            ]);

            $customer = customer::create($validated);

            return response()->json([
                'message' => 'Trader creada con éxito',
                'data' => $customer
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores si algo falla
            return response()->json([
                'message' => 'No se pudo crear Trader',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    public function showName(Customer $customer)
    {
        $customer = DB::table('customers')->where('registered_name', '=', $customer)->get();
        return $customer->count();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'registered_name' => 'required|string|max:255',
                'tax_id' => 'required|numeric|digits_between:8,12',
                'contact_name' => 'required|string|max:255',
                'contact_phone' => 'required|numeric|digits_between:7,12',
                'contact_mail' => 'required|email|max:255',
            ]);
            $customer = customer::findOrFail($id);
            $customer->update($validated);

            return response()->json([
                'message' => 'Customer actualizado con éxito',
                'data' => $customer
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo actualizar el customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            customer::destroy($id);
            $existe = customer::find($id);
            if ($existe) {
                return response()->json([
                    'message' => 'No se eliminó el Trader. Inténtalo de nuevo.',
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Trader eliminado con éxito.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el Trader.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
