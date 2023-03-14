<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class transporteAsignado extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $datos;
    public $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos, $date)
    {
        $this->datos = $datos;
        $this->date  = $date;
        $this->subject = '[ '.$datos['cntr_number'].' ]'. 'Asignacion de Transporte.';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.transporteAsignado');
    }
}
