<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class razonSocial extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'razon_social','cuit','direccion','provincia','pais','paut','permiso','vto_permiso'];

}
