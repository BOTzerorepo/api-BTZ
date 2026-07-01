<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccionComercial extends Model
{
    protected $table = 'acciones_comerciales';

    protected $fillable = [
        'cliente_id', 'fecha', 'tipo', 'descripcion', 'resultado',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteComercial::class, 'cliente_id');
    }
}
