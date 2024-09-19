<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class InterestPoint extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = [
        'type',
        'description',
        'latitude',
        'longitude',
        'radius',
        'accion_correo_customer_entrada',
        'accion_correo_cliente_entrada',
        'accion_cambiar_status_entrada',
        'accion_notificacion_customer_entrada',
        'accion_notificacion_cliente_entrada',
        'accion_correo_customer_salida',
        'accion_correo_cliente_salida',
        'accion_cambiar_status_salida',
        'accion_notificacion_customer_salida',
        'accion_notificacion_cliente_salida'
    ];    

    public function cntrs()
    {
        return $this->belongsToMany(Cntr::class, 'cntr_interest_point')
                    ->withPivot('order');
    }
}
