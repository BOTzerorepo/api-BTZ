<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarifarioItem extends Model
{
    use SoftDeletes;

    protected $table = 'tarifario_items';

    protected $fillable = [
        'empresa', 'origen', 'destino', 'tipo_servicio', 'tipo_cntr',
        'moneda', 'tarifa', 'descripcion', 'vigencia_desde', 'vigencia_hasta',
    ];

    public function combos()
    {
        return $this->belongsToMany(TarifarioCombo::class, 'tarifario_combo_items', 'item_id', 'combo_id');
    }
}
