<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'aker' => [
        // Endpoint v2 para posición puntual (issetDominio, serviceSatelital, flotaId, etc.)
        'url'       => env('AKER_API_URL', 'https://app.akercontrol.com/ws/v2/servicios'),

        // Base del endpoint de flota completa (flota, flotaTransport, flotaCliente)
        // URL final = {flota_url}/{phone}/{code}
        'flota_url' => env('AKER_FLOTA_URL', 'https://app.akercontrol.com/ws/flota'),

        'code'      => env('AKER_API_CODE', 'E6HW19'),   // E6HW19 (el que tiene datos)
        'phone'     => env('AKER_PHONE', '2612128105'),      // 2612128105
        'cercania'  => env('AKER_CERCANIA', true),
        'domicilio' => env('AKER_DOMICILIO', false),
    ],

];
