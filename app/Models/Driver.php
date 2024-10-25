<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'drivers';
    protected $fillable = [
        'nombre',
        'foto',
        'documento',
        'vto_carnet',
        'WhatsApp',
        'mail',
        'user',
        'empresa',
        'transporte',
        'fletero_id',
        'transport_id',
        'Observaciones',
    ];

    public function fletero()
    {
        return $this->belongsTo(Fletero::class);
    }

    public function transporte()
    {
        return $this->belongsTo(Transport::class);
    }
}
