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
        $this->subject = 'INSTRUCCIONES INTERNAS // '.$datos['type'] . ' - '.$datos['operacion'] .' - '. $datos['trader'] .' - '.$datos['cantidad'].'X'.$datos['cntr_type'].' // BKG: '. $datos['booking'];
    }

    
    public function build()
    {

        $type = $this->datos['type'];

        if ($type === 'Impo Terrestre') {
            return $this->view('mails.avisoNewCargaImpoTerrestre');
        } /* elseif ($type === 'tipo2') {
            return $this->view('mails.avisoNewCarga2');
        } */ else {
            return $this->view('mails.avisoNewCarga');
        }
        return $this->view('mails.avisoNewCarga');
    }

   
}
