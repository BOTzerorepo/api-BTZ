<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carga extends Model
{
    use HasFactory;
    protected $table = 'carga';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'booking',
        'bl_hbl',
        'shipper',
        'commodity',
        'load_place',
        'trader',
        'importador',
        'load_date',
        'unload_place',
        'cut_off_fis',
        'cut_off_doc',
        'oceans_line',
        'vessel',
        'voyage',
        'final_point',
        'ETA',
        'ETD',
        'consignee',
        'notify',
        'custom_place',
        'custom_agent',
        'custom_place_impo',
        'custom_agent_impo',
        'ref_customer',
        'senasa',
        'senasa_string',
        'tara',
        'tara_string',
        'referencia_carga',
        'comercial_reference',
        'observation_customer',
        'tarifa_ref',
        'user',
        'empresa',
        'status',
        'big_state',
        'confirm_date',
        'ex_alto',
        'ex_ancho',
        'ex_largo',
        'obs_imo',
        'rf_tem',
        'rf_humedad',
        'rf_venti',
        'document_bookingConf',
        'created_at',
        'type',
    ];
}
