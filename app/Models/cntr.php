<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class cntr extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $table = 'cntr';
    protected $primaryKey = 'id_cntr';

    protected $fillable = [
        'id_cntr ',
        'booking',
        'cntr_number',
        'cntr_seal',       
        'cntr_type',
        'confirmacion',
        'net_weight',
        'retiro_place',
        'set_',
        'set_humidity',
        'set_vent',
        'document_invoice',
        'document_packing',
        'user_cntr',
        'company',
        'status_cntr',
        'main_status',
        'in_usd',
        'company_invoice_out',
        'modo_pago_in',
        'plazo_de_pago_in',
        'out_usd',
        'observation_out',
        'plazo_de_pago_out',
        'modo_pago_out',
        'interchange',
        'cntr_crt',
        'cntr_micdta',
        'profit',
        'calificacion_carga',
        'feedback_customer',
    ];
}
