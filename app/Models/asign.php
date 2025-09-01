<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class asign extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $table = 'asign';

    protected $fillable = [
        'driver',
        'cntr_number',
        'booking',
        'truck',
        'truck_semi',
        'transport',
        'transport_agent',
        'crt',
        'fletero_razon_social',
        'fletero_cuit',
        'fletero_domicilio',
        'fletero_paut',
        'fletero_permiso',
        'fletero_vto_permiso',
        'observation_load',
        'file_instruction',
        'updated_at',
        'created_at',
        'user',
        'company',
        'agent_port',
        'sub_empresa',
    ];
}
