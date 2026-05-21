<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;

class User extends Authenticatable implements AuditableContract, JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, Auditable, HasRoles;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'empresa',
        'pass',
        'celular',
        'name',
        'last_name',
        'cc_emails',
        'cliente_id',
        'transport_id',
        'last_login_at',
    ];

    protected $hidden = [
        'pass',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
    ];

    // Excluir last_login_at del tracking de OwenIt/Auditing
    // (cada login haría un registro 'updated' innecesario en audits)
    protected $auditExclude = [
        'last_login_at',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Retorna la clave primaria del modelo
    }

    public function getJWTCustomClaims()
    {
        return []; // Aquí puedes agregar reclamaciones personalizadas si lo deseas
    }
    public function getAuthPassword()
    {
        return $this->pass; // Cambia 'password' a 'pass'
    }
}
