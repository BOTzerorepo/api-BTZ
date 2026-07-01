<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionItem extends Model
{
    protected $table = 'cotizacion_items';

    protected $fillable = [
        'cotizacion_id', 'tarifario_item_id', 'descripcion',
        'origen', 'destino', 'tipo_servicio', 'tipo_cntr', 'moneda', 'tarifa',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function tarifarioItem()
    {
        return $this->belongsTo(TarifarioItem::class, 'tarifario_item_id');
    }
}
