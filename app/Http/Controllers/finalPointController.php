<?php

namespace App\Http\Controllers;

use App\Models\port;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class finalPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $final_points = DB::table('ports')->get();
        return $final_points;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $final_points = new port();
        $final_points->description = $request['description'];
        $final_points->pais = $request['pais'];
        $final_points->provincia = $request['provincia'];
        $final_points->save();

        return $final_points;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $final_points = port::find($id);
        return $final_points;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $final_points = port::findOrFail($id);
        $final_points->description = $request['description'];
        $final_points->pais = $request['pais'];
        $final_points->provincia = $request['provincia'];
        $final_points->sigla = $request['sigla'];
        $final_points->save();

        return $final_points;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        port::destroy($id);

        $existe = port::find($id);
        if($existe){
            return 'No se elimino el Lugar de Descarga';
        }else{
            return 'Se elimino el Lugar de Descarga';
        };
    }
}
