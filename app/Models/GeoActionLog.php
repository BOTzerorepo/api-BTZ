<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoActionLog extends Model
{
    protected $table = 'geo_action_logs';

    protected $fillable = [
        'trip_id','cntr_number','domain',
        'action_type','point_type',
        'distance_m','threshold_m',
        'event_lat','event_lng',
        'position_lat','position_lng',
        'status_at_moment','aker_time','meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'aker_time' => 'datetime',
    ];
}