<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'registered_name',
        'tax_id',
        'contact_name',
        'contact_phone',
        'contact_mail'
    ];
}
