<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionDiagnostico extends Model
{
    protected $table = 'acciones_diagnostico';

    protected $fillable = [
        'diagnostico_id', 'descripcion', 'tipo', 'responsable',
        'fecha_limite', 'estado', 'comentarios',
    ];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }
}
