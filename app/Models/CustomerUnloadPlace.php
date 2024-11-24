<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerUnloadPlace extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'address',
        'city',
        'country',
        'km_from_town',
        'remarks',
        'latitud',
        'longitud',
        'link_maps',
        'user',
        'company'
    ];
}
