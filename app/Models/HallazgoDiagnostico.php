<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HallazgoDiagnostico extends Model
{
    protected $table = 'hallazgos_diagnostico';

    protected $fillable = ['diagnostico_id', 'descripcion', 'impacto'];

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }
}
