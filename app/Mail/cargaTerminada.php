<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class cargaTerminada extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $datos;
    public $archivoAdjunto;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos, $archivoAdjunto)
    {
        $this->datos = $datos;
        if ($archivoAdjunto == null) {
            $this->archivoAdjunto = $archivoAdjunto;
        } else {
            $this->archivoAdjunto = '/app' . '/' . $archivoAdjunto;
        }
        $this->subject = 'CARGAR FINALIZADA // ' . $datos['ref_customer'] . ' - ' . $datos['type'] . ' - ' . $datos['trader'] . ' - 1 * ' . $datos['cntr_type'] . '// BKG: ' . $datos['booking'] . '.';

    }
    public function build()
    {
        if ($this->archivoAdjunto == null) {
            return $this->view('mails.CargaTerminada');
        } else {
            return $this->view('mails.CargaTerminada')
            ->attach(storage_path($this->archivoAdjunto), [
                'as' => 'Documentacion Status', // Nombre personalizado del archivo adjunto
            ]);
        }
    }



}
