<?php

namespace App\Http\Controllers;

use App\Models\DepositoRetiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositoRetiroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $depositoRetiros = DB::table('deposito_retiros')->get();         
        return $depositoRetiros;
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
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function show(DepositoRetiro $depositoRetiro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function edit(DepositoRetiro $depositoRetiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DepositoRetiro $depositoRetiro)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DepositoRetiro  $depositoRetiro
     * @return \Illuminate\Http\Response
     */
    public function destroy(DepositoRetiro $depositoRetiro)
    {
        //
    }
}
