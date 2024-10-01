<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoredocumetRequest;
use App\Http\Requests\UpdatedocumetRequest;
use App\Models\documet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($booking,$user)
    {
        $permiso = DB::table('users')->select('permiso')->where('username','=',$user)->get();

        if( $permiso[0]->permiso == 'ata'){

            $documet = documet::where(['booking' => $booking, 'eliminado' => 0, 'user'=> $user])->get();
            return $documet->toJson();

        }else{

            $documet = documet::where(['booking' => $booking, 'eliminado' => 0, 'cntr' => null])->get();
            return $documet->toJson();
        }
        
    }
    public function indexCntr($booking,$user,$cntr)
    {

        $permiso = DB::table('users')->select('permiso')->where('username','=',$user)->get();

        if( $permiso[0]->permiso == 'ata'){

            $documet = documet::where(['booking' => $booking, 'eliminado' => 0, 'user'=> $user,'cntr' => $cntr])->get();
            return $documet->toJson();

        }else{

            $documet = documet::where(['booking' => $booking, 'eliminado' => 0,'cntr' => $cntr])->get();
            return $documet->toJson();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeAta(StoredocumetRequest $request,$booking)
    {
        $nameArchivo = $request->file('file')->getClientOriginalName();
        $folder ='documents/'. $booking;
        if(!file_exists($folder)){
            mkdir('documents/'. $booking, 0777, true);
            $path = $request->file('file')->storeAs('public/'.$folder,$nameArchivo);
            $extension = $request->file('file')->getClientOriginalExtension();
        }else{
            $path = $request->file('file')->storeAs('public/'.$folder,$nameArchivo);
            $extension = $request->file('file')->getClientOriginalExtension();
        }
        
        $user= $request['user'];
        $documet = new documet();
        $documet->name = $nameArchivo;
        $documet->doc = $nameArchivo;
        $documet->booking = $booking;
        $documet->extension = $extension;
        $documet->user = $user;
        $documet->save();
        
        return response()->json(['success'=>$nameArchivo]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoredocumetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoredocumetRequest $request, $booking)
    {
       // http://rail.com.ar/storage/instructivos/{booking}]/{cntr}/{nameArchivo}
       $user= $request['user'];
       $nameArchivo = $request->file('file')->getClientOriginalName();
       if($request['cntr'] != '0' ) {
           $folder ='documents/'. $booking. '/' . $request['cntr'];
           if(!file_exists($folder)){
               mkdir('documents/'. $booking. '/' . $request['cntr'], 0777, true);
               $path = $request->file('file')->storeAs('public/'.$folder,$nameArchivo);
               $extension = $request->file('file')->getClientOriginalExtension();
           }else{
               $path = $request->file('file')->storeAs('public/'.$folder,$nameArchivo);
               $extension = $request->file('file')->getClientOriginalExtension();
           }

           $documet = new documet();
           $documet->name = $nameArchivo;
           $documet->doc = $nameArchivo;
           $documet->booking = $booking;
           $documet->cntr = $request['cntr'];
           $documet->user = $user;
           $documet->extension = $extension;
           $documet->save();

           return response()->json(['success'=>$nameArchivo]);
       } else {
           $folder ='documents/'. $booking;
           if(!file_exists($folder)){
               mkdir('documents/'. $booking, 0777, true);
               $path = $request->file('file')->storeAs('public/'.$folder,$nameArchivo);
               $extension = $request->file('file')->getClientOriginalExtension();
           }else{
               $path = $request->file('file')->storeAs('public/'.$folder,$nameArchivo);
               $extension = $request->file('file')->getClientOriginalExtension();
           }
           
           $documet = new documet();
           $documet->name = $nameArchivo;
           $documet->doc = $nameArchivo;
           $documet->booking = $booking;
           $documet->extension = $extension;
           $documet->user = $user;
           $documet->save();
           
           return response()->json(['success'=>$nameArchivo]);

       }
    }

    public function destroy(StoredocumetRequest $request)
    {
        $link = $request['link'];
        $id = $request['id'];

        $doc = documet::find($id);
        $doc->eliminado = 1;
        $doc->save();

        return Redirect::to($link); 
    }
}
