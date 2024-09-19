<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ServiceSatelital;

class RevisarCoordenadas extends Command
{
    protected $signature = 'revisar:coordenadas';
    protected $description = 'Revisa las coordenadas de los camiones y puntos de interés cada 10 minutos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Ejecuta la función del controlador
        $controlador = new ServiceSatelital();
        $controlador->revisarCoordenadas();

        $this->info('Coordenadas revisadas correctamente.');
    }
}
