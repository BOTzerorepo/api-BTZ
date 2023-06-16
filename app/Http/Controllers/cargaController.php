<?php

namespace App\Http\Controllers;

use App\Models\asign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class cargaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = " WHERE WEEKOFYEAR(`carga`.`load_date`)=WEEKOFYEAR(NOW()) AND carga.status != 'TERMINADA' ORDER BY `carga`.`load_date` DESC";
        
    }

    public function loadTHisWeek()
    {
        
        $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr','cntr.booking', '=' ,'carga.booking')
        ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
        ->select('carga.*', 'cntr.*' , 'asign.driver', 'asign.transport')
        ->whereBetween('carga.load_date',[Carbon::parse('last monday')->startOfDay(),Carbon::parse('next Sunday')->endOfDay()])
        ->where('carga.status', '!=', 'TERMINADA')
        ->orderBy('carga.load_date', 'DESC')->get();
        return $todasLasCargasDeEstaSemana;
    
    }
    public function loadLastWeek()
    {
        
        $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr','cntr.booking', '=' ,'carga.booking')
        ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
        ->select('carga.*', 'cntr.*' , 'asign.driver', 'asign.transport')
        ->whereBetween('carga.load_date',[Carbon::parse('next monday')->startOfDay(),Carbon::parse('next Sunday')->endOfDay()])
        ->where('carga.status', '!=', 'TERMINADA')
        ->orderBy('carga.load_date', 'DESC')->get();
        return $todasLasCargasDeEstaSemana;
    
    }
    public function loadNextWeek()
    {
        
        $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr','cntr.booking', '=' ,'carga.booking')
        ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
        ->select('carga.*', 'cntr.*' , 'asign.driver', 'asign.transport')
        ->whereBetween('carga.load_date',[Carbon::parse('last monday')->startOfDay(),Carbon::parse('next Sunday')->endOfDay()])
        ->where('carga.status', '!=', 'TERMINADA')
        ->orderBy('carga.load_date', 'DESC')->get();
        return $todasLasCargasDeEstaSemana;
    
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
    public function issetBooking($booking)
    {
        $booking = DB::table('carga')->where('booking','=',$booking)->get();
        return $booking->count();
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
    public function destroy($id)
    {
        //
    }
}
