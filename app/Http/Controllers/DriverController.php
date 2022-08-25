<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function showDriver($transporte)

    {
        $idTranport = DB::table('transporte')->where('id','=',$transporte)->get('razon_social');
        return $idTranport;
        $id = $idTranport[0]->razon_social;
        
        /* Hay que recibir el id del Transporte */
        $drivers = DB::table('choferes')->where('transporte','=',$id)->get();
        return $drivers;

    }
}
