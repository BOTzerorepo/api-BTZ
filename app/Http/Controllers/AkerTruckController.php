<?php

namespace App\Http\Controllers;

use App\Models\akerTransport;
use App\Models\akerTruck;
use Illuminate\Http\Request;

class AkerTruckController extends Controller
{
    public function index(){

        $trucks = akerTruck::all();
        return $trucks;
    }

    public function show($id){

        $truck = akerTruck::find($id);
        return $truck;

    }
    public function isset($domain)
    {
        $truck = akerTruck::where('domain',$domain)->join('trucks','akertrucks.domain','=','trucks.domain')->count();
        return $truck;
    }
    
}
