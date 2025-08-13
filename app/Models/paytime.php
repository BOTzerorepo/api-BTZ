<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayTime extends Model
{
    use HasFactory;
    protected $table = 'pay_times';
    protected $fillable = [
        'title',
        'description',
        'user',
        'empresa',
    ];
}
