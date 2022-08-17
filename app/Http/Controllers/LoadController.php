<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreloadRequest;
use App\Http\Requests\UpdateloadRequest;
use App\Models\load;


class LoadController extends Controller
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
     * @param  \App\Http\Requests\StoreloadRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreloadRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\load  $load
     * @return \Illuminate\Http\Response
     */
    public function show(load $load)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\load  $load
     * @return \Illuminate\Http\Response
     */
    public function edit(load $load)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateloadRequest  $request
     * @param  \App\Models\load  $load
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateloadRequest $request, load $load)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\load  $load
     * @return \Illuminate\Http\Response
     */
    public function destroy(load $load)
    {
        //
    }
}
