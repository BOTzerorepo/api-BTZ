<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'razon_social',
        'tax_id',
        'puerto',
        'contact_phone',
        'contact_name',
        'contact_mail',
        'user',
        'empresa',
        'observation_gral',
    ];
}
