<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerario extends Model
{
    use HasFactory;
    
    protected $fillable = ['unidad_asignada', 'carga_id', 'descarga_id', 'trip_id', 'user'];


    public function carga()
    {
        return $this->belongsTo('App\Models\CustomerLoadPlace', 'carga_id');
    }

    public function descarga()
    {
        return $this->belongsTo('App\Models\CustomerUnloadPlace', 'descarga_id');
    }

    public function puntosDeInteres()
    {
        return $this->hasMany('App\Models\PuntoDeInteres');
    }
}
