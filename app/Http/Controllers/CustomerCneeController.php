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
        $customerCnee = new CustomerCnee();
        $customerCnee->razon_social = $request['razon_social'];
        $customerCnee->tax_id = $request['tax_id'];
        $customerCnee->address = $request['address'];
        $customerCnee->city = $request['city'];
        $customerCnee->country = $request['country'];
        $customerCnee->postal_code = $request['postal_code'];
        $customerCnee->create_user = $request['create_user'];
        $customerCnee->company = $request['company'];
        $customerCnee->remarks = $request['remarks'];
        $customerCnee->save();

        return $customerCnee;
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
        $customerCnee = CustomerCnee::findOrFail($id);
        $customerCnee->razon_social = $request['razon_social'];
        $customerCnee->tax_id = $request['tax_id'];
        $customerCnee->address = $request['address'];
        $customerCnee->city = $request['city'];
        $customerCnee->country = $request['country'];
        $customerCnee->postal_code = $request['postal_code'];
        $customerCnee->create_user = $request['create_user'];
        $customerCnee->company = $request['company'];
        $customerCnee->remarks = $request['remarks'];
        $customerCnee->save();

        return $customerCnee;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerCnee  $customerCnee
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        CustomerCnee::destroy($id);

        $existe = CustomerCnee::find($id);
        if($existe){
            return 'No se elimino la Customer Cnee';
        }else{
            return 'Se elimino la Customer Cnee';
        };
    }
}
