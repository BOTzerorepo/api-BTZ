<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;


    public $token;
    public $ruta;


    public function __construct($token)
    {
        $this->token = $token;

    }

    public function build()
    {

        $frontendUrl = env('FRONT_URL','https://totaltrade.btz.ar' ); // valor por defecto
        $url = "{$frontendUrl}/reset-pass.php?token={$this->token}";
        return $this->subject('Recuperar contraseña')
            ->view('mails.reset-password')
            ->with(['url' => $url]);
    }


    public function envelope()
    {
        return new Envelope(
            subject: 'Reset Password Mail',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
  

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
