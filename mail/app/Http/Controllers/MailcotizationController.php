<?php

namespace App\Http\Controllers;

use App\Mail\contactBotzero;
use App\Mail\cotizacionBotzero;
use App\Models\mailcontact;
use App\Models\mailcotization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailcotizationController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mail = new mailcotization;
        $mail->email = $request['email'];
        $mail->phone = $request['phone'];
        $mail->trips = $request['trips'];
        $mail->observation = $request['observation'];
        $mail->type = $request['type'];
        $mail->save();

        Mail::to('pablorio@botzero.tech')->send(new cotizacionBotzero($mail));

        return $mail;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
