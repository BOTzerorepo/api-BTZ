<?php

namespace App\Http\Controllers;

use App\Models\CustomerAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerAgents = DB::table('customer_agents')->get();         
        return $customerAgents;
    }

    public function indexCompany($empresa)
    {
        $customerAgent = DB::table('customer_agents')->where('empresa','=',$empresa)->get();
        return $customerAgent;
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
        $customerAgent = new CustomerAgent();
        $customerAgent->razon_social = $request['razon_social'];
        $customerAgent->tax_id = $request['tax_id'];
        $customerAgent->pais = $request['pais'];
        $customerAgent->provincia = $request['provincia'];
        $customerAgent->mail = $request['mail'];
        $customerAgent->phone = $request['phone'];
        $customerAgent->user = $request['user'];
        $customerAgent->empresa = $request['empresa'];
        $customerAgent->save();

        return $customerAgent;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerAgent  $customerAgent
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        $customerAgent = DB::table('customer_agents')->where('id','=',$id)->get();
        return $customerAgent;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerAgent  $customerAgent
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerAgent $customerAgent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerAgent  $customerAgent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $customerAgent = CustomerAgent::findOrFail($id);
        $customerAgent->razon_social = $request['razon_social'];
        $customerAgent->tax_id = $request['tax_id'];
        $customerAgent->pais = $request['pais'];
        $customerAgent->provincia = $request['provincia'];
        $customerAgent->mail = $request['mail'];
        $customerAgent->phone = $request['phone'];
        $customerAgent->user = $request['user'];
        $customerAgent->empresa = $request['empresa'];
        $customerAgent->save();

        return $customerAgent;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerAgent  $customerAgent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CustomerAgent::destroy($id);

        $existe = CustomerAgent::find($id);
        if($existe){
            return 'No se elimino el Customer Agent';
        }else{
            return 'Se elimino el Customer Agent';
        };
    }
}
