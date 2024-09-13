<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fletero extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'razon_social',
        'logo',
        'satelital',
        'alta_aker',
        'CUIT',
        'direccion',
        'provincia',
        'pais',
        'paut',
        'permiso',
        'vto_permiso',
        'observation',
    ];

    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'transport_fletero');
    }

    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }
}
