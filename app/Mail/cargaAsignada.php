<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class cargaAsignada extends Mailable
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
        $this->subject = 'ENVIO DE DATOS // ' . $datos['ref_customer'] . ' - ' . $datos['type'] . ' - ' . $datos['trader'] . ' – 1 x '  . $datos['cntr_type'] . ' // BKG: '  . $datos['cntr_type'] . ' .';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.CargaAsignada');
    }
}
