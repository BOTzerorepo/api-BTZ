<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PuntoInteresSalida extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $contenedor;
    public $puntoDeInteres;

    public function __construct($contenedor, $puntoDeInteres)
    {
        $this->contenedor = $contenedor;
        $this->puntoDeInteres = $puntoDeInteres;

        $ref    = $contenedor->ref_customer ?? 'SIN REF';
        $type   = $contenedor->type ?? 'SIN TYPE';
        $trader = $contenedor->trader ?? 'SIN TRADER';
        $ct     = $contenedor->cntr_type ?? 'SIN CNTR';
        $bkg    = $contenedor->booking ?? 'SIN BKG';

        $this->subject = "PI SALIDA // {$ref} - {$type} - {$trader} - 1 * {$ct} // BKG: {$bkg}.";
    }

    public function build()
    {
        return $this->view('mails.PuntoInteresSalida');
    }
}
