<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositoRetiro extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'address',
        'country',
        'city',
        'km_from_town',
        'latitud',
        'longitud',
        'link_maps',
        'user',
        'empresa',
    ];
}
