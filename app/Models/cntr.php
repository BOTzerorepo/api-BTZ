<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class cntr extends Model implements AuditableContract
{
    use HasFactory, Auditable;
    protected $table = 'cntr';
    protected $primaryKey = 'id_cntr';
}
