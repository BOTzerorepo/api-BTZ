<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteComercial extends Model
{
    use SoftDeletes;

    protected $table = 'clientes_comercial';

    protected $fillable = [
        'empresa', 'razon_social', 'cuit', 'industria', 'segmento', 'estado',
        'fecha_alta', 'contacto_nombre', 'contacto_email', 'contacto_telefono',
        'contacto_cargo', 'direccion', 'notas',
    ];

    public function acciones()
    {
        return $this->hasMany(AccionComercial::class, 'cliente_id');
    }

    public function proximasAcciones()
    {
        return $this->hasMany(ProximaAccion::class, 'cliente_id');
    }

    public function sucursales()
    {
        return $this->hasMany(SucursalCliente::class, 'cliente_id');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'cliente_id');
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'cliente_id');
    }

    public function insights()
    {
        return $this->hasMany(Insight::class, 'cliente_id');
    }
}
