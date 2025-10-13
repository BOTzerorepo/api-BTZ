<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Arr;

class EmailRecipientsService
{
    /** Normaliza, valida y deduplica una lista (string o array) de emails */
    public function parseList(string|array|null $value): array
    {
        if (empty($value)) return [];

        // Si viene array, lo paso a string
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        // Normalizo separadores: ; \n \r \t -> ,
        $normalized = str_replace([';', "\n", "\r", "\t"], ',', $value);

        // Split + trim + filtro válidos
        $arr = array_filter(
            array_map('trim', explode(',', $normalized)),
            fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)
        );

        // Dedup + reindex
        return array_values(array_unique($arr));
    }

    /**
     * Construye TO/CC/BCC combinando:
     * - email y cc_emails del usuario
     * - toExtras / ccExtras externos (opcionales)
     * - bcc (string|array|nulo)
     */
    public function buildRecipients(
        ?string $userEmail,
        ?string $userCcEmails,
        string|array|null $toExtras = null,
        string|array|null $ccExtras = null,
        string|array|null $bcc = null
    ): array {
        $to = $this->parseList(array_filter([$userEmail]));
        $to = array_values(array_unique(array_merge(
            $to,
            $this->parseList($toExtras)
        )));

        $cc = array_values(array_unique(array_merge(
            $this->parseList($userCcEmails),
            $this->parseList($ccExtras)
        )));

        $bcc = $this->parseList($bcc);

        return [$to, $cc, $bcc];
    }

    /** Envía un Mailable con TO/CC/BCC (omite CC/BCC si están vacíos) */
    public function sendWithRecipients(array $to, array $cc, array $bcc, \Illuminate\Mail\Mailable $mailable): void
    {
        $pending = Mail::to($to);
        if (!empty($cc))  { $pending->cc($cc); }
        if (!empty($bcc)) { $pending->bcc($bcc); }
        $pending->send($mailable);
    }
}
