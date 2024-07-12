<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoDeInteres extends Model
{
    use HasFactory;
    protected $fillable = ['descripcion', 'latitud', 'longitud', 'accion_mail', 'accion_notificacion', 'accion_status', 'rango_aviso','user'];

    public function itinerario()
    {
        return $this->belongsTo('App\Itinerario');
    }

}
