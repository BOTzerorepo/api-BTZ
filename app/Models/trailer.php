<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class trailer extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    public function fletero()
    {
        return $this->belongsTo(Fletero::class);
    }

    public function transporte()
    {
        return $this->belongsTo(Transport::class);
    }
}
