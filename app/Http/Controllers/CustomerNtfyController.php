<?php

namespace App\Http\Controllers;

use App\Models\CustomerNtfy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerNtfyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customersNtfy = DB::table('customer_ntfies')->get();         
        return $customersNtfy;
    }

    public function indexCompany($company)
    {
        $customersNtfy = DB::table('customer_ntfies')->where('company','=',$company)->get();
        return $customersNtfy;
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
        $customerNtfy = new CustomerNtfy();
        $customerNtfy->razon_social = $request['razon_social'];
        $customerNtfy->tax_id = $request['tax_id'];
        $customerNtfy->address = $request['address'];
        $customerNtfy->city = $request['city'];
        $customerNtfy->country = $request['country'];
        $customerNtfy->postal_code = $request['postal_code'];
        $customerNtfy->create_user = $request['create_user'];
        $customerNtfy->company = $request['company'];
        $customerNtfy->remarks = $request['remarks'];
        $customerNtfy->save();

        return $customerNtfy;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerNtfy  $customerNtfy
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerNtfy = DB::table('customer_ntfys')->where('id','=',$id)->get();
        return $customerNtfy;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerNtfy  $customerNtfy
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerNtfy $customerNtfy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerNtfy  $customerNtfy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customerNtfy = CustomerNtfy::findOrFail($id);
        $customerNtfy->razon_social = $request['razon_social'];
        $customerNtfy->tax_id = $request['tax_id'];
        $customerNtfy->address = $request['address'];
        $customerNtfy->city = $request['city'];
        $customerNtfy->country = $request['country'];
        $customerNtfy->postal_code = $request['postal_code'];
        $customerNtfy->create_user = $request['create_user'];
        $customerNtfy->company = $request['company'];
        $customerNtfy->remarks = $request['remarks'];
        $customerNtfy->save();

        return $customerNtfy;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerNtfy  $customerNtfy
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CustomerNtfy::destroy($id);

        $existe = CustomerNtfy::find($id);
        if($existe){
            return 'No se elimino el Customer Ntfy';
        }else{
            return 'Se elimino el Customer Ntfy';
        };
    }
}
