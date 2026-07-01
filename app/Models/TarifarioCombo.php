<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarifarioCombo extends Model
{
    use SoftDeletes;

    protected $table = 'tarifario_combos';

    protected $fillable = [
        'empresa', 'nombre', 'descripcion', 'precio_combo',
        'moneda', 'vigencia_desde', 'vigencia_hasta',
    ];

    public function items()
    {
        return $this->belongsToMany(TarifarioItem::class, 'tarifario_combo_items', 'combo_id', 'item_id');
    }
}
