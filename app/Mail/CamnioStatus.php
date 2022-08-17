<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CamnioStatus extends Mailable
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
    public function __construct($datos)
    {
        $this->datos = $datos;

        $this->subject = '[ '.$datos['cntr'].' ]'. 'Cambio su Status a ' . $datos['status'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.CargaCambiaStatus');
    }
}
