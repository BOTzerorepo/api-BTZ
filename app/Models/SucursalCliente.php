<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SucursalCliente extends Model
{
    protected $table = 'sucursales_cliente';

    protected $fillable = [
        'cliente_id', 'nombre', 'direccion',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteComercial::class, 'cliente_id');
    }

    public function usuarios()
    {
        return $this->hasMany(UsuarioCliente::class, 'sucursal_id');
    }
}
