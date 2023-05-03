<?php

namespace App\Http\Controllers;

use App\Models\CntrType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CntrTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cntrTypes = DB::table('cntr_types')->get();       
        return $cntrTypes;
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
        $cntrType = new CntrType();
        $cntrType->title = $request['title'];
        $cntrType->teu= $request['teu'];
        $cntrType->weight = $request['weight'];
        $cntrType->height = $request['height'];
        $cntrType->width = $request['width'];
        $cntrType->longitud = $request['longitud'];
        $cntrType->observation = $request['observation'];
        $cntrType->user = $request['user'];
        $cntrType->company = $request['company'];
        $cntrType->save();

        return $cntrType;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CntrType  $cntrType
     * @return \Illuminate\Http\Response
     */
    public function show(CntrType $cntrType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CntrType  $cntrType
     * @return \Illuminate\Http\Response
     */
    public function edit(CntrType $cntrType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CntrType  $cntrType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {
        $cntrType = CntrType::findOrFail($id);
        $cntrType->title = $request['title'];
        $cntrType->teu= $request['teu'];
        $cntrType->weight = $request['weight'];
        $cntrType->height = $request['height'];
        $cntrType->width = $request['width'];
        $cntrType->longitud = $request['longitud'];
        $cntrType->observation = $request['observation'];
        $cntrType->user = $request['user'];
        $cntrType->company = $request['company'];
        $cntrType->save();

        return $cntrType;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CntrType  $cntrType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        CntrType::destroy($id);

        $existe = CntrType::find($id);
        if($existe){
            return 'No se elimino el Tipo de Cntr';
        }else{
            return 'Se elimino el Tipo de Cntr';
        };
    }
}
