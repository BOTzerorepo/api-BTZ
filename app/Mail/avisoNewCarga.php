<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class avisoNewCarga extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $datos;

   
    public function __construct($datos)
    {
        $this->datos = $datos;
        $this->subject = 'INSTRUCCIONES INTERNAS // '.$datos['operacion'] . ' - '.$datos['type'] .' - '. $datos['trader'] .' - '.$datos['cantidad'].'X'.$datos['cntr_type'].' // BKG: '. $datos['booking'];
    }

    
    public function build()
    {

        $type = $this->datos['type'];

        if ($type === 'Impo Terrestre') {

            return $this->view('mails.avisoNewCargaImpoTerrestre');

        } elseif ($type === 'Expo Maritima') {

            return $this->view('mails.avisoNewCargaExpoMaritima');

        } elseif ($type === 'Expo Terrestre') {

            return $this->view('mails.avisoNewCargaExpoTerrestre');

        } elseif ($type === 'Impo Maritima') {

            return $this->view('mails.avisoNewCargaImpoMaritima');

        } elseif ($type === 'Nacional') {

            return $this->view('mails.avisoNewCargaNacional');

        }
         else {

            return $this->view('mails.avisoNewCargaFOB');
        }

    }

   
}
