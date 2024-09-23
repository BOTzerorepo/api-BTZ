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
    public $contenedor;
    public $puntoDeInteres;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contenedor, $puntoDeInteres )
    {
        
        $this->contenedor = $contenedor;
        $this->puntoDeInteres = $puntoDeInteres;


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
