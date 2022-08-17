<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storecargo_typeRequest;
use App\Http\Requests\Updatecargo_typeRequest;
use App\Models\cargo_type;

class CargoTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\Storecargo_typeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Storecargo_typeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\cargo_type  $cargo_type
     * @return \Illuminate\Http\Response
     */
    public function show(cargo_type $cargo_type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\cargo_type  $cargo_type
     * @return \Illuminate\Http\Response
     */
    public function edit(cargo_type $cargo_type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Updatecargo_typeRequest  $request
     * @param  \App\Models\cargo_type  $cargo_type
     * @return \Illuminate\Http\Response
     */
    public function update(Updatecargo_typeRequest $request, cargo_type $cargo_type)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\cargo_type  $cargo_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(cargo_type $cargo_type)
    {
        //
    }
}
