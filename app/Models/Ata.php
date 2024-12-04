<?php

// cambiamos

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ata extends Model
{
    use HasFactory;
    protected $fillable = [
        'razon_social',
        'tax_id',
        'provincia',
        'phone',
        'pais',
        'mail',
        'user',
        'empresa',
    ];
}
