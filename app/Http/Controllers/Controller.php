<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function carpetaIntructivos($booking, $trip){

        if (!file_exists('instructivos/' . $booking)) {

            /* Si no Existe la Carperta Del booking */

            mkdir('instructivos/' . $booking, 0777, true);

            /* Si existe la Carpeta del Contenedor dentro de la Carpeta del Booking la Asignamos*/
            if (file_exists('instructivos/' . $booking . '/' . $trip)) {

            } else {

                /* Si no existe la Carpeta del Contenedor dentro de la Carpeta del Booking la creamos y  la Asignamos*/


                mkdir('instructivos/' . $booking . '/' . $trip, 0777, true);
            }
        } else {

            /* si Ya existe la Carpeta Booking */

            if (file_exists('instructivos/' . $booking . '/' . $trip)) {
                /* y existe la carpeta de CNTR la asignamos */

            } else {

                /* y si no existe la carpeta de CNTR la creamos y la asignamos */

                mkdir('instructivos/' . $booking . '/' . $trip, 0777, true);
            }
        }
    }
}
