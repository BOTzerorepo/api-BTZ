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
        $this->subject = 'ASIGNACION DE UNIDAD // ' . $datos['ref_customer'] . ' - ' . $datos['type'] . ' - ' . $datos['trader'] . ' – 1x ' . $datos['cntr_type'] . ' // BKG:' . $datos['booking'] . ' .';
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
