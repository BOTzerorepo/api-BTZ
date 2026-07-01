<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnosticos';

    protected $appends = ['cliente_nombre'];

    protected $fillable = [
        'cliente_id', 'nombre', 'fecha', 'estado',
        'prep_comercial', 'prep_cant_operaciones', 'prep_tipo_carga', 'prep_modulos_habilitados',
        'prep_estadisticas_uso', 'prep_documentos_disponibles', 'prep_problemas_conocidos',
        'reunion_quien_usa', 'reunion_que_info_necesita', 'reunion_que_valor_encuentra',
        'reunion_que_no_usa', 'reunion_que_le_falta',
        'uso_frecuencia', 'valores_encontrados', 'barreras', 'funcionalidades_pedidas',
    ];

    protected $casts = [
        'prep_tipo_carga'          => 'array',
        'prep_modulos_habilitados' => 'array',
        'valores_encontrados'      => 'array',
        'barreras'                 => 'array',
        'funcionalidades_pedidas'  => 'array',
        'uso_frecuencia'           => 'integer',
        'fecha'                    => 'date:Y-m-d',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteComercial::class, 'cliente_id');
    }

    public function hallazgos()
    {
        return $this->hasMany(HallazgoDiagnostico::class, 'diagnostico_id');
    }

    public function oportunidades()
    {
        return $this->hasMany(OportunidadDiagnostico::class, 'diagnostico_id');
    }

    public function acciones()
    {
        return $this->hasMany(AccionDiagnostico::class, 'diagnostico_id');
    }

    public function getClienteNombreAttribute()
    {
        return $this->cliente->razon_social ?? null;
    }
}
