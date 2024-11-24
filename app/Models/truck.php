<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class truck extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'model',
        'chasis',
        'poliza',
        'vto_poliza',
        'type',
        'domain',
        'year',
        'device_truck',
        'satelital_location',
        'transport_id',
        'user',
        'customer_id',
        'fletero_id' // Añadido fletero_id
    ];

    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    public function fletero()
    {
        return $this->belongsTo(Fletero::class);
    }
}
