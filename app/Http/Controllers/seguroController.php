<?php

namespace App\Http\Controllers;

use App\Mail\insuraceRequest;
use App\Models\insurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class seguroController extends Controller
{
  function store(Request $request){

        $insunrence = new insurance();
        $insunrence->customer = $request['customer'];
        $insunrence->beneficiario = $request['beneficiario'];
        $insunrence->tax_id = $request['tax_id'];
        $insunrence->commodity = $request['commodity'];
        $insunrence->suma_asegurada = $request['suma_asegurada'];
        $insunrence->truck_transport = $request['truck_transport'];
        $insunrence->truck_domain = $request['truck_domain'];
        $insunrence->truck_trailer = $request['truck_trailer'];
        $insunrence->truck_device = $request['truck_device'];
        $insunrence->load_place = $request['load_place'];
        $insunrence->download_place = $request['download_place'];
        $insunrence->reference_doc = $request['reference_doc'];
        $insunrence->load_date = $request['load_date'];
        $insunrence->truck_driver = $request['truck_driver'];
        $insunrence->truck_document = $request['truck_document'];
        $insunrence->driver_phone = $request['driver_phone'];
        $insunrence->email = $request['email'];
        $insunrence->observation_customer = $request['observation_customer'];
        $insunrence->save();


        Mail::to('fbar@servergroupsa.com')->send(new insuraceRequest($insunrence));
        Mail::to($insunrence->email)->cc('totaltrade@botzero.ar')->cc('totaltrade@botzero.ar')->send(new insuraceRequest($insunrence));

        return 'ok';

  }
}
