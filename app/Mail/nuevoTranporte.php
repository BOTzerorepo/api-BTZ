<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class nuevoTranporte extends Mailable
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
        $this->subject = 'NUEVO TRANSPORTE DATO DE ALTA - ' . $datos['razon_social'];
    }

    public function build()
    {
        return $this->view('mails.nuevoTransporte');
    }
}
