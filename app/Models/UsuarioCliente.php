<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioCliente extends Model
{
    protected $table = 'usuarios_cliente';

    protected $fillable = [
        'sucursal_id', 'nombre', 'email', 'rol', 'notif_email', 'notif_sistema',
    ];

    public function sucursal()
    {
        return $this->belongsTo(SucursalCliente::class, 'sucursal_id');
    }
}
