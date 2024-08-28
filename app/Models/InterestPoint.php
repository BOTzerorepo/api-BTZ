<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestPoint extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'latitude',
        'longitude',
        'radius',
        'trigger_on',
        'status_on_trigger',
        'is_general'
    ];

    public function cntrs()
    {
        return $this->belongsToMany(Cntr::class, 'cntr_interest_point')
                    ->withPivot('order');
    }
}
