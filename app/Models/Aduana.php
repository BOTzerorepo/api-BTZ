<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aduana extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'address',
        'provincia',
        'pais',
        'lat',
        'lon',
        'user',
        'km_from_town',
        'link_map',
    ];

}
