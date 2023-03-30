<?php

namespace App\Http\Controllers;

use App\Models\Ata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtaController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $atas = DB::table('atas')->get();       
        return $atas;
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
        $ata = new Ata();
        $ata->razon_social = $request['razon_social'];
        $ata->tax_id= $request['tax_id'];
        $ata->provincia = $request['provincia'];
        $ata->phone = $request['phone'];
        $ata->pais = $request['pais'];
        $ata->mail = $request['mail'];
        $ata->user = $request['user'];
        $ata->empresa = $request['empresa'];
        $ata->save();

        return $ata;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ata  $ata
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ata = DB::table('atas')->where('id','=',$id)->get();
        return $ata;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ata  $ata
     * @return \Illuminate\Http\Response
     */
    public function edit(Ata $ata)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ata  $ata
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ata = Ata::findOrFail($id);
        $ata->razon_social = $request['razon_social'];
        $ata->tax_id= $request['tax_id'];
        $ata->provincia = $request['provincia'];
        $ata->phone = $request['phone'];
        $ata->pais = $request['pais'];
        $ata->mail = $request['mail'];
        $ata->user = $request['user'];
        $ata->empresa = $request['empresa'];
        $ata->save();

        return $ata;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ata  $ata
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Ata::destroy($id);

        $existe = Ata::find($id);
        if($existe){
            return 'No se elimino el Agente de Transporte';
        }else{
            return 'Se elimino el Agente de Transporte';
        };
    }
}
