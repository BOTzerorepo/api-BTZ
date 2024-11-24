<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerShipper extends Model
{
    use HasFactory;
    protected $fillable = [
        'razon_social',
        'tax_id',
        'address',
        'city',
        'country',
        'postal_code',
        'create_user',
        'company',
        'remarks'
    ];
}
