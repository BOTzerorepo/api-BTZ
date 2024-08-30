<?php

namespace App\Http\Controllers;

use App\Models\Aduana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AduanasController extends Controller
{
    public function index(){

        $aduanas = Aduana::all();
        return $aduanas;
    }
    public function show($id)
    {
        $aduana = DB::table('aduanas')->where('id',$id)->get();
    
        return $aduana;
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'address' => 'required|string',
            'provincia' => 'required|string',
            'pais' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'user' => 'required|string',
            'company' => 'required|string',
        ]);

        $aduana = new Aduana($validated);
        $aduana->link_maps = 'https://www.google.es/maps?q=' . $request->lat . ',' . $request->lon;
        $aduana->save();

        return $aduana;
    }
    public function update(Request $request,  $id)
    {
        $aduana = Aduana::findOrFail($id);
        $aduana->description = $request['description'];
        $aduana->address = $request['address'];
        $aduana->provincia = $request['provincia'];
        $aduana->pais = $request['pais'];
        $aduana->lat = $request['lat'];
        $aduana->lon = $request['lon'];
        $aduana->link_maps = 'https://www.google.es/maps?q=' . $request['lat'] . ',' . $request['lon'];
        $aduana->user = $request['user'];
        $aduana->company = $request['company'];
        $aduana->save();

        return $aduana;
    }
    public function destroy($id)
    {
        Aduana::destroy($id);

        $existe = Aduana::find($id);
        if ($existe) {
            return 'No se elimino el Lugar de Carga';
        } else {
            return 'Se elimino el Lugar de Carga';
        };
    }
}
