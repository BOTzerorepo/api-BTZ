<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeofencingEvent extends Model
{
    protected $fillable = [
        'cntr_number',
        'truck_plate',
        'lat',
        'lon',
        'zone_type',
        'event_type',
        'entered_at',
        'exited_at',
        'duration_minutes',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'entered_at' => 'datetime',
        'exited_at' => 'datetime'
    ];
}
