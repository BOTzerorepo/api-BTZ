<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OportunidadDiagnostico extends Model
{
    protected $table = 'oportunidades_diagnostico';

    protected $fillable = ['diagnostico_id', 'descripcion', 'impacto', 'funcionalidad'];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }
}
