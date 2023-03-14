<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoretrailerRequest;
use App\Http\Requests\UpdatetrailerRequest;
use App\Models\trailer;
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
        $trailer = DB::table('trailers')->where('customer_id','=',$customer)->get();
        return $trailer;
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
        
        $customerId = DB::table('users')->select('customer_id')->where('id','=',$request['user'])->get(0); 
        $cId =  $customerId[0]->customer_id;
        
        $trailer = new trailer();
        $trailer->type = $request['type'];
        $trailer->chasis = $request['chasis'];
        $trailer->poliza = $request['poliza'];
        $trailer->vto_poliza = $request['vto_poliza'];
        $trailer->domain = $request['domain'];
        $trailer->year = $request['year'];
        $trailer->user_id = $request['user'];
        $trailer->transport_id = $request['transport_id'];
        $trailer->customer_id = $cId;
        $trailer->save();

        return $trailer;

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
       
        $trailers = DB::table('trailers')->where('transport_id','=',$transporte)->get(); 
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
       
        $trailer->type = $request['type'];
        $trailer->domain = $request['domain'];
        $trailer->chasis = $request['chasis'];
        $trailer->poliza = $request['poliza'];
        $trailer->vto_poliza = $request['vto_poliza'];
        $trailer->year = $request['year'];
        $trailer->user_id = $request['user_id'];
        $trailer->transport_id = $request['transport_id'];
        $trailer->save();

        return $trailer;
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
        if($existe){
            return 'No se elimino el Trailer';
        }else{
            return 'Se elimino el Trailer';
        };
    }
}
