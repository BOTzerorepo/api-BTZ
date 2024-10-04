<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PuntoInteresSalida extends Mailable
{
    use Queueable, SerializesModels;
    public $contenedor;
    public $puntoDeInteres;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contenedor, $puntoDeInteres )
    {
        $this->contenedor = $contenedor;
        $this->puntoDeInteres = $puntoDeInteres;
    }

    public function build()
    {
        return $this->view('mails.PuntoInteresSalida');
    }

    
}
