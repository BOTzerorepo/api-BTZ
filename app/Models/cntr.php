<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cntr extends Model
{
    use HasFactory;
    protected $table = 'cntr';
    protected $primaryKey = 'id_cntr';
}
