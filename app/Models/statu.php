<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class statu extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
        'main_status',
        'cntr_number',
        'user_status',
 
    ];
}
