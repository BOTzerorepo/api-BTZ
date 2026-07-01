<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cotizacion extends Model
{
    use SoftDeletes;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'numero', 'cliente_id', 'comercial_id', 'combo_id',
        'fecha_creacion', 'fecha_vigencia', 'estado', 'total_usd', 'notas',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteComercial::class, 'cliente_id');
    }

    public function comercial()
    {
        return $this->belongsTo(User::class, 'comercial_id');
    }

    public function combo()
    {
        return $this->belongsTo(TarifarioCombo::class, 'combo_id');
    }

    public function items()
    {
        return $this->hasMany(CotizacionItem::class, 'cotizacion_id');
    }
}
