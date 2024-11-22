<?php

namespace App\Http\Controllers;

use App\Mail\contactBotzero;
use App\Mail\formContact;
use App\Models\mailcontact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class contactoMailController extends Controller
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
        $mailContacto = new mailcontact;
        $mailContacto->name = $request['name'];
        $mailContacto->email = $request['email'];
        $mailContacto->phone = $request['phone'];
        $mailContacto->subject = $request['subject'];
        $mailContacto->observation = $request['observation'];
        $mailContacto->save();


       
        $data = [
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' =>  $request['phone'],
            'subject' => $request['subject'],
            'observation' =>$request['observation']
        ];
        Mail::to('copia@botzero.com.ar')->send(new formContact($data));
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
