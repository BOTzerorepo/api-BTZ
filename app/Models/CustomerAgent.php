<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAgent extends Model
{
    use HasFactory;
    protected $fillable = [
        'razon_social',
        'tax_id',
        'pais',
        'provincia',
        'mail',
        'phone',
        'user',
        'empresa',
    ];
}
