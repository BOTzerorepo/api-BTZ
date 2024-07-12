<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class puntoDeInteres extends Mailable
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
        $datosnum = json_decode(json_encode($datos), true); // Convertimos $datos a array y lo guardamos en $datosnum

        if ($datosnum['confirmacion'] != 0) { // Accedemos a $datosnum como si fuera un array
            $subject = 'Carga a ' . $datosnum['rango'] . ' metros del Punto de Interés: ' . $datosnum['descripcion'] . ' | ' . $datosnum['ref_customer'] . ' - ' . $datosnum['booking'] . ' - ' . $datosnum['cntr'];
        } else {
            $subject = 'Carga a ' . $datosnum['rango'] . ' metros del Punto de Interés: ' . $datosnum['descripcion'] . ' | ' . $datosnum['ref_customer'] . ' - ' . $datosnum['booking'] . ' - Contenedor SIN CONFIRMAR';
        }

        $this->subject($subject);
        $this->datos = $datosnum;



    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    
    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function build()
    {
        
        return $this->view('mails.puntoDeInteres');

    }

}
