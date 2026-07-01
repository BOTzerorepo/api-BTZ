<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioClienteAcceso extends Model
{
    protected $table = 'usuario_cliente_accesos';

    protected $fillable = [
        'cliente_comercial_id',
        'user_id',
        'ver_precios',
        'ver_documentos',
        'ver_tracking',
        'ver_cargas_internas',
        'notif_email',
        'notif_nuevas_cargas',
        'notif_cambio_estado',
        'columnas_visibles',
        'notas',
    ];

    protected $casts = [
        'ver_precios'          => 'boolean',
        'ver_documentos'       => 'boolean',
        'ver_tracking'         => 'boolean',
        'ver_cargas_internas'  => 'boolean',
        'notif_email'          => 'boolean',
        'notif_nuevas_cargas'  => 'boolean',
        'notif_cambio_estado'  => 'boolean',
        'columnas_visibles'    => 'array',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteComercial::class, 'cliente_comercial_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
