<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProximaAccion extends Model
{
    protected $table = 'proximas_acciones';

    protected $fillable = [
        'cliente_id', 'fecha', 'tipo', 'descripcion', 'responsable', 'completada',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteComercial::class, 'cliente_id');
    }
}
