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
        $customersShipper = DB::table('customer_shippers')->where('company','=',$company)->get();
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
        $customerShipper = new CustomerShipper();
        $customerShipper->razon_social = $request['razon_social'];
        $customerShipper->tax_id = $request['tax_id'];
        $customerShipper->address = $request['address'];
        $customerShipper->city = $request['city'];
        $customerShipper->country = $request['country'];
        $customerShipper->postal_code = $request['postal_code'];
        $customerShipper->create_user = $request['create_user'];
        $customerShipper->company = $request['company'];
        $customerShipper->remarks = $request['remarks'];
        $customerShipper->save();

        return $customerShipper;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerShipper  $customerShipper
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerShipper = DB::table('customer_shippers')->where('id','=',$id)->get();
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
        $customerShipper = CustomerShipper::findOrFail($id);
        $customerShipper->razon_social = $request['razon_social'];
        $customerShipper->tax_id = $request['tax_id'];
        $customerShipper->address = $request['address'];
        $customerShipper->city = $request['city'];
        $customerShipper->country = $request['country'];
        $customerShipper->postal_code = $request['postal_code'];
        $customerShipper->create_user = $request['create_user'];
        $customerShipper->company = $request['company'];
        $customerShipper->remarks = $request['remarks'];
        $customerShipper->save();

        return $customerShipper;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerShipper  $customerShipper
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CustomerShipper::destroy($id);

        $existe = CustomerShipper::find($id);
        if($existe){
            return 'No se elimino el Customer Ntfy';
        }else{
            return 'Se elimino el Customer Ntfy';
        };
    }
}
