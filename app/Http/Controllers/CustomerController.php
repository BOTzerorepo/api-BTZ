<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        $customers = DB::table('customers')->get();         
        return $customers;
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
        $customer = new Customer();
        $customer->registered_name = $request['registered_name'];
        $customer->tax_id = $request['tax_id'];
        $customer->contact_name = $request['contact_name'];
        $customer->contact_phone = $request['contact_phone'];
        $customer->contact_mail = $request['contact_mail'];
        $customer->save();

        return $customer;
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
    public function update(Request $request,  $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->registered_name = $request['registered_name'];
        $customer->tax_id = $request['tax_id'];
        $customer->contact_name = $request['contact_name'];
        $customer->contact_phone = $request['contact_phone'];
        $customer->contact_mail = $request['contact_mail'];
        $customer->save();

        return $customer;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        Customer::destroy($id);

        $existe = Customer::find($id);
        if($existe){
            return 'No se elimino el Trader';
        }else{
            return 'Se elimino el Trader';
        };
    }
}
