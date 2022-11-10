<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class cargaCargando extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $datos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos)
    {
        $this->datos = $datos;
        $this->subject = '[ AUTOMATICO ] Carga en Zona de Carga. Viaje: '.$datos['cntr'];
    }
    /**
     * Build the message
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.CargaCargando');
    }
}
