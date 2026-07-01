<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insight extends Model
{
    protected $table = 'insights';

    protected $appends = ['cliente_nombre'];

    protected $fillable = [
        'empresa', 'cliente_id', 'tipo', 'descripcion', 'impacto',
        'repetido_por', 'relacionado_con',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteComercial::class, 'cliente_id');
    }

    public function getClienteNombreAttribute()
    {
        return $this->cliente->razon_social ?? null;
    }
}
