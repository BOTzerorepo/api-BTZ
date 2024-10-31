<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    use HasFactory;
    protected $table = 'notification';

    protected $fillable = [
        'title',
        'description',
        'user_to',
        'Created_at',
        'status',
        'sta_carga',
        'user_create',
        'company_create',
        'cntr_number',
        'booking',       
    ];
}
