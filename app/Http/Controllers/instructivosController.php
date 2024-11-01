<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class instructivosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user)

    {

        $user = DB::table('users')->where('username', '=', $user)->first();
        
        if ($user->permiso == 'Traffic') {

        $instructivos = DB::table('asign')->where('file_instruction', '!=', null)->select('asign.*','cntr.confirmacion')->join('cntr','cntr.cntr_number','asign.cntr_number')->where('asign.company','=',$user->empresa)->orderBy('asign.created_at','desc')->get();
        return $instructivos;

        }else{

        
        $instructivos = DB::table('asign')->where('file_instruction', '!=', null)->select('asign.*', 'cntr.confirmacion')->join('cntr', 'cntr.cntr_number', 'asign.cntr_number')->where('asign.company','=',$user->empresa)->orderBy('asign.created_at','desc')->get();
        return $instructivos;
        
        }

    }
    public function indexTransport($transport)
    {

        $transportIds = explode(',', $transport);
        $razonSocialList = Transport::whereIn('id', $transportIds)->pluck('razon_social');

        $instructivos = DB::table('asign')->where('file_instruction', '!=', null)->select('asign.*', 'cntr.confirmacion')->join('cntr', 'cntr.cntr_number', 'asign.cntr_number')->whereIn('asign.transport', $razonSocialList)->orderBy('asign.created_at', 'desc')->get();
        return $instructivos;
        
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user,$id)
    {

        $user = DB::table('users')->where('username', '=', $user)->first();
        
        if ($user->permiso == 'Traffic') {

            $instructivo = DB::table('asign')->where('id', $id)->update(['file_instruction' => null]);
            
            if ($instructivo == 1 ){

                return 'ok';

            }else{

                return 'no exite el instructivo.';
            }
            
        }

        return 'se requieren pemisos de Trafico';
      
    }
}
