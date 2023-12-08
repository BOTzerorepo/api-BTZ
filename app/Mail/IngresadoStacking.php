<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IngresadoStacking extends Mailable
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
        $this->subject = 'STATUS // ' . $datos['ref_customer'] . ' - ' . $datos['type'] . ' - ' . $datos['trader'] . ' - 1 * ' . $datos['cntr_type'] . '// BKG: ' . $datos['booking'] . '.';

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.CargaIngresadaStacking');
    }
}
