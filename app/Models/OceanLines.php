<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OceanLines extends Model
{
    use HasFactory;
    protected $table = 'ocean_lines';
    protected $fillable = [
        'razon_social',
        'pais',
        'tax_id',
        'user',
        'empresa',
        'andress',
        'mail',
    ];
}
