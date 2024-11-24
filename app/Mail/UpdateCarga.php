<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpdateCarga extends Mailable
{
    use Queueable, SerializesModels;
    public $modificacionesCntr;
    public $modificacionesCarga;
    public $carga;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($modificacionesCntr, $modificacionesCarga, $carga)
    {
        $this->modificacionesCntr = $modificacionesCntr;
        $this->modificacionesCarga = $modificacionesCarga;
        $this->carga = $carga;
        $this->subject = 'MODIFICACION DE CARGA// '.$carga['ref_customer'] .' // BKG: '. $carga['booking'];
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

        $modificacionesCntr = $this->modificacionesCntr;
        $modificacionesCarga = $this->modificacionesCarga;
        $carga = $this->carga;
        return $this->view('mails.UpdateCarga');
       //esto busca en la carpeta public.    
    }
}
