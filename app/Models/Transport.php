<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Transport extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'transports';

    protected $fillable = [
        'razon_social',
        'CUIT',
        'direccion',
        'pais',
        'provincia',
        'satelital',
        'paut',
        'permiso',
        'vto_permiso',
        'contacto_logistica_nombre',
        'contacto_logistica_celular',
        'contacto_logistica_mail',
        'contacto_admin_nombre',
        'contacto_admin_celular',
        'contacto_admin_mail',
        'user',
        'empresa',
        'observation',
    ];

    public function fleteros()
    {
        return $this->belongsToMany(Fletero::class, 'transport_fletero');
    }
}
