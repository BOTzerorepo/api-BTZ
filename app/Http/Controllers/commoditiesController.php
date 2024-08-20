<?php

namespace App\Http\Controllers;

use App\Models\commodity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class commoditiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commodities = commodity::all();
        return $commodities;
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
        $commodity = new commodity();
        $commodity->commodity = $request['commodity'];
        $commodity->user = $request['user'];
        $commodity->company = $request['company'];
        $commodity->save();

        return $commodity;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $commodity = DB::table('commodities')->where('id', $id)->get();

        return $commodity;
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
        $commodity = commodity::findOrFail($id);
        $commodity->commodity = $request['commodity'];
        $commodity->user = $request['user'];
        $commodity->company = $request['company'];
        $commodity->save();

        return $commodity;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        commodity::destroy($id);

        $existe = commodity::find($id);
        if ($existe) {
            return 'No se elimino el Commodity';
        } else {
            return 'Se elimino el Commodity';
        };
    }
}
