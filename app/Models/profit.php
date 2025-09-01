<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class profit extends Model
{
    use HasFactory;
    protected $table = 'profit';
    protected $fillable = [
        'cntr_number',
        'in_usd',
        'in_razon_social',
        'in_detalle',
        'out_usd',
        'out_razon_social',
        'out_detalle',
        'user'
    ];
}
