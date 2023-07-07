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
    }

    public function loadTHisWeek($user)

    {

        $user = DB::table('users')->where('username', '=', $user)->first();
        $terminaSemana = Carbon::parse('next Sunday')->endOfDay();
        $empiezaSemana = Carbon::parse('last monday')->startOfDay();

        if ($user->permiso == 'Traffic') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->whereBetween('carga.load_date', [$empiezaSemana, $terminaSemana])
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'DESC')->get();
                
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->whereBetween('carga.load_date', [$empiezaSemana, $terminaSemana])
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'DESC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }
    public function loadLastWeek($user)
    {

        $user = DB::table('users')->where('username', '=', $user)->first();
        $empiezaSemana = Carbon::parse('last monday')->startOfDay();

        if ($user->permiso == 'Traffic') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', "<", $empiezaSemana)
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.status', '!=', 'TERMINADA')
                ->orderBy('carga.load_date', 'DESC')->get();
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', "<", $empiezaSemana)
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'DESC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }
    public function loadNextWeek($user)
    {
        $user = DB::table('users')->where('username', '=', $user)->first();

        $terminaSemana = Carbon::parse('next Sunday')->endOfDay();

        if ($user->permiso == 'Traffic') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', ">", $terminaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'DESC')->get();
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.load_date', ">", $terminaSemana)
                ->where('carga.status', '!=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'DESC')->get();
        }

        return $todasLasCargasDeEstaSemana;
    }

    public function loadFinished($user)
    {
        $user = DB::table('users')->where('username', '=', $user)->first();

      

        if ($user->permiso == 'Traffic') {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.status', '=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->orderBy('carga.load_date', 'DESC')->get();
        } else {

            $todasLasCargasDeEstaSemana = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
                ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
                ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
                ->where('carga.status', '=', 'TERMINADA')
                ->where('carga.empresa', '=', $user->empresa)
                ->where('carga.user', '=', $user->username)
                ->orderBy('carga.load_date', 'DESC')->get();
        }

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
    public function show($id, $user)
    {

        $user = DB::table('users')->where('username', '=', $user)->first();

        if ($user->permiso == 'Traffic') {

            $cargaPorId = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
            ->where('carga.empresa', '=', $user->empresa)
            ->where('carga.id', '=', $id)
            ->orderBy('carga.load_date', 'DESC')->get();

            return $cargaPorId;

        }else{

            $cargaPorId = DB::table('carga')->join('cntr', 'cntr.booking', '=', 'carga.booking')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->select('carga.*', 'cntr.*', 'asign.driver', 'asign.transport')
            ->where('carga.empresa', '=', $user->empresa)
            ->where('carga.user', '=', $user->username)
            ->where('carga.id', '=', $id)
            ->orderBy('carga.load_date', 'DESC')->get();

            return $cargaPorId;
            
        }
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
        $booking = DB::table('carga')->where('booking', '=', $booking)->get();
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
