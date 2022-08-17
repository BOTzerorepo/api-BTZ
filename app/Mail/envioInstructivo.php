<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class envioInstructivo extends Mailable
{
    use Queueable, SerializesModels;

    public $datos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos)
    {
        $this->datos = $datos;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $datos = $this->datos;
        return $this->view('mails.CargaEnvioInstructivo')
        ->subject('Instructivo de Viaje.'.'[ '.$datos['cntr_number'].' ]')
        ->attach(public_path() . '/' .'instructivos/'.$datos['booking'].'/'.$datos['cntr_number'].'/'.'instructivo_'.$datos['booking'].'_'.$datos['cntr_number'].'.pdf');
       //esto busca en la carpeta public.    
    }
}
